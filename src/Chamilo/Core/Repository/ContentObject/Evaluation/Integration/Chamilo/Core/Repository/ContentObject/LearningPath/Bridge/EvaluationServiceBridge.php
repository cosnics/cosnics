<?php

namespace Chamilo\Core\Repository\ContentObject\Evaluation\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Bridge;

use Chamilo\Core\Repository\ContentObject\Evaluation\Display\Bridge\Interfaces\EmbeddedViewSupport;
use Chamilo\Core\Repository\ContentObject\Evaluation\Display\Bridge\Interfaces\EvaluationServiceBridgeInterface;
use Chamilo\Core\Repository\ContentObject\Evaluation\Display\Service\EvaluationEntryService;
use Chamilo\Core\Repository\ContentObject\Evaluation\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Bridge\Interfaces\LearningPathEvaluationServiceBridgeInterface;
use Chamilo\Core\Repository\ContentObject\Evaluation\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Domain\EvaluationConfiguration;
use Chamilo\Core\Repository\ContentObject\Evaluation\Storage\DataClass\Evaluation;
use Chamilo\Core\Repository\ContentObject\Evaluation\Storage\DataClass\EvaluationEntry;
use Chamilo\Core\Repository\ContentObject\Evaluation\Storage\DataClass\EvaluationEntryScore;
use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Attempt\TreeNodeAttempt;
use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\ContextIdentifier;

/**
 * Class EvaluationServiceBridge
 * @package Chamilo\Core\Repository\ContentObject\Evaluation\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Bridge
 */
class EvaluationServiceBridge implements EvaluationServiceBridgeInterface, EmbeddedViewSupport
{
    /**
     * @var EvaluationEntryService
     */
    protected $evaluationEntryService;

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
     * @param EvaluationEntryService $evaluationEntryService
     */
    public function __construct(LearningPathEvaluationServiceBridgeInterface $learningPathEvaluationServiceBridge, EvaluationEntryService $evaluationEntryService)
    {
        $this->learningPathEvaluationServiceBridge = $learningPathEvaluationServiceBridge;
        $this->evaluationEntryService = $evaluationEntryService;
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
     * @return int|null
     */
    public function getCurrentEntityIdentifier(User $currentUser): ?int
    {
        return $this->learningPathEvaluationServiceBridge->getCurrentEntityIdentifier($currentUser, $this->getCurrentEntityType());
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
        return $this->learningPathEvaluationServiceBridge->getContextIdentifier($this->treeNode->getId());
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
        return $this->learningPathEvaluationServiceBridge->getTargetEntityIds($this->getCurrentEntityType());
    }

    /**
     * @param int $entityId
     *
     * @return User[]
     */
    public function getUsersForEntity(int $entityId): array
    {
        return $this->learningPathEvaluationServiceBridge->getUsersForEntity($this->getCurrentEntityType(), $entityId);
    }

    /**
     * @param int $entityId
     * @return string
     */
    public function getEntityDisplayName(int $entityId): string
    {
        return $this->learningPathEvaluationServiceBridge->getEntityDisplayName($this->getCurrentEntityType(), $entityId);
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
        return $this->learningPathEvaluationServiceBridge->isUserPartOfEntity($user, $entityType, $entityId);
    }

    /**
     * @param int $evaluationId
     * @param int $entityId
     * @return EvaluationEntry
     */
    public function createEvaluationEntryIfNotExists(int $evaluationId, int $entityId): EvaluationEntry
    {
        return $this->evaluationEntryService->createEvaluationEntryIfNotExists($evaluationId, $this->getContextIdentifier(), $this->getCurrentEntityType(), $entityId);
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
        return $this->evaluationEntryService->createOrUpdateEvaluationEntryScoreForEntity($evaluationId, $evaluatorId, $this->getContextIdentifier(), $this->getCurrentEntityType(), $entityId, $score);
    }

    /**
     * @param int $evaluationId
     * @param int $evaluatorId
     * @param int $entityId
     * @return EvaluationEntryScore
     */
    public function saveEntityAsPresent(int $evaluationId, int $evaluatorId, int $entityId): EvaluationEntryScore
    {
        return $this->evaluationEntryService->saveEntityAsPresent($evaluationId, $evaluatorId, $this->getContextIdentifier(), $this->getCurrentEntityType(), $entityId);
    }

    /**
     * @param int $evaluationId
     * @param int $evaluatorId
     * @param int $entityId
     * @return EvaluationEntryScore
     */
    public function saveEntityAsAbsent(int $evaluationId, int $evaluatorId, int $entityId): EvaluationEntryScore
    {
        return $this->evaluationEntryService->saveEntityAsAbsent($evaluationId, $evaluatorId, $this->getContextIdentifier(), $this->getCurrentEntityType(), $entityId);
    }
}