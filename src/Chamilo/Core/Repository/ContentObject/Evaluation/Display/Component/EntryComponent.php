<?php

namespace Chamilo\Core\Repository\ContentObject\Evaluation\Display\Component;

use Chamilo\Core\Repository\ContentObject\Evaluation\Display\Manager;
use Chamilo\Core\Repository\ContentObject\Evaluation\Storage\DataClass\EvaluationEntry;
use Chamilo\Core\Repository\ContentObject\Evaluation\Storage\DataClass\EvaluationEntryScore;
use Chamilo\Libraries\Architecture\Application\ApplicationConfiguration;
use Chamilo\Libraries\Architecture\ContextIdentifier;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Core\Repository\Feedback\FeedbackSupport;
use Chamilo\Libraries\Storage\FilterParameters\FilterParameters;

/**
 * @package Chamilo\Core\Repository\ContentObject\Evaluation\Display\Component
 *
 * @author - Stefan Gabriëls - Hogeschool Gent
 */
class EntryComponent extends Manager implements FeedbackSupport
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
            return $this->getEntityService()->createEvaluationEntryIfNotExists($evaluation->getId(), $contextIdentifier, $entityType, $entityId);
        }
        else
        {
            return $this->getEntityService()->getEvaluationEntryForEntity($contextIdentifier, $entityType, $entityId);
        }
    }

    /**
     * @param EvaluationEntry $evaluationEntry
     * @return array
     * @throws \Chamilo\Libraries\Architecture\Exceptions\ClassNotExistException
     */
    protected function getTemplateProperties(EvaluationEntry $evaluationEntry): array
    {
        $entityType = $this->getEntityType();
        $entityId = $this->getEntityIdentifier();
        $evaluationScore = $this->getEntityService()->getEvaluationEntryScore($evaluationEntry->getId());
        $score = '';
        $presenceStatus = 'neutral';

        if ($evaluationScore instanceof EvaluationEntryScore)
        {
            $score = $evaluationScore->getScore();
            $presenceStatus = $evaluationScore->isAbsent() ? 'absent' : 'present';
        }

        $this->set_parameter('entity_id', $entityId); // otherwise feedback update url doesn't pick this up

        $configuration = new ApplicationConfiguration($this->getRequest(), $this->getUser(), $this);
        $configuration->set(\Chamilo\Core\Repository\Feedback\Manager::CONFIGURATION_SHOW_FEEDBACK_HEADER, false);

        $this->getFeedbackServiceBridge()->setEntryId($evaluationEntry->getId());
        $this->getRubricBridge()->setEvaluationEntry($evaluationEntry);

        $feedbackManager = $this->getApplicationFactory()->getApplication(
            'Chamilo\Core\Repository\Feedback', $configuration,
            \Chamilo\Core\Repository\Feedback\Manager::ACTION_BROWSE
        );
        $feedbackManagerHtml = $feedbackManager->run();

        $rubricAction = null;
        $rubricView = null;
        $hasRubric = $canUseRubricEvaluation = false;

        $userIds = $this->getEvaluationServiceBridge()->getTargetEntityIds();
        $contextIdentifier = $this->getEvaluationServiceBridge()->getContextIdentifier();

        $selectedUsers = $this->getEntityService()->getUsersFromIDs($userIds, $contextIdentifier, new FilterParameters());
        $users = array();
        $selectedUser = null;

        $prev = null;
        $previousUser = null;
        $nextUser = null;
        $count = 0;
        $userIndex = 0;
        foreach ($selectedUsers as $user)
        {
            $users[] = ['fullname' => strtoupper($user['lastname']) . ' ' . $user['firstname'], 'url' => $this->get_url(['entity_id' => $user['id']]), 'selected' => $entityId == $user['id']];
            if ($entityId == $user['id'])
            {
                $selectedUser = end($users);
                $previousUser = $prev;
                $userIndex = $count + 1;
            } else if (isset($prev) && $prev['selected'])
            {
                $nextUser = end($users);
            }
            $prev = end($users);
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
            'USERS' => $users,
            'SELECTED_USER' => $selectedUser,
            'PREVIOUS_USER' => $previousUser,
            'NEXT_USER' => $nextUser,
            'USER_COUNT' => $count,
            'USER_INDEX' => $userIndex,
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
        $entityType = $this->getEntityType();

        if ($entityType == 0)
        {
            $user = $this->getEntityService()->getUserForEntity($entityId);
            $this->entityName = $user->get_fullname();
            return $this->entityName;
        }

        return '';
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
}