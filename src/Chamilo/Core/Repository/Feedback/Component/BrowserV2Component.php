<?php
namespace Chamilo\Core\Repository\Feedback\Component;

use Chamilo\Core\Repository\Common\ContentObjectResourceRenderer;
use Chamilo\Core\Repository\Feedback\FeedbackNotificationSupport;
use Chamilo\Core\Repository\Feedback\Form\AddFeedbackFormType;
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
use Chamilo\Libraries\Format\Table\Pager;
use Chamilo\Libraries\Format\Table\PagerRenderer;
use Chamilo\Libraries\Storage\ResultSet\ResultSet;


class BrowserV2Component extends Manager implements DelegateComponent
{
    const PARAM_COUNT = 'feedback_count';
    const PARAM_PAGE_NUMBER = 'feedback_page_nr';

    /**
     * Executes this controller
     */
    public function run()
    {
        if (! $this->get_application()->is_allowed_to_view_feedback() &&
             ! $this->get_application()->is_allowed_to_create_feedback())
        {
            throw new NotAllowedException();
        }

        $formFactory = $this->getForm();

        $form = $formFactory->create(AddFeedbackFormType::class);


        $form->handleRequest($this->getRequest());

        if($form->isValid())
        {
            if (! $this->get_application()->is_allowed_to_create_feedback())
            {
                throw new NotAllowedException();
            }

            $formData = $form->getData();

            /**
             * @var Feedback $feedback
             */
            $feedback = $this->get_application()->get_feedback();

            $feedback->set_user_id($this->getUser()->getId());
            $feedback->set_comment($formData[Feedback::PROPERTY_COMMENT]);
            $feedback->set_creation_date(time());
            $feedback->set_modification_date(time());

            $success = $feedback->create();

            $this->notifyNewFeedback($feedback);

            $this->redirect(
                $this->getTranslator()->trans(
                    $success ? 'ObjectCreated' : 'ObjectNotCreated',
                    array('OBJECT' => $this->getTranslator()->trans('Feedback', [], 'Chamilo\Core\Repository\Feedback')),
                    'Chamilo\Libraries'),
                ! $success);
        }

        $feedback = $this->get_application()->retrieve_feedbacks(
            $this->getPager()->getNumberOfItemsPerPage(),
            $this->getPager()->getCurrentRangeOffset());

        if ($feedback instanceof ResultSet) {
            $feedback = $feedback->as_array();
        }

        $feedbackCount = count($feedback);

        if($this->get_application()->count_feedbacks() > $feedbackCount) {
            $pagination = $this->getPagerRenderer()->renderPaginationWithPageLimit(
                $this->get_parameters(),
                self::PARAM_PAGE_NUMBER);
        } else {
            $pagination = null;
        }

        $formView = $form->createView();
        return $this->getTwig()
            ->render(
                Manager::context() . ':add_feedback.html.twig',
                [
                    'form' => $formView,
                    'createRight' => $this->get_application()->is_allowed_to_create_feedback(),
                    'feedbackCount' => $feedbackCount,
                    'feedbackToolbar' => $this->renderFeedbackButtonToolbar(),
                    'showFeedbackHeader' => $this->showFeedbackHeader(),
                    'feedback' => $this->toFeedbackDTOs($feedback),
                    'pagination' => $pagination
                ]
            );
    }

    protected function toFeedbackDTOs($feedback)
    {
        $feedbackDTOs = [];
        foreach ($feedback as $feedbackItem) {
            /**
             * @var Feedback $feedbackItem
             */
            $feedbackDTO = [];
            $profilePhotoUrl = new Redirect(
                array(
                    Application::PARAM_CONTEXT => \Chamilo\Core\User\Ajax\Manager::context(),
                    Application::PARAM_ACTION => \Chamilo\Core\User\Ajax\Manager::ACTION_USER_PICTURE,
                    \Chamilo\Core\User\Manager::PARAM_USER_USER_ID => $feedbackItem->get_user()->getId())
            );

            $feedbackDTO['photoUrl'] = $profilePhotoUrl->getUrl();
            $feedbackDTO['updateAllowed'] = $this->get_application()->is_allowed_to_update_feedback($feedbackItem);
            $feedbackDTO['updateUrl'] = $this->get_url(
                [
                    Manager::PARAM_ACTION => Manager::ACTION_UPDATE,
                    Manager::PARAM_FEEDBACK_ID => $feedbackItem->get_id()
                ]
            );
            $feedbackDTO['deleteAllowed'] = $this->get_application()->is_allowed_to_delete_feedback($feedbackItem);
            $feedbackDTO['deleteUrl'] = $this->get_url(
                [
                    Manager::PARAM_ACTION => Manager::ACTION_DELETE,
                    Manager::PARAM_FEEDBACK_ID => $feedbackItem->getId()
                ]
            );
            $feedbackDTO['userFullname'] = $feedbackItem->get_user()->get_fullname();
            $feedbackDTO['creationDate'] = $feedbackItem->get_creation_date();
            $feedbackDTO['content'] = $this->renderFeedbackContent($feedbackItem);

            $feedbackDTOs[] = $feedbackDTO;
        }

        return $feedbackDTOs;
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
        $buttonToolbar = new ButtonToolBar(null, array(), array('receive-feedback-buttons'));
        $buttonToolbarRenderer = new ButtonToolBarRenderer($buttonToolbar);

        if (! $this->get_application() instanceof FeedbackNotificationSupport)
        {
            return $buttonToolbarRenderer->render();
        }

        $hasNotification = false;

        $isAllowedToViewFeedback = $this->get_application()->is_allowed_to_view_feedback();
        $isAllowedToCreateFeedback = $this->get_application()->is_allowed_to_create_feedback();

        if ($isAllowedToViewFeedback || $isAllowedToCreateFeedback)
        {
            $baseParameters = array();

            if ($isAllowedToViewFeedback)
            {
                $feedbackCount = $this->get_application()->count_feedbacks();
                $portfolioNotification = $this->get_application()->retrieve_notification();
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
                $hasNotification);

            $buttonToolbar->addItems($actionsGenerator->run());
        }

        $html = array();

        $html[] = '<div class="receive-feedback-spacer"></div>';

        if ($hasNotification)
        {
            $html[] = '<div class="alert alert-info alert-receive-feedback">';
            $html[] = '<div class="pull-left receive-feedback-info">Nieuwe feedback wordt naar jouw e-mail verzonden.</div>';
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
                $this->get_application()->count_feedbacks(),
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
