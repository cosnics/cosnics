<?php
namespace Chamilo\Core\Repository\Feedback\Component;

use Chamilo\Core\Repository\Feedback\FeedbackNotificationSupport;
use Chamilo\Core\Repository\Feedback\Form\FeedbackForm;
use Chamilo\Core\Repository\Feedback\Manager;
use Chamilo\Core\Repository\Feedback\Storage\DataClass\Feedback;
use Chamilo\Core\Repository\Feedback\Storage\DataClass\Notification;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\DatetimeUtilities;
use Chamilo\Libraries\Utilities\Utilities;
use Chamilo\Libraries\Format\Table\Pager;
use Chamilo\Libraries\Format\Table\PagerRenderer;

class BrowserComponent extends Manager implements DelegateComponent
{
    const PARAM_COUNT = 'feedback_count';
    const PARAM_PAGE_NUMBER = 'feedback_page_nr';

    /**
     * Executes this controller
     */
    public function run()
    {
        if (! $this->get_parent()->is_allowed_to_view_feedback())
        {
            throw new NotAllowedException();
        }

        $form = new FeedbackForm($this, $this->get_url());

        if ($form->validate())
        {
            if (! $this->get_parent()->is_allowed_to_create_feedback())
            {
                throw new NotAllowedException();
            }

            $values = $form->exportValues();

            // Create the feedback
            $feedback = $this->get_parent()->get_feedback();

            $feedback->set_user_id($this->get_user_id());
            $feedback->set_comment($values[Feedback :: PROPERTY_COMMENT]);
            $feedback->set_creation_date(time());
            $feedback->set_modification_date(time());

            $success = $feedback->create();

            if ($this->get_parent() instanceof FeedbackNotificationSupport)
            {
                if ($success && $this->get_parent()->is_allowed_to_view_feedback())
                {
                    $notification_requested = isset($values[FeedbackForm :: PROPERTY_NOTIFICATIONS]) ? true : false;
                    $notification = $this->get_parent()->retrieve_notification();

                    if ($notification instanceof Notification && ! $notification_requested)
                    {
                        $success = $notification->delete();
                    }
                    elseif ($notification instanceof Notification && $notification_requested)
                    {
                        $notification->set_modification_date(time());

                        $success = $notification->update();
                    }
                    elseif (! $notification instanceof Notification && $notification_requested)
                    {
                        $notification = $this->get_parent()->get_notification();

                        $notification->set_user_id($this->get_user_id());
                        $notification->set_creation_date(time());
                        $notification->set_modification_date(time());

                        $success = $notification->create();
                    }
                }
            }

            $this->redirect(
                Translation :: get(
                    $success ? 'ObjectCreated' : 'ObjectNotCreated',
                    array('OBJECT' => Translation :: get('Feedback')),
                    Utilities :: COMMON_LIBRARIES),
                ! $success);
        }
        else

        {
            $html = array();

            $feedbacks = $this->get_parent()->retrieve_feedbacks(
                $this->getPager()->getNumberOfItemsPerPage(),
                $this->getPager()->getCurrentRangeOffset());

            if ($feedbacks->size() == 0 && ! $this->get_parent()->is_allowed_to_create_feedback())
            {
                $html[] = '<div class="alert alert-info">';
                $html[] = Translation :: get('NoFeedbackYet');
                $html[] = '</div>';
            }

            if ($feedbacks->size() > 0)
            {
                $html[] = '<h3>';
                $html[] = Translation :: get('Feedback');
                $html[] = '</h3>';

                $html[] = '<div class="panel panel-default panel-feedback">';
                $html[] = '<div class="list-group">';

                while ($feedback = $feedbacks->next_result())
                {
                    $html[] = '<div class="list-group-item">';

                    $profilePhotoUrl = new Redirect(
                        array(
                            Application :: PARAM_CONTEXT => \Chamilo\Core\User\Ajax\Manager :: context(),
                            Application :: PARAM_ACTION => \Chamilo\Core\User\Ajax\Manager :: ACTION_USER_PICTURE,
                            \Chamilo\Core\User\Manager :: PARAM_USER_USER_ID => $feedback->get_user()->get_id()));

                    $html[] = '<img class="panel-feedback-profile pull-left" src="' . $profilePhotoUrl->getUrl() . '" />';

                    $html[] = '<div class="pull-right">';

                    if ($this->get_parent()->is_allowed_to_update_feedback($feedback))
                    {
                        $html[] = $this->render_update_action($feedback);
                    }

                    if ($this->get_parent()->is_allowed_to_delete_feedback($feedback))
                    {
                        $html[] = $this->render_delete_action($feedback);
                    }

                    $html[] = '</div>';

                    $html[] = '<h4 class="list-group-item-heading">' . $feedback->get_user()->get_fullname() .
                         ' <small>(' . $this->format_date($feedback->get_creation_date()) . ')</small></h4>';
                    $html[] = '<p class="list-group-item-text">' . $feedback->get_comment() . '</p>';

                    $html[] = '</div>';
                }

                $html[] = '</div>';
                $html[] = '</div>';

                if ($this->get_parent()->count_feedbacks() > $feedbacks->size())
                {
                    $html[] = '<div class="row">';
                    $html[] = '<div class="col-xs-12 feedback-pagination">';
                    $html[] = $this->getPagerRenderer()->renderPaginationWithPageLimit(
                        $this->get_parameters(),
                        self :: PARAM_PAGE_NUMBER);
                    $html[] = '</div>';
                    $html[] = '</div>';
                }
            }

            if ($this->get_parent()->is_allowed_to_create_feedback())
            {
                $html[] = '<h3>';
                $html[] = Translation :: get('AddFeedback');
                $html[] = '</h3>';

                $html[] = $form->toHtml();
            }

            return implode(PHP_EOL, $html);
        }
    }

    /**
     *
     * @param \core\repository\content_object\portfolio\feedback\Feedback $feedback
     * @return string
     */
    public function render_delete_action($feedback_publication)
    {
        $delete_url = $this->get_url(
            array(
                Manager :: PARAM_ACTION => Manager :: ACTION_DELETE,
                Manager :: PARAM_FEEDBACK_ID => $feedback_publication->get_id()));

        $title = Translation :: get('Delete', null, Utilities :: COMMON_LIBRARIES);

        $delete_link = '<a href="' . $delete_url . '" onclick="return confirm(\'' .
             addslashes(Translation :: get('Confirm', null, Utilities :: COMMON_LIBRARIES)) . '\');"><img src="' .
             Theme :: getInstance()->getCommonImagePath('Action/Delete') . '"  alt="' . $title . '" title="' . $title .
             '"/></a>';

        return $delete_link;
    }

    /**
     *
     * @param \core\repository\content_object\portfolio\feedback\Feedback $feedback
     * @return string
     */
    public function render_update_action($feedback_publication)
    {
        $update_url = $this->get_url(
            array(
                Manager :: PARAM_ACTION => Manager :: ACTION_UPDATE,
                Manager :: PARAM_FEEDBACK_ID => $feedback_publication->get_id()));

        $title = Translation :: get('Edit', null, Utilities :: COMMON_LIBRARIES);
        $update_link = '<a href="' . $update_url . '"><img src="' .
             Theme :: getInstance()->getCommonImagePath('Action/Edit') . '"  alt="' . $title . '" title="' . $title .
             '"/></a>';

        return $update_link;
    }

    /**
     *
     * @param int $date
     * @return string
     */
    public function format_date($date)
    {
        $date_format = Translation :: get('DateTimeFormatLong', null, Utilities :: COMMON_LIBRARIES);
        return DatetimeUtilities :: format_locale_date($date_format, $date);
    }

    /**
     *
     * @return integer
     */
    public function getCount()
    {
        return $this->getRequest()->query->get(self :: PARAM_COUNT, 5);
    }

    /**
     *
     * @return integer
     */
    public function getPageNumber()
    {
        return $this->getRequest()->query->get(self :: PARAM_PAGE_NUMBER, 1);
    }

    /**
     *
     * @return \Chamilo\Libraries\Format\Table\Pager
     */
    public function getPager()
    {
        if (is_null($this->pager))
        {
            $this->pager = new Pager(
                $this->getCount(),
                1,
                $this->get_parent()->count_feedbacks(),
                $this->getPageNumber());
        }

        return $this->pager;
    }

    /**
     *
     * @return \Chamilo\Libraries\Format\Table\PagerRenderer
     */
    public function getPagerRenderer()
    {
        if (is_null($this->pagerRenderer))
        {
            $this->pagerRenderer = new PagerRenderer($this->getPager());
        }

        return $this->pagerRenderer;
    }
}
