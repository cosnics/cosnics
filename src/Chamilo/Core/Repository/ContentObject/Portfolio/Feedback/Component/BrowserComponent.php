<?php
namespace Chamilo\Core\Repository\ContentObject\Portfolio\Feedback\Component;

use Chamilo\Core\Repository\ContentObject\Portfolio\Feedback\Form\FeedbackForm;
use Chamilo\Core\Repository\ContentObject\Portfolio\Feedback\Manager;
use Chamilo\Core\Repository\ContentObject\Portfolio\Feedback\Storage\DataClass\AbstractFeedback;
use Chamilo\Core\Repository\ContentObject\Portfolio\Feedback\Storage\DataClass\AbstractNotification;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Display;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Format\Utilities\ResourceManager;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\DatetimeUtilities;
use Chamilo\Libraries\Utilities\Utilities;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;

/**
 * Render a list of feedback
 *
 * @package repository\content_object\portfolio\feedback
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class BrowserComponent extends Manager implements DelegateComponent
{

    /**
     * Executes this component
     */
    public function run()
    {
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
            $feedback->set_comment($values[AbstractFeedback :: PROPERTY_COMMENT]);
            $feedback->set_creation_date(time());
            $feedback->set_modification_date(time());

            $success = $feedback->create();

            if ($success && $this->get_parent()->is_allowed_to_view_feedback())
            {
                $notification_requested = isset($values[FeedbackForm :: PROPERTY_NOTIFICATIONS]) ? true : false;

                $notification = $this->get_parent()->retrieve_notification();

                if ($notification instanceof AbstractNotification && ! $notification_requested)
                {
                    $success = $notification->delete();
                }
                elseif ($notification instanceof AbstractNotification && $notification_requested)
                {
                    $notification->set_modification_date(time());

                    $success = $notification->update();
                }
                elseif (! $notification instanceof AbstractNotification && $notification_requested)
                {
                    $notification = $this->get_parent()->get_notification();
                    $notification->set_user_id($this->get_user_id());
                    $notification->set_creation_date(time());
                    $notification->set_modification_date(time());

                    $success = $notification->create();
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

            $feedbacks = $this->get_parent()->retrieve_feedbacks();

            if ($feedbacks->size() == 0 && ! $this->get_parent()->is_allowed_to_create_feedback())
            {
                $html[] = Display :: normal_message(Translation :: get('NoFeedbackYet'), true);
            }

            while ($feedback = $feedbacks->next_result())
            {
                $html[] = '<div class="feedback-container ' .
                     ($feedbacks->current() % 2 == 0 ? 'feedback-container-odd' : 'feedback-container-even') . '">';
                $html[] = '<div class="feedback">';

                $html[] = '<div class="body">';
                $html[] = '<div class="content">';

                $html[] = '<span class="user">';
                $html[] = $feedback->get_user()->get_fullname();
                $html[] = '</span> ';
                $html[] = $feedback->get_comment();

                $html[] = '<div class="date">';
                $html[] = $this->format_date($feedback->get_creation_date());
                $html[] = '</div>';

                $html[] = '</div>';
                $html[] = '</div>';

                $html[] = '<div class="photo">';

                $profilePhotoUrl = new Redirect(
                    array(
                        Application :: PARAM_CONTEXT => \Chamilo\Core\User\Ajax\Manager :: context(),
                        Application :: PARAM_ACTION => \Chamilo\Core\User\Ajax\Manager :: ACTION_USER_PICTURE,
                        \Chamilo\Core\User\Manager :: PARAM_USER_USER_ID => $feedback->get_user()->get_id()));

                $html[] = '<img style="width: 32px;" src="' . $profilePhotoUrl->getUrl() . '" />';
                $html[] = '</div>';

                $html[] = '<div class="actions">';

                if ($this->get_parent()->is_allowed_to_update_feedback($feedback))
                {
                    $html[] = $this->render_update_action($feedback);
                }

                if ($this->get_parent()->is_allowed_to_delete_feedback($feedback))
                {
                    $html[] = $this->render_delete_action($feedback);
                }

                $html[] = '</div>';
                $html[] = '</div>';
                $html[] = '<div class="clear"></div>';
                $html[] = '</div>';
                $html[] = '<div class="clear"></div>';

                // TODO: This visual fix should be replaced with a visual and logic fix, preventing the retrieval of
                // all
                // feedback, limiting it to the first three and retrieving the rest via AJAX if and when requested
                if ($feedbacks->size() > 3 && $feedbacks->current() == 3)
                {
                    $html[] = '<div class="feedback-history">';
                }

                if ($feedbacks->size() > 3 && $feedbacks->current() == $feedbacks->size())
                {
                    $html[] = '</div>';

                    $html[] = '<div class="feedback-container feedback-container-odd feedback-history-toggle">';
                    $html[] = '<div class="feedback">';

                    $html[] = '<div class="body">';
                    $html[] = '<div class="content">';
                    $html[] = '<span class="feedback-history-toggle-visible">' .
                         Translation :: get('ViewPreviousComments') . '</span>';
                    $html[] = '<span class="feedback-history-toggle-invisible">' . Translation :: get(
                        'HidePreviousComments') . '</span>';

                    $html[] = '</div>';
                    $html[] = '</div>';

                    $html[] = '<div class="photo" style="text-align: center;">' . Theme :: getInstance()->getImage(
                        'Action/Feedback',
                        'png',
                        null,
                        null,
                        null,
                        false,
                        __NAMESPACE__) . '</div>';
                    $html[] = '<div class="actions"></div>';

                    $html[] = '</div>';
                    $html[] = '<div class="clear"></div>';
                    $html[] = '</div>';
                    $html[] = '<div class="clear"></div>';

                    $html[] = ResourceManager :: get_instance()->get_resource_html(
                        Path :: getInstance()->getJavascriptPath(
                            'Chamilo\Core\Repository\ContentObject\Portfolio\Feedback',
                            true) . 'Feedback.js');
                }
            }

            if ($this->get_parent()->is_allowed_to_create_feedback())
            {
                $html[] = '<div class="feedback-form">';
                $html[] = $form->toHtml();
                $html[] = '<div class="clear"></div>';
                $html[] = '</div>';
            }

            return implode(PHP_EOL, $html);
        }
    }

    /**
     *
     * @param \core\repository\content_object\portfolio\feedback\Feedback $feedback
     * @return string
     */
    public function render_delete_action($feedback)
    {
        $delete_url = $this->get_url(
            array(
                Manager :: PARAM_ACTION => Manager :: ACTION_DELETE,
                Manager :: PARAM_FEEDBACK_ID => $feedback->get_id()));

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
    public function render_update_action($feedback)
    {
        $update_url = $this->get_url(
            array(
                Manager :: PARAM_ACTION => Manager :: ACTION_UPDATE,
                Manager :: PARAM_FEEDBACK_ID => $feedback->get_id()));

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
}
