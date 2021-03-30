<?php

namespace Chamilo\Core\Repository\ContentObject\Evaluation\Display\Component;

use Chamilo\Core\Repository\ContentObject\Evaluation\Display\Manager;
use Chamilo\Core\Repository\ContentObject\Evaluation\Display\Service\EntityService;
use Chamilo\Core\Repository\Feedback\Bridge\FeedbackServiceBridgeInterface;
use Chamilo\Libraries\Architecture\Application\ApplicationConfiguration;
use Chamilo\Libraries\Architecture\Application\ApplicationConfigurationInterface;
use Chamilo\Libraries\Format\Table\Interfaces\TableSupport;

class EntryComponent extends Manager implements \Chamilo\Core\Repository\Feedback\FeedbackSupport/*, TableSupport*/
{
    const PARAM_RUBRIC_ENTRY = 'RubricEntry';
    const PARAM_RUBRIC_RESULTS = 'RubricResult';

    /*public function __construct(ApplicationConfigurationInterface $applicationConfiguration)
    {
        parent::__construct($applicationConfiguration);
    }*/

    public function run()
    {
        return $this->getTwig()->render(
            \Chamilo\Core\Repository\ContentObject\Evaluation\Display\Manager::context() . ':EntryViewer.html.twig',
             $this->getTemplateProperties()
        );
    }

    protected function getTemplateProperties()
    {
        $entityService = $this->getService(EntityService::class);
        $contextIdentifier = $this->getEvaluationServiceBridge()->getContextIdentifier();
        $entityType = $this->getEvaluationServiceBridge()->getCurrentEntityType();
        $entityId = $this->getRequest()->query->get('entity_id');
        $evaluationEntry = $entityService->getEvaluationEntryForEntity($contextIdentifier, $entityType, $entityId);

        $this->set_parameter('entity_id', $entityId); // otherwise feedback update url doesn't pick this up

        if ($entityType == 0)
        {
            $user = $entityService->getUserForEntity($entityId);
        }

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
        $hasRubric = $canUseRubricEvaluation = false;

        if ($this->supportsRubrics())
        {
            $hasRubric = $this->getRubricService()->evaluationHasRubric($this->getEvaluation());

            $canUseRubricEvaluation = $this->canUseRubricEvaluation();

            if ($this->getRequest()->getFromUrl(self::PARAM_RUBRIC_ENTRY) && $canUseRubricEvaluation)
            {
                $rubricView = $this->runRubricComponent('Entry');
            }

            if ($this->getRequest()->getFromUrl(self::PARAM_RUBRIC_RESULTS))
            {
                $rubricView = $this->runRubricComponent('Result');
            }
        }

        return [
            'HEADER' => $this->render_header(),
            'ENTITY_TYPE' => $entityType,
            'USER' => $user,
            'CAN_EDIT_EVALUATION' => true, //$this->getAssignmentServiceBridge()->canEditAssignment(),
            'FEEDBACK_MANAGER' => $feedbackManagerHtml,
            'HAS_RUBRIC' => $hasRubric,
            'RUBRIC_VIEW' => $rubricView,
            'RUBRIC_ENTRY_URL' => $this->get_url([self::PARAM_RUBRIC_ENTRY => 1], [self::PARAM_RUBRIC_RESULTS]),
            'RUBRIC_RESULTS_URL' => $this->get_url([self::PARAM_RUBRIC_RESULTS => 1], [self::PARAM_RUBRIC_ENTRY]),
            'CAN_USE_RUBRIC_EVALUATION' => $canUseRubricEvaluation,
            'FOOTER' => $this->render_footer()
        ];
    }

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

        return $this->getRubricService()->isSelfEvaluationAllowed($this->getEvaluation());
    }
}