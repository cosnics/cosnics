<?php

namespace Chamilo\Core\Repository\ContentObject\Presence\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Bridge;

use Chamilo\Core\Repository\ContentObject\Presence\Display\Bridge\Interfaces\PresenceServiceBridgeInterface;
use Chamilo\Core\Repository\ContentObject\Presence\Display\Service\PresenceService;
use Chamilo\Core\Repository\ContentObject\Presence\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Bridge\Interfaces\LearningPathPresenceServiceBridgeInterface;
use Chamilo\Core\Repository\ContentObject\Presence\Storage\DataClass\Presence;
use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Attempt\TreeNodeAttempt;
use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode;
use Chamilo\Libraries\Architecture\ContextIdentifier;
use Chamilo\Core\Repository\ContentObject\LearningPath\Service\TreeNodeDataService;
use Chamilo\Libraries\Storage\FilterParameters\FilterParameters;

/**
 * Class PresenceServiceBridge
 * @package Chamilo\Core\Repository\ContentObject\Presence\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Bridge
 */
class PresenceServiceBridge implements PresenceServiceBridgeInterface
{
    /**
     * @var PresenceService
     */
    protected $presenceService;

    /**
     * @var LearningPathPresenceServiceBridgeInterface
     */
    protected $learningPathPresenceServiceBridge;

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
     * PresenceServiceBridge constructor.
     *
     * @param LearningPathPresenceServiceBridgeInterface $learningPathPresenceServiceBridge
     * @param PresenceService $presenceService
     * @param TreeNodeDataService $treeNodeDataService
     */
    public function __construct(LearningPathPresenceServiceBridgeInterface $learningPathPresenceServiceBridge, PresenceService $presenceService, TreeNodeDataService $treeNodeDataService)
    {
        $this->learningPathPresenceServiceBridge = $learningPathPresenceServiceBridge;
        $this->presenceService = $presenceService;
        $this->treeNodeDataService = $treeNodeDataService;
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode $treeNode
     */
    public function setTreeNode(TreeNode $treeNode)
    {
        if (!$treeNode->getContentObject() instanceof Presence)
        {
            throw new \RuntimeException(
                'The given treenode does not reference a valid presence and should not be used'
            );
        }

        $this->treeNode = $treeNode;
    }

    /**
     * @param TreeNodeAttempt $treeNodeAttempt
     */
    public function setTreeNodeAttempt(TreeNodeAttempt $treeNodeAttempt)
    {
        $this->treeNodeAttempt = $treeNodeAttempt;
    }

    /**
     * @return ContextIdentifier
     */
    public function getContextIdentifier(): ContextIdentifier
    {
        return $this->learningPathPresenceServiceBridge->getContextIdentifier($this->treeNode->getId());
    }

    /**
     * @return bool
     */
    public function canEditPresence(): bool
    {
        return $this->learningPathPresenceServiceBridge->canEditPresence();
    }

    /**
     * @param FilterParameters|null $filterParameters
     * @return int[]
     */
    public function getTargetUserIds(FilterParameters $filterParameters = null): array
    {
        return $this->learningPathPresenceServiceBridge->getTargetUserIds($filterParameters);
    }
}