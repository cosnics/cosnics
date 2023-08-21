<?php

namespace Chamilo\Core\Repository\ContentObject\Evaluation\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Bridge;

use Chamilo\Core\Repository\ContentObject\Evaluation\Display\Bridge\Interfaces\EmbeddedViewSupport;
use Chamilo\Core\Repository\ContentObject\Evaluation\Display\Bridge\Interfaces\EvaluationServiceBridgeInterface;
use Chamilo\Core\Repository\ContentObject\Evaluation\Display\Service\EvaluationEntryService;
use Chamilo\Core\Repository\ContentObject\Evaluation\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Bridge\Interfaces\LearningPathEvaluationServiceBridgeInterface;
use Chamilo\Core\Repository\ContentObject\Evaluation\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Domain\EvaluationConfiguration;
use Chamilo\Core\Repository\ContentObject\Evaluation\Storage\DataClass\Evaluation;
use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Attempt\TreeNodeAttempt;
use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\ContextIdentifier;
use Chamilo\Core\Repository\ContentObject\LearningPath\Service\TreeNodeDataService;

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
     * @var TreeNodeDataService
     */
    protected $treeNodeDataService;

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
     * @param TreeNodeDataService $treeNodeDataService
     */
    public function __construct(LearningPathEvaluationServiceBridgeInterface $learningPathEvaluationServiceBridge, EvaluationEntryService $evaluationEntryService, TreeNodeDataService $treeNodeDataService)
    {
        $this->learningPathEvaluationServiceBridge = $learningPathEvaluationServiceBridge;
        $this->evaluationEntryService = $evaluationEntryService;
        $this->treeNodeDataService = $treeNodeDataService;
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
    public function getOpenForStudents(): bool
    {
        return $this->treeNodeConfiguration->getOpenForStudents();
    }

    /**
     * @param bool $openForStudents
     */
    public function setOpenForStudents(bool $openForStudents)
    {
        $this->treeNodeConfiguration->setOpenForStudents($openForStudents);
        $this->treeNode->setConfiguration($this->treeNodeConfiguration);
        $this->treeNodeDataService->storeConfigurationForTreeNode($this->treeNode);
    }

    /**
     * @return bool
     */
    public function getSelfEvaluationAllowed(): bool
    {
        return $this->treeNodeConfiguration->getSelfEvaluationAllowed();
    }

    /**
     * @param bool $selfEvaluationAllowed
     */
    public function setSelfEvaluationAllowed(bool $selfEvaluationAllowed)
    {
        $this->treeNodeConfiguration->setSelfEvaluationAllowed($selfEvaluationAllowed);
        $this->treeNode->setConfiguration($this->treeNodeConfiguration);
        $this->treeNodeDataService->storeConfigurationForTreeNode($this->treeNode);
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
     * @param User $currentUser
     *
     * @return int[]
     */
    public function getAvailableEntityIdentifiersForUser(User $currentUser): array
    {
        return $this->learningPathEvaluationServiceBridge->getAvailableEntityIdentifiersForUser(
            $currentUser, $this->getCurrentEntityType()
        );
    }

    /**
     * @param int $entityType
     * @param int $entityId
     *
     * @return String
     */
    public function renderEntityNameByEntityTypeAndEntityId($entityType, $entityId)
    {
        return $this->learningPathEvaluationServiceBridge->renderEntityNameByEntityTypeAndEntityId(
            $entityType, $entityId
        );
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
     *
     * @param int $entityType
     *
     * @return string
     */
    public function getPluralEntityNameByType($entityType)
    {
        return $this->learningPathEvaluationServiceBridge->getPluralEntityNameByType($entityType);
    }

    /**
     * @param $entityType
     *
     * @return mixed
     */
    public function getEntityNameByType($entityType)
    {
        return $this->learningPathEvaluationServiceBridge->getEntityNameByType($entityType);
    }

    /**
     * @return bool
     */
    public function canUseAns(): bool
    {
        return $this->learningPathEvaluationServiceBridge->canUseAns();
    }
}