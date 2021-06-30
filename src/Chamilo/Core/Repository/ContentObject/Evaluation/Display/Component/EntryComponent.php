<?php

namespace Chamilo\Core\Repository\ContentObject\Evaluation\Display\Component;

use Chamilo\Core\Repository\ContentObject\Evaluation\Display\Bridge\Interfaces\EmbeddedViewSupport;
use Chamilo\Core\Repository\ContentObject\Evaluation\Display\Manager;
use Chamilo\Core\Repository\ContentObject\Evaluation\Display\Service\Entity\EvaluationEntityRetrieveProperties;
use Chamilo\Core\Repository\ContentObject\Evaluation\Interfaces\ConfirmRubricScoreInterface;
use Chamilo\Core\Repository\ContentObject\Evaluation\Storage\DataClass\EvaluationEntry;
use Chamilo\Core\Repository\ContentObject\Evaluation\Storage\DataClass\EvaluationEntryScore;
use Chamilo\Libraries\Architecture\Application\ApplicationConfiguration;
use Chamilo\Libraries\Architecture\ContextIdentifier;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Core\Repository\Feedback\FeedbackSupport;
use Chamilo\Libraries\Storage\FilterParameters\FilterParameters;
use Chamilo\Core\Repository\ContentObject\Rubric\Display\Manager as RubricDisplayManager;

/**
 * @package Chamilo\Core\Repository\ContentObject\Evaluation\Display\Component
 *
 * @author - Stefan Gabriëls - Hogeschool Gent
 */
class EntryComponent extends Manager implements FeedbackSupport, ConfirmRubricScoreInterface
{
    const PARAM_RUBRIC_ENTRY = 'RubricEntry';
    const PARAM_RUBRIC_RESULTS = 'RubricResult';

    /**
     *
     * @var string
     */
    private $entityName;

    /**
     * @var EvaluationEntry
     */
    protected $evaluationEntry;

    public function run()
    {
        $this->ensureEntityIdentifier();
        $this->getRubricBridge()->setConfirmRubricScore($this);
        $this->evaluationEntry = $this->ensureEvaluationEntry();
        if (!$this->evaluationEntry )
        {
            throw new NotAllowedException();
        }
        $this->checkAccessRights();

        $this->getFeedbackRightsServiceBridge()->setEvaluationEntry($this->evaluationEntry);

        if ($this->getRightsService()->canUserEditEvaluation())
        {
            $title = $this->getTranslator()->trans('Evaluation', [], Manager::context()) . ' ' . $this->getEntityName();
            BreadcrumbTrail::getInstance()->get_last()->set_name($title);
        }
        else
        {
            BreadcrumbTrail::getInstance()->remove(count(BreadcrumbTrail::getInstance()->getBreadcrumbs()) - 1);
        }

        return $this->getTwig()->render(
            \Chamilo\Core\Repository\ContentObject\Evaluation\Display\Manager::context() . ':EntryViewer.html.twig',
             $this->getTemplateProperties($this->evaluationEntry)
        );
    }

    /**
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NotAllowedException
     */
    protected function checkAccessRights()
    {
        if ($this->evaluationEntry &&
            $this->getRightsService()->canUserViewEntry($this->getUser(), $this->evaluationEntry))
        {
            return;
        }

        if ($this->getRightsService()->canUserViewEntity(
            $this->getUser(), $this->getEntityType(), $this->getEntityIdentifier()
        ))
        {
            return;
        }

        throw new NotAllowedException();
    }

    /**
     * @return EvaluationEntry|\Chamilo\Libraries\Storage\DataClass\CompositeDataClass|\Chamilo\Libraries\Storage\DataClass\DataClass|false
     * @throws \Chamilo\Libraries\Architecture\Exceptions\UserException
     */
    private function ensureEvaluationEntry()
    {
        $contextIdentifier = $this->getEvaluationServiceBridge()->getContextIdentifier();
        $entityType = $this->getEntityType();
        $entityId = $this->getEntityIdentifier();
        $evaluation = $this->get_root_content_object();

        if ($this->getRightsService()->canUserEditEvaluation())
        {
            return $this->getEvaluationEntryService()->createEvaluationEntryIfNotExists($evaluation->getId(), $contextIdentifier, $entityType, $entityId);
        }
        else
        {
            return $this->getEvaluationEntryService()->getEvaluationEntryForEntity($contextIdentifier, $entityType, $entityId);
        }
    }

    /**
     * @param EvaluationEntry $evaluationEntry
     * @return array
     * @throws \Chamilo\Libraries\Architecture\Exceptions\ClassNotExistException
     */
    protected function getTemplateProperties(EvaluationEntry $evaluationEntry): array
    {
        $entityId = $this->getEntityIdentifier();
        $evaluationScore = $this->getEvaluationEntryService()->getEvaluationEntryScore($evaluationEntry->getId());
        $score = '';
        $presenceStatus = 'neutral';
        $rubricScore = null;
        $confirmOverwriteScore = false;

        if ($evaluationScore instanceof EvaluationEntryScore)
        {
            $score = $evaluationScore->getScore();
            $presenceStatus = $evaluationScore->isAbsent() ? 'absent' : 'present';
        }

        if ($this->getRightsService()->canUserEditEvaluation() && $this->getRequest()->getFromUrl('RubricScoreUpdated') && !is_null($this->getRubricScore()))
        {
            $rubricScore = $this->getRubricScore();
            if ($score != $rubricScore)
            {
                $confirmOverwriteScore = true;
            }
        }

        $this->clearRubricScore();

        $this->set_parameter('entity_id', $entityId); // otherwise feedback update url doesn't pick this up

        $configuration = new ApplicationConfiguration($this->getRequest(), $this->getUser(), $this);
        $configuration->set(\Chamilo\Core\Repository\Feedback\Manager::CONFIGURATION_SHOW_FEEDBACK_HEADER, false);

        $this->getFeedbackServiceBridge()->setEntryId($evaluationEntry->getId());
        $this->getRubricBridge()->setEvaluationEntry($evaluationEntry);
        $this->getRubricBridge()->setPostSaveRedirectParameters([RubricDisplayManager::PARAM_ACTION => null, RubricDisplayManager::ACTION_RESULT => 1, 'RubricScoreUpdated' => 1]);

        $feedbackManager = $this->getApplicationFactory()->getApplication(
            'Chamilo\Core\Repository\Feedback', $configuration,
            \Chamilo\Core\Repository\Feedback\Manager::ACTION_BROWSE
        );
        $feedbackManagerHtml = $feedbackManager->run();

        $rubricAction = null;
        $rubricView = null;
        $hasRubric = $canUseRubricEvaluation = false;

        $entityIds = $this->getEvaluationServiceBridge()->getTargetEntityIds();
        $contextIdentifier = $this->getEvaluationServiceBridge()->getContextIdentifier();
        $openForStudents = $this->getEvaluationServiceBridge()->getOpenForStudents();

        $selectedEntities = $this->getEntityService()->getEntitiesFromIds($entityIds, $contextIdentifier, EvaluationEntityRetrieveProperties::NONE(), new FilterParameters());

        $entities = array();
        $selectedEntity = null;
        $prev = null;
        $previousEntity = null;
        $nextEntity = null;
        $count = 0;
        $entityIndex = 0;
        $entityType = $this->getEntityType();

        foreach ($selectedEntities as $entity)
        {
            if ($entityType == 0)
            {
                $name = strtoupper($entity['lastname']) . ' ' . $entity['firstname'];
            }
            else
            {
                $name = $entity['name'];
            }
            $entities[] = ['name' => $name, 'url' => $this->get_url(['entity_id' => $entity['id']]), 'selected' => $entityId == $entity['id']];
            if ($entityId == $entity['id'])
            {
                $selectedEntity = end($entities);
                $previousEntity = $prev;
                $entityIndex = $count + 1;
            } else if (isset($prev) && $prev['selected'])
            {
                $nextEntity = end($entities);
            }
            $prev = end($entities);
            $count++;
        }

        if ($this->supportsRubrics())
        {
            $hasRubric = $this->getEvaluationRubricService()->evaluationHasRubric($this->getEvaluation());
            $canUseRubricEvaluation = $this->canUseRubricEvaluation();
            $entryContextIdentifier = $this->getRubricBridge()->getContextIdentifier();
            $rubricAction = $this->getRubricActionFromRequest($canUseRubricEvaluation, $entryContextIdentifier);

            if ($rubricAction)
            {
                $rubricView = $this->runRubricComponent($rubricAction);
            }
        }

        return [
            'HEADER' => $this->render_header(),
            'EVALUATION_TITLE' => $this->getEvaluation()->get_title(),
            'ENTITIES' => $entities,
            'SELECTED_ENTITY' => $selectedEntity,
            'PREVIOUS_ENTITY' => $previousEntity,
            'NEXT_ENTITY' => $nextEntity,
            'DISPLAY_ALL_ENTITIES_URL' => $this->getEvaluationServiceBridge() instanceof EmbeddedViewSupport,
            'ALL_ENTITIES_URL' => $this->get_url([self::PARAM_ACTION => self::DEFAULT_ACTION, 'entity_id' => null]),
            'ENTITY_COUNT' => $count,
            'ENTITY_INDEX' => $entityIndex,
            'ENTITY_TYPE' => $entityType,
            'CAN_EDIT_EVALUATION' => $this->getRightsService()->canUserEditEvaluation(),
            'PRESENCE_STATUS' => $presenceStatus,
            'SCORE' => $score,
            'SAVE_SCORE_URL' => $this->get_url([self::PARAM_ACTION => self::ACTION_SAVE_SCORE]),
            'FEEDBACK_MANAGER' => $feedbackManagerHtml,
            'HAS_RUBRIC' => $hasRubric,
            'RUBRIC_VIEW' => $rubricView,
            'RUBRIC_ACTION' => $rubricAction,
            'RUBRIC_ENTRY_URL' => $this->get_url([self::PARAM_RUBRIC_ENTRY => 1], [self::PARAM_RUBRIC_RESULTS]),
            'RUBRIC_RESULTS_URL' => $this->get_url([self::PARAM_RUBRIC_RESULTS => 1], [self::PARAM_RUBRIC_ENTRY]),
            'CAN_USE_RUBRIC_EVALUATION' => $canUseRubricEvaluation,
            'RUBRIC_SCORE' => $rubricScore,
            'CONFIRM_OVERWRITE_SCORE' => $confirmOverwriteScore,
            'OPEN_FOR_STUDENTS' => $openForStudents,
            'FOOTER' => $this->render_footer()
        ];
    }

    /**
     * @param bool $canUseRubricEvaluation
     * @param ContextIdentifier $contextIdentifier
     * @return string|null
     */
    protected function getRubricActionFromRequest(bool $canUseRubricEvaluation, ContextIdentifier $contextIdentifier) : ?string
    {
        if ($canUseRubricEvaluation && $this->getRequest()->getFromUrl(self::PARAM_RUBRIC_ENTRY))
        {
            return 'Entry';
        }

        if ($this->getRequest()->getFromUrl(self::PARAM_RUBRIC_RESULTS))
        {
            return 'Result';
        }

        if ($this->getEvaluationRubricService()->entryHasResults($contextIdentifier))
        {
            return 'Result';
        }

        if ($canUseRubricEvaluation)
        {
            return 'Entry';
        }

        return null;
    }

    protected function getEntityName() : string
    {
        if (isset($this->entityName))
        {
            return $this->entityName;
        }

        $entityId = $this->getEntityIdentifier();
        $this->entityName = $this->getEvaluationServiceBridge()->getEntityDisplayName($entityId);
        return $this->entityName;
    }

    public function render_header($pageTitle = '')
    {
        if ($this->getRightsService()->canUserEditEvaluation())
        {
            return parent::render_header();
        }
        $html = [];
        $html[] = parent::render_header('');
        return implode(PHP_EOL, $html);
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

        return $this->getEvaluationRubricService()->isSelfEvaluationAllowed($this->getEvaluation());
    }

    /**
     * @return int|null
     */
    protected function getRubricScore()
    {
        return $this->getSessionUtilities()->retrieve(self::SESSION_RUBRIC_SCORE);
    }

    /**
     * @param int $score
     */
    public function registerRubricScore(int $score): void
    {
        $this->getSessionUtilities()->register(self::SESSION_RUBRIC_SCORE, $score);
    }

    protected function clearRubricScore()
    {
        $this->getSessionUtilities()->unregister(self::SESSION_RUBRIC_SCORE);
    }
}