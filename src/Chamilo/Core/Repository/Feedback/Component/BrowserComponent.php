<?php

namespace Chamilo\Core\Repository\Feedback\Component;

use Chamilo\Core\Repository\Common\ContentObjectResourceRenderer;
use Chamilo\Core\Repository\Feedback\FeedbackNotificationSupport;
use Chamilo\Core\Repository\Feedback\Form\FeedbackForm;
use Chamilo\Core\Repository\Feedback\Generator\ActionsGenerator;
use Chamilo\Core\Repository\Feedback\Manager;
use Chamilo\Core\Repository\Feedback\Storage\DataClass\Feedback;
use Chamilo\Core\Repository\Feedback\Storage\DataClass\Notification;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar;
use Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Table\Pager;
use Chamilo\Libraries\Format\Table\PagerRenderer;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\DatetimeUtilities;
use Chamilo\Libraries\Utilities\Utilities;

class BrowserComponent extends Manager implements DelegateComponent
{
    const PARAM_COUNT = 'feedback_count';
    const PARAM_PAGE_NUMBER = 'feedback_page_nr';

    /**
     * Executes this controller
     */
    public function run()
    {
        $canViewFeedback = $this->feedbackRightsServiceBridge->canViewFeedback();
        $canCreateFeedback = $this->feedbackRightsServiceBridge->canCreateFeedback();

        if (!$canViewFeedback && !$canCreateFeedback)
        {
            throw new NotAllowedException();
        }

        $form = new FeedbackForm($this, $this->get_url());

        if ($form->validate())
        {
            if (!$canCreateFeedback)
            {
                throw new NotAllowedException();
            }

            $values = $form->exportValues();

            $feedback = $this->feedbackServiceBridge->createFeedback($this->getUser(), $values[Feedback::PROPERTY_COMMENT]);
            $success = $feedback instanceof Feedback;

            $this->notifyNewFeedback($feedback);

            $this->redirect(
                Translation::get(
                    $success ? 'ObjectCreated' : 'ObjectNotCreated',
                    array('OBJECT' => Translation::get('Feedback')),
                    Utilities::COMMON_LIBRARIES
                ),
                !$success
            );
        }
        else

        {
            $html = [];

            $feedbacks = $this->feedbackServiceBridge->getFeedback(
                $this->getPager()->getNumberOfItemsPerPage(),
                $this->getPager()->getCurrentRangeOffset()
            );

            $feedbackCount = count($feedbacks);

            if ($feedbackCount == 0)
            {
                $html[] = $this->renderFeedbackButtonToolbar();
                $html[] = '<div class="alert alert-info">';
                $html[] = Translation::get('NoFeedbackYet');
                $html[] = '</div>';
            }

            if ($feedbackCount > 0)
            {
                if (!$canCreateFeedback)
                {
                    $html[] = $this->renderFeedbackButtonToolbar();
                }

                if ($this->showFeedbackHeader())
                {
                    $html[] = '<h3>';
                    $html[] = Translation::get('Feedback');
                    $html[] = '<div class="clearfix"></div>';
                    $html[] = '</h3>';
                }

                $html[] = '<div class="panel panel-default panel-feedback">';
                $html[] = '<div class="list-group">';

                foreach ($feedbacks as $feedback)
                {
                    $html[] = '<div class="list-group-item" id="feedback' . $feedback->getId() . '">';

                    $html[] = '<div style="display:flex;">';
                    $profilePhotoUrl = new Redirect(
                        array(
                            Application::PARAM_CONTEXT => \Chamilo\Core\User\Ajax\Manager::context(),
                            Application::PARAM_ACTION => \Chamilo\Core\User\Ajax\Manager::ACTION_USER_PICTURE,
                            \Chamilo\Core\User\Manager::PARAM_USER_USER_ID => $feedback->get_user()->get_id()
                        )
                    );

                    $html[] = '<img class="panel-feedback-profile" src="' . $profilePhotoUrl->getUrl() . '" />';

                    $html[] = '<h4 class="list-group-item-heading" style="flex-grow: 2;">' .
                        $feedback->get_user()->get_fullname() .
                        '<div class="feedback-date">' . $this->format_date($feedback->get_creation_date()) .
                        '</div></h4>';

                    $allowedToUpdateFeedback = $this->feedbackRightsServiceBridge->canEditFeedback($feedback);
                    $allowedToDeleteFeedback = $this->feedbackRightsServiceBridge->canDeleteFeedback($feedback);

                    if ($allowedToUpdateFeedback || $allowedToDeleteFeedback)
                    {
                        $html[] = '<div class="text-right" style="min-width: 40px;">';

                        if ($allowedToUpdateFeedback)
                        {
                            $html[] = $this->render_update_action($feedback);
                        }

                        if ($allowedToDeleteFeedback)
                        {
                            $html[] = $this->render_delete_action($feedback);
                        }

                        $html[] = '</div>';
                    }

                    $html[] = '</div>';
                    $html[] = '<div class="list-group-item-text feedback-content">' .
                        $this->renderFeedbackContent($feedback) . '</div>';

                    $html[] = '</div>';
                }

                $html[] = '</div>';
                $html[] = '</div>';

                if ($this->feedbackServiceBridge->countFeedback() > $feedbackCount)
                {
                    $html[] = '<div class="row">';
                    $html[] = '<div class="col-xs-12 feedback-pagination">';
                    $html[] = $this->getPagerRenderer()->renderPaginationWithPageLimit(
                        $this->get_parameters(),
                        self::PARAM_PAGE_NUMBER
                    );
                    $html[] = '</div>';
                    $html[] = '</div>';
                }
            }

            if ($canCreateFeedback)
            {
                $html[] = $this->renderFeedbackButtonToolbar();

                $html[] = '<h3>';
                $html[] = Translation::get('AddFeedback');
                $html[] = '<div class="clearfix"></div>';
                $html[] = '</h3>';

                $html[] = $form->toHtml();
            }

            return implode(PHP_EOL, $html);
        }
    }

    protected function renderFeedbackContent(Feedback $feedback)
    {
        $content = $feedback->get_comment();

        $descriptionRenderer = new ContentObjectResourceRenderer($this, $content);

        return $descriptionRenderer->run();
    }

    /**
     * Renders the feedback button
     *
     * @return ButtonToolBar|string
     */
    protected function renderFeedbackButtonToolbar()
    {
        $buttonToolbar = new ButtonToolBar(null, [], array('receive-feedback-buttons'));
        $buttonToolbarRenderer = new ButtonToolBarRenderer($buttonToolbar);

        if (!$this->get_application() instanceof FeedbackNotificationSupport)
        {
            return $buttonToolbarRenderer->render();
        }

        $hasNotification = false;

        $isAllowedToViewFeedback = $this->feedbackRightsServiceBridge->canViewFeedback();
        $isAllowedToCreateFeedback = $this->feedbackRightsServiceBridge->canCreateFeedback();

        if ($isAllowedToViewFeedback || $isAllowedToCreateFeedback)
        {
            $baseParameters = [];

            if ($isAllowedToViewFeedback)
            {
                $feedbackCount = $this->feedbackServiceBridge->countFeedback();
                $portfolioNotification = $this->get_parent()->retrieve_notification();
                $hasNotification = $portfolioNotification instanceof Notification;
            }
            else
            {
                $feedbackCount = 0;
                $hasNotification = false;
            }

            $actionsGenerator = new ActionsGenerator(
                $this->get_application(),
                $baseParameters,
                $isAllowedToViewFeedback,
                $feedbackCount,
                $hasNotification
            );

            $buttonToolbar->addItems($actionsGenerator->run());
        }

        $html = [];

        $html[] = '<div class="receive-feedback-spacer"></div>';

        if ($hasNotification)
        {
            $html[] = '<div class="alert alert-info alert-receive-feedback">';
            $html[] =
                '<div class="pull-left receive-feedback-info">Nieuwe feedback wordt naar jouw e-mail verzonden.</div>';
        }

        $html[] = $buttonToolbarRenderer->render();

        if ($hasNotification)
        {
            $html[] = '<div class="clearfix"></div>';
            $html[] = '</div>';
        }

        return implode(PHP_EOL, $html);
    }

    /**
     *
     * @param \core\repository\content_object\portfolio\feedback\Feedback $feedback
     *
     * @return string
     */
    public function render_delete_action($feedback_publication)
    {
        $delete_url = $this->get_url(
            array(
                Manager::PARAM_ACTION => Manager::ACTION_DELETE,
                Manager::PARAM_FEEDBACK_ID => $feedback_publication->get_id()
            )
        );

        $bootstrapGlyph = new FontAwesomeGlyph('times');
        $title = Translation::get('Delete', null, Utilities::COMMON_LIBRARIES);
        $delete_link = '<a title="' . htmlentities($title) . '" href="' . $delete_url . '" onclick="return confirm(\'' .
            addslashes(Translation::get('Confirm', null, Utilities::COMMON_LIBRARIES)) . '\');">' .
            $bootstrapGlyph->render() . '</a>';

        return $delete_link;
    }

    /**
     *
     * @param \core\repository\content_object\portfolio\feedback\Feedback $feedback
     *
     * @return string
     */
    public function render_update_action($feedback_publication)
    {
        $update_url = $this->get_url(
            array(
                Manager::PARAM_ACTION => Manager::ACTION_UPDATE,
                Manager::PARAM_FEEDBACK_ID => $feedback_publication->get_id()
            )
        );

        $bootstrapGlyph = new FontAwesomeGlyph('pencil-alt');
        $title = Translation::get('Edit', null, Utilities::COMMON_LIBRARIES);
        $update_link =
            '<a title="' . htmlentities($title) . '" href="' . $update_url . '">' . $bootstrapGlyph->render() .
            '</a>';

        return $update_link;
    }

    /**
     *
     * @param int $date
     *
     * @return string
     */
    public function format_date($date)
    {
        $date_format = Translation::get('DateTimeFormatLong', null, Utilities::COMMON_LIBRARIES);

        return DatetimeUtilities::format_locale_date($date_format, $date);
    }

    /**
     *
     * @return integer
     */
    public function getCount()
    {
        return $this->getRequest()->query->get(self::PARAM_COUNT, 5);
    }

    /**
     *
     * @return integer
     */
    public function getPageNumber()
    {
        return $this->getRequest()->query->get(self::PARAM_PAGE_NUMBER, 1);
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
                $this->feedbackServiceBridge->countFeedback(),
                $this->getPageNumber()
            );
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
