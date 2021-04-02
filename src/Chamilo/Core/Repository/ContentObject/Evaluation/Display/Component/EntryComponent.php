<?php

namespace Chamilo\Core\Repository\ContentObject\Evaluation\Display\Component;

use Chamilo\Core\Repository\ContentObject\Evaluation\Display\Manager;
use Chamilo\Libraries\Architecture\Application\ApplicationConfiguration;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Core\Repository\Feedback\FeedbackSupport;

class EntryComponent extends Manager implements FeedbackSupport
{
    const PARAM_RUBRIC_ENTRY = 'RubricEntry';
    const PARAM_RUBRIC_RESULTS = 'RubricResult';

    private $entityName;

    public function run()
    {
        BreadcrumbTrail::getInstance()->get_last()->set_name(
            $this->getTranslator()->trans('Evaluation', [], Manager::context()) . ' ' . $this->getEntityName());

        return $this->getTwig()->render(
            \Chamilo\Core\Repository\ContentObject\Evaluation\Display\Manager::context() . ':EntryViewer.html.twig',
             $this->getTemplateProperties()
        );
    }

    protected function getTemplateProperties()
    {
        $contextIdentifier = $this->getEvaluationServiceBridge()->getContextIdentifier();
        $entityType = $this->getEvaluationServiceBridge()->getCurrentEntityType();
        $entityId = $this->getRequest()->query->get('entity_id');
        $evaluationEntry = $this->getEntityService()->getEvaluationEntryForEntity($contextIdentifier, $entityType, $entityId);

        $this->set_parameter('entity_id', $entityId); // otherwise feedback update url doesn't pick this up

        $configuration = new ApplicationConfiguration($this->getRequest(), $this->getUser(), $this);
        $configuration->set(\Chamilo\Core\Repository\Feedback\Manager::CONFIGURATION_SHOW_FEEDBACK_HEADER, false);

        $this->getFeedbackServiceBridge()->setEntryId($evaluationEntry->getId());
        $this->getRubricBridge()->setEvaluationEntry($evaluationEntry);

        $feedbackManager = $this->getApplicationFactory()->getApplication(
            "Chamilo\Core\Repository\Feedback", $configuration,
            \Chamilo\Core\Repository\Feedback\Manager::ACTION_BROWSE
        );
        $feedbackManagerHtml = $feedbackManager->run();

        $rubricView = null;
        $rubricDisplay = null;
        $hasRubric = $canUseRubricEvaluation = false;

        if ($this->supportsRubrics())
        {
            $hasRubric = $this->getEvaluationRubricService()->evaluationHasRubric($this->getEvaluation());

            $canUseRubricEvaluation = $this->canUseRubricEvaluation();

            if ($this->getRequest()->getFromUrl(self::PARAM_RUBRIC_ENTRY) && $canUseRubricEvaluation)
            {
                $rubricView = $this->runRubricComponent('Entry');
                $rubricDisplay = 'Entry';
            }
            else if ($this->getRequest()->getFromUrl(self::PARAM_RUBRIC_RESULTS))
            {
                $rubricView = $this->runRubricComponent('Result');
                $rubricDisplay = 'Result';
            }
        }

        return [
            'HEADER' => $this->render_header(),
            'ENTITY_TYPE' => $entityType,
            'CAN_EDIT_EVALUATION' => true, //$this->getAssignmentServiceBridge()->canEditAssignment(),
            'FEEDBACK_MANAGER' => $feedbackManagerHtml,
            'HAS_RUBRIC' => $hasRubric,
            'RUBRIC_VIEW' => $rubricView,
            'RUBRIC_DISPLAY' => $rubricDisplay,
            'RUBRIC_ENTRY_URL' => $this->get_url([self::PARAM_RUBRIC_ENTRY => 1], [self::PARAM_RUBRIC_RESULTS]),
            'RUBRIC_RESULTS_URL' => $this->get_url([self::PARAM_RUBRIC_RESULTS => 1], [self::PARAM_RUBRIC_ENTRY]),
            'CAN_USE_RUBRIC_EVALUATION' => $canUseRubricEvaluation,
            'FOOTER' => $this->render_footer()
        ];
    }

    protected function getEntityName() : string
    {
        if (isset($this->entityName))
        {
            return $this->entityName;
        }

        $entityId = $this->getRequest()->query->get('entity_id');
        $entityType = $this->getEvaluationServiceBridge()->getCurrentEntityType();

        if ($entityType == 0)
        {
            $user = $this->getEntityService()->getUserForEntity($entityId);
            $this->entityName = $user->get_fullname();
            return $this->entityName;
        }

        return '';
    }

    /*public function render_header($pageTitle = '')
    {
        $html = [];
        $html[] = parent::render_header($pageTitle);
        return implode(PHP_EOL, $html);
    }*/


    /**
     *
     * @see \Chamilo\Core\Repository\Feedback\FeedbackSupport::retrieve_feedbacks()
     */
    public function retrieve_feedbacks($count, $offset)
    {
        return $this->getFeedbackServiceBridge()->getFeedbackByEntry($this->getEntry());
    }

    /**
     *
     * @see \Chamilo\Core\Repository\Feedback\FeedbackSupport::retrieve_feedback()
     */
    public function retrieve_feedback($feedbackIdentifier)
    {
        return $this->getFeedbackServiceBridge()->getFeedbackByIdentifier($feedbackIdentifier);
    }

    /**
     *
     * @see \Chamilo\Core\Repository\Feedback\FeedbackSupport::get_feedback()
     */
    public function get_feedback()
    {
        return null;
    }

    /**
     *
     * @see \Chamilo\Core\Repository\Feedback\FeedbackSupport::count_feedbacks()
     */
    public function count_feedbacks()
    {
        return $this->getFeedbackServiceBridge()->countFeedbackByEntry($this->getEntry());
    }

    /**
     *
     * @see \Chamilo\Core\Repository\Feedback\FeedbackSupport::is_allowed_to_view_feedback()
     */
    public function is_allowed_to_view_feedback()
    {
        return true;
    }

    /**
     *
     * @see \Chamilo\Core\Repository\Feedback\FeedbackSupport::is_allowed_to_create_feedback()
     */
    public function is_allowed_to_create_feedback()
    {
        return true;
    }

    /**
     * @param \Chamilo\Core\Repository\Feedback\Storage\DataClass\Feedback $feedback
     *
     * @return bool
     */
    public function is_allowed_to_update_feedback($feedback)
    {
        return $feedback->get_user_id() == $this->getUser()->getId();
    }

    /**
     *
     * @see \Chamilo\Core\Repository\Feedback\FeedbackSupport::is_allowed_to_delete_feedback()
     */
    public function is_allowed_to_delete_feedback($feedback)
    {
        return $feedback->get_user_id() == $this->getUser()->getId();
    }

    /**
     * @return bool|null
     */
    protected function canUseRubricEvaluation()
    {
        if ($this->getEvaluationServiceBridge()->canEditEvaluation())
        {
            return true;
        }

        return $this->getEvaluationRubricService()->isSelfEvaluationAllowed($this->getEvaluation());
    }
}