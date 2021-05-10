<?php

namespace Chamilo\Core\Repository\ContentObject\Evaluation\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Bridge;

use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Core\Repository\ContentObject\Evaluation\Display\Bridge\Interfaces\EmbeddedViewSupport;
use Chamilo\Core\Repository\ContentObject\Evaluation\Display\Bridge\Interfaces\EvaluationServiceBridgeInterface;
use Chamilo\Core\Repository\ContentObject\Evaluation\Display\Service\EntityService;
use Chamilo\Core\Repository\ContentObject\Evaluation\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Bridge\Interfaces\LearningPathEvaluationServiceBridgeInterface;
use Chamilo\Core\Repository\ContentObject\Evaluation\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Domain\EvaluationConfiguration;
use Chamilo\Core\Repository\ContentObject\Evaluation\Storage\DataClass\Evaluation;
use Chamilo\Core\Repository\ContentObject\Evaluation\Storage\DataClass\EvaluationEntry;
use Chamilo\Core\Repository\ContentObject\Evaluation\Storage\DataClass\EvaluationEntryScore;
use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Attempt\TreeNodeAttempt;
use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\ContextIdentifier;
use Chamilo\Core\Repository\ContentObject\LearningPath\Service\LearningPathStepContextService;

/**
 * Class EvaluationServiceBridge
 * @package Chamilo\Core\Repository\ContentObject\Evaluation\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Bridge
 */
class EvaluationServiceBridge implements EvaluationServiceBridgeInterface, EmbeddedViewSupport
{
    /**
     * @var EntityService
     */
    protected $entityService;

    /**
     * @var LearningPathStepContextService
     */
    protected $learningPathStepContextService;

    /**
     * @var LearningPathEvaluationServiceBridgeInterface
     */
    protected $learningPathEvaluationServiceBridge;

    /**
     * @var TreeNode
     */
    protected $treeNode;

    /**
     * @var TreeNodeAttempt
     */
    protected $treeNodeAttempt;

    /**
     * @var EvaluationConfiguration $treeNodeConfiguration
     */
    protected $treeNodeConfiguration;

    /**
     * EvaluationServiceBridge constructor.
     *
     * @param LearningPathEvaluationServiceBridgeInterface $learningPathEvaluationServiceBridge
     * @param EntityService $entityService
     * @param LearningPathStepContextService $learningPathStepContextService
     */
    public function __construct(LearningPathEvaluationServiceBridgeInterface $learningPathEvaluationServiceBridge, EntityService $entityService, LearningPathStepContextService $learningPathStepContextService)
    {
        $this->learningPathEvaluationServiceBridge = $learningPathEvaluationServiceBridge;
        $this->entityService = $entityService;
        $this->learningPathStepContextService = $learningPathStepContextService;
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode $treeNode
     */
    public function setTreeNode(TreeNode $treeNode)
    {
        if (!$treeNode->getContentObject() instanceof Evaluation)
        {
            throw new \RuntimeException(
                'The given treenode does not reference a valid evaluation and should not be used'
            );
        }

        $this->treeNode = $treeNode;
        $this->treeNodeConfiguration = $this->treeNode->getConfiguration(new EvaluationConfiguration());
    }

    /**
     * @param TreeNodeAttempt $treeNodeAttempt
     */
    public function setTreeNodeAttempt(TreeNodeAttempt $treeNodeAttempt)
    {
        $this->treeNodeAttempt = $treeNodeAttempt;
    }

    /**
     * @param User $currentUser
     *
     * @return int
     */
    public function getCurrentEntityIdentifier(User $currentUser): int
    {
        return $currentUser->getId(); // Todo: get according to entity type
    }

    /**
     * @return int
     */
    public function getCurrentEntityType(): int
    {
        return $this->treeNodeConfiguration->getEntityType();
    }

    /**
     * @return ContextIdentifier
     */
    public function getContextIdentifier(): ContextIdentifier
    {
        $stepId = $this->treeNode->getId();
        $contextClass = ContentObjectPublication::class_name();
        $contextId = $this->learningPathEvaluationServiceBridge->getPublicationId();
        $learningPathStepContext = $this->learningPathStepContextService->getOrCreateLearningPathStepContext($stepId, $contextClass, $contextId);
        return new ContextIdentifier(get_class($learningPathStepContext), $learningPathStepContext->getId());
    }

    /**
     * @return bool
     */
    public function canEditEvaluation(): bool
    {
        return $this->learningPathEvaluationServiceBridge->canEditEvaluation();
    }

    /**
     * @return bool
     */
    public function getReleaseScores(): bool
    {
        return $this->treeNodeConfiguration->getReleaseScores();
    }

    /**
     * @return int[]
     */
    public function getTargetEntityIds(): array
    {
        $publicationId = $this->learningPathEvaluationServiceBridge->getPublicationId();
        return \Chamilo\Application\Weblcms\Storage\DataManager::getPublicationTargetUserIds($publicationId, null);
    }

    /**
     * @param int $entityType
     * @param int $entityId
     *
     * @return User[]
     */
    public function getUsersForEntity(int $entityType, int $entityId): array
    {
        // Todo: actually search by entity type
        return [$this->entityService->getUserForEntity($entityId)];
        /*$entityService = $this->entityServiceManager->getEntityServiceByType($this->getCurrentEntityType());

        return $entityService->getUsersForEntity($entityId);*/
    }

    /**
     * @param User $user
     * @param int $entityType
     * @param int $entityId
     *
     * @return bool
     */
    public function isUserPartOfEntity(User $user, int $entityType, int $entityId): bool
    {
        if ($entityType == 0 && $user->getId() == $entityId)
        {
            return true;
        }
        // todo: all other cases
        return false;

        //return $this->entityService->isUserPartOfEntity($user, $this->contentObjectPublication, $entityId);
    }

    /**
     * @param int $evaluationId
     * @param int $entityId
     * @return EvaluationEntry
     */
    public function createEvaluationEntryIfNotExists(int $evaluationId, int $entityId): EvaluationEntry
    {
        return $this->entityService->createEvaluationEntryIfNotExists($evaluationId, $this->getContextIdentifier(), $this->getCurrentEntityType(), $entityId);
    }

    /**
     * @param int $evaluationId
     * @param int $evaluatorId
     * @param int $entityId
     * @param int $score
     * @return EvaluationEntryScore
     */
    public function saveEntryScoreForEntity(int $evaluationId, int $evaluatorId, int $entityId, int $score): EvaluationEntryScore
    {
        return $this->entityService->createOrUpdateEvaluationEntryScoreForEntity($evaluationId, $evaluatorId, $this->getContextIdentifier(), $this->getCurrentEntityType(), $entityId, $score);
    }

    /**
     * @param int $evaluationId
     * @param int $evaluatorId
     * @param int $entityId
     * @return EvaluationEntryScore
     */
    public function saveEntityAsPresent(int $evaluationId, int $evaluatorId, int $entityId): EvaluationEntryScore
    {
        return $this->entityService->saveEntityAsPresent($evaluationId, $evaluatorId, $this->getContextIdentifier(), $this->getCurrentEntityType(), $entityId);
    }

    /**
     * @param int $evaluationId
     * @param int $evaluatorId
     * @param int $entityId
     * @return EvaluationEntryScore
     */
    public function saveEntityAsAbsent(int $evaluationId, int $evaluatorId, int $entityId): EvaluationEntryScore
    {
        return $this->entityService->saveEntityAsAbsent($evaluationId, $evaluatorId, $this->getContextIdentifier(), $this->getCurrentEntityType(), $entityId);
    }
}