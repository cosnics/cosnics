<?php

namespace Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Bridge;

use Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Feedback;
use Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Bridge\Interfaces\FeedbackServiceBridgeInterface;
use Chamilo\Core\Repository\ContentObject\Assignment\Storage\DataClass\Assignment;
use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode;
use Chamilo\Core\User\Storage\DataClass\User;
use RuntimeException;

/**
 * @package Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Bridge
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class FeedbackServiceBridge implements \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Interfaces\FeedbackServiceBridgeInterface
{

    /**
     * @var TreeNode
     */
    protected $treeNode;

    /**
     * @var \Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Bridge\Interfaces\FeedbackServiceBridgeInterface
     */
    protected $learningPathAssignmentFeedbackServiceBridge;

    /**
     * FeedbackServiceBridge constructor.
     *
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Bridge\Interfaces\FeedbackServiceBridgeInterface $learningPathAssignmentFeedbackServiceBridge
     */
    public function __construct(FeedbackServiceBridgeInterface $learningPathAssignmentFeedbackServiceBridge)
    {
        $this->learningPathAssignmentFeedbackServiceBridge = $learningPathAssignmentFeedbackServiceBridge;
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode $treeNode
     */
    public function setTreeNode(TreeNode $treeNode)
    {
        if (!$treeNode->getContentObject() instanceof Assignment)
        {
            throw new RuntimeException(
                'The given treenode does not reference a valid assignment and should not be used'
            );
        }

        $this->treeNode = $treeNode;
    }

    /**
     * @param integer $entityType
     *
     * @return integer
     */
    public function countDistinctFeedbackByEntityType($entityType)
    {
        return $this->learningPathAssignmentFeedbackServiceBridge->countDistinctFeedbackByTreeNodeDataAndEntityType(
            $this->treeNode->getTreeNodeData(), $entityType
        );
    }

    /**
     * @param integer $entityType
     * @param integer $entityId
     *
     * @return int
     */
    public function countFeedbackByEntityTypeAndEntityId($entityType, $entityId)
    {
        return $this->learningPathAssignmentFeedbackServiceBridge->countFeedbackForTreeNodeDataByEntityTypeAndEntityId(
            $this->treeNode->getTreeNodeData(), $entityType, $entityId
        );
    }

    /**
     * @param integer $entityType
     * @param integer $entityId
     *
     * @return integer
     */
    public function countDistinctFeedbackForEntityTypeAndId($entityType, $entityId)
    {
        return $this->learningPathAssignmentFeedbackServiceBridge->countDistinctFeedbackForTreeNodeDataEntityTypeAndId(
            $this->treeNode->getTreeNodeData(), $entityType, $entityId
        );
    }

    /**
     * @param integer $entryIdentifier
     *
     * @return integer
     */
    public function countFeedbackByEntryIdentifier($entryIdentifier)
    {
        return $this->learningPathAssignmentFeedbackServiceBridge->countFeedbackByEntryIdentifier($entryIdentifier);
    }

    /**
     * @param integer $feedbackIdentifier
     *
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Feedback
     */
    public function getFeedbackByIdentifier($feedbackIdentifier)
    {
        return $this->learningPathAssignmentFeedbackServiceBridge->getFeedbackByIdentifier($feedbackIdentifier);
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry $entry
     *
     * @return integer
     */
    public function countFeedbackByEntry(Entry $entry)
    {
        return $this->learningPathAssignmentFeedbackServiceBridge->countFeedbackByEntry($entry);
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry $entry
     *
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Feedback[]
     */
    public function getFeedbackByEntry(Entry $entry)
    {
        return $this->learningPathAssignmentFeedbackServiceBridge->getFeedbackByEntry($entry);
    }

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param string $feedback
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry $entry
     *
     * @return \Chamilo\Core\Repository\Feedback\Storage\DataClass\Feedback
     */
    public function createFeedback(User $user, $feedback, Entry $entry)
    {
        return $this->learningPathAssignmentFeedbackServiceBridge->createFeedback($user, $feedback, $entry);
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Feedback|\Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Bridge\Storage\DataClass\Feedback $feedback
     */
    public function updateFeedback(Feedback $feedback)
    {
        $this->learningPathAssignmentFeedbackServiceBridge->updateFeedback($feedback);
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Feedback|\Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Bridge\Storage\DataClass\Feedback $feedback
     */
    public function deleteFeedback(Feedback $feedback)
    {
        $this->learningPathAssignmentFeedbackServiceBridge->deleteFeedback($feedback);
    }
}