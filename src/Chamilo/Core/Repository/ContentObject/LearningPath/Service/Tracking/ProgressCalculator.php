<?php

namespace Chamilo\Core\Repository\ContentObject\LearningPath\Service\Tracking;

use Chamilo\Core\Repository\ContentObject\Assessment\Storage\DataClass\Assessment;
use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Attempt\TreeNodeAttempt;
use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode;
use Chamilo\Core\Repository\ContentObject\LearningPath\Service\AttemptService;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\LearningPath;
use Chamilo\Core\User\Storage\DataClass\User;

/**
 * Service to calculate the progress / completion of the learning path or individual nodes
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class ProgressCalculator
{
    /**
     * @var AttemptService
     */
    protected $attemptService;

    /**
     * @var bool[]
     */
    protected $treeNodesCompletedCache;

    /**
     * ProgressCalculator constructor.
     *
     * @param AttemptService $attemptService
     */
    public function __construct(AttemptService $attemptService)
    {
        $this->attemptService = $attemptService;
        $this->treeNodesCompletedCache = [];
    }

    /**
     * Returns the progress for a given user in a given learning path
     *
     * @param LearningPath $learningPath
     * @param User $user
     * @param TreeNode $treeNode
     *
     * @return int
     */
    public function getLearningPathProgress(
        LearningPath $learningPath, User $user, TreeNode $treeNode
    )
    {
        /** @var TreeNode[] $nodes */
        $nodes = array();
        $nodes[] = $treeNode;
        $nodes = array_merge($nodes, $treeNode->getDescendantNodes());

        $nodesCompleted = 0;

        foreach ($nodes as $node)
        {
            if ($this->isTreeNodeCompleted($learningPath, $user, $node))
            {
                $nodesCompleted ++;
            }
        }

        $progress = (int) floor(($nodesCompleted / count($nodes)) * 100);

        return $progress > 100 ? 100 : $progress;
    }

    /**
     * Checks if a given learning path tree node is completed
     *
     * @param LearningPath $learningPath
     * @param User $user
     * @param TreeNode $treeNode
     *
     * @return bool
     */
    public function isTreeNodeCompleted(
        LearningPath $learningPath, User $user, TreeNode $treeNode
    )
    {
        $cacheKey = md5($learningPath->getId() . ':' . $user->getId() . ':' . $treeNode->getId());

        if (!array_key_exists($cacheKey, $this->treeNodesCompletedCache))
        {
            $this->treeNodesCompletedCache[$cacheKey] =
                $this->calculateTreeNodeCompleted($learningPath, $user, $treeNode);
        }

        return $this->treeNodesCompletedCache[$cacheKey];
    }

    /**
     * Returns whether or not the given TreeNode is blocked for the given user
     *
     * @param LearningPath $learningPath
     * @param User $user
     * @param TreeNode $treeNode
     *
     * @return bool
     */
    public function isCurrentTreeNodeBlocked(
        LearningPath $learningPath, User $user, TreeNode $treeNode
    )
    {
        $previousNodes = $treeNode->getPreviousNodes();

        foreach ($previousNodes as $previousNode)
        {
            if ($this->doesNodeBlockCurrentNode($learningPath, $user, $treeNode, $previousNode))
            {
                return true;
            }
        }

        return false;
    }

    /**
     * Returns a list of the nodes that are responsible that a step can not be taken
     *
     * @param LearningPath $learningPath
     * @param User $user
     * @param TreeNode $treeNode
     *
     * @return TreeNode[]
     */
    public function getResponsibleNodesForBlockedTreeNode(
        LearningPath $learningPath, User $user, TreeNode $treeNode
    )
    {
        $previousNodes = $treeNode->getPreviousNodes();

        $blockedNodes = array();

        foreach ($previousNodes as $previousNode)
        {
            if ($this->doesNodeBlockCurrentNode($learningPath, $user, $treeNode, $previousNode))
            {
                $blockedNodes[] = $previousNode;
            }
        }

        return $blockedNodes;
    }

    /**
     * Helper function to check whether or not the
     *
     * @param LearningPath $learningPath
     * @param User $user
     * @param TreeNode $currentTreeNode
     * @param TreeNode $possibleBlockNode
     *
     * @return bool
     */
    protected function doesNodeBlockCurrentNode(
        LearningPath $learningPath, User $user, TreeNode $currentTreeNode,
        TreeNode $possibleBlockNode
    )
    {
        if ($currentTreeNode->isChildOf($possibleBlockNode))
        {
            return false;
        }

        if (
            $learningPath->enforcesDefaultTraversingOrder() ||
            (!$possibleBlockNode->isRootNode() && $possibleBlockNode->getTreeNodeData()->isBlocked())
        )
        {
            if (!$this->isTreeNodeCompleted($learningPath, $user, $possibleBlockNode))
            {
                return true;
            }
        }

        return false;
    }


    /**
     * Determines whether or not the learning path tree node is completed by checking the tracking and every subitem
     *
     * @param LearningPath $learningPath
     * @param User $user
     * @param TreeNode $treeNode
     *
     * @return bool
     */
    protected function calculateTreeNodeCompleted(
        LearningPath $learningPath, User $user, TreeNode $treeNode
    )
    {
        $treeNodeAttempts = $this->attemptService->getTreeNodeAttempts($learningPath, $user);

        if ($treeNode->hasChildNodes())
        {
            $completed = true;

            foreach ($treeNode->getChildNodes() as $childTreeNode)
            {
                $completed &= $this->isTreeNodeCompleted(
                    $learningPath, $user, $childTreeNode
                );
            }

            if (!$completed)
            {
                return false;
            }
        }

        /** @var TreeNodeAttempt[] $treeNodeAttempts */
        $treeNodeAttempts = $treeNodeAttempts[$treeNode->getId()];

        foreach ($treeNodeAttempts as $treeNodeAttempt)
        {
            if($this->isAttemptCompleted($treeNode, $treeNodeAttempt))
            {
                return true;
            }
        }

        return false;
    }

    /**
     * Checks whether or not a given attempt for a treenode is completed
     *
     * @param TreeNode $treeNode
     * @param TreeNodeAttempt $treeNodeAttempt
     *
     * @return bool
     */
    protected function isAttemptCompleted(TreeNode $treeNode, TreeNodeAttempt $treeNodeAttempt)
    {
        $isAssessment = $treeNode->getContentObject() instanceof Assessment;
        $masteryScore = $treeNode->getTreeNodeData()->getMasteryScore();

        if (!$treeNodeAttempt->isCompleted())
        {
            return false;
        }

        if(!$isAssessment)
        {
            return true;
        }

        return $treeNodeAttempt->get_score() >= $masteryScore;
    }
}