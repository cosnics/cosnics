<?php

namespace Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Bridge\Feedback;

use Chamilo\Core\Repository\ContentObject\Assignment\Bridge\Feedback\AssignmentFeedbackDataManagerInterface;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Entry;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Feedback;
use Chamilo\Core\Repository\ContentObject\Assignment\Storage\DataClass\Assignment;
use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode;
use Chamilo\Core\User\Storage\DataClass\User;

/**
 * @package Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Bridge
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class AssignmentFeedbackDataManager implements AssignmentFeedbackDataManagerInterface
{

    /**
     * @var TreeNode
     */
    protected $treeNode;

    /**
     * @var \Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Bridge\Feedback\LearningPathAssignmentFeedbackDataManagerInterface
     */
    protected $learningPathAssignmentFeedbackDataManager;

    /**
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode $treeNode
     */
    public function setTreeNode(TreeNode $treeNode)
    {
        if (!$treeNode->getContentObject() instanceof Assignment)
        {
            throw new \RuntimeException(
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
        return $this->learningPathAssignmentFeedbackDataManager->countDistinctFeedbackByTreeNodeDataAndEntityType(
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
        return $this->learningPathAssignmentFeedbackDataManager->countFeedbackForTreeNodeDataByEntityTypeAndEntityId(
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
        return $this->learningPathAssignmentFeedbackDataManager->countDistinctFeedbackForTreeNodeDataEntityTypeAndId(
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
        return $this->learningPathAssignmentFeedbackDataManager->countFeedbackByEntryIdentifier($entryIdentifier);
    }

    /**
     * @param integer $feedbackIdentifier
     *
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Feedback
     */
    public function getFeedbackByIdentifier($feedbackIdentifier)
    {
        return $this->learningPathAssignmentFeedbackDataManager->getFeedbackByIdentifier($feedbackIdentifier);
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Entry $entry
     *
     * @return integer
     */
    public function countFeedbackByEntry(Entry $entry)
    {
        return $this->learningPathAssignmentFeedbackDataManager->countFeedbackByEntry($entry);
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Entry $entry
     *
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Feedback[]
     */
    public function getFeedbackByEntry(Entry $entry)
    {
        return $this->learningPathAssignmentFeedbackDataManager->getFeedbackByEntry($entry);
    }

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param string $feedback
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Entry $entry
     *
     * @return \Chamilo\Core\Repository\Feedback\Storage\DataClass\Feedback
     */
    public function createFeedback(User $user, $feedback, Entry $entry)
    {
        return $this->learningPathAssignmentFeedbackDataManager->createFeedback($user, $feedback, $entry);
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Feedback|\Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\Feedback $feedback
     */
    public function updateFeedback(Feedback $feedback)
    {
        $this->learningPathAssignmentFeedbackDataManager->updateFeedback($feedback);
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Feedback|\Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\Feedback $feedback
     */
    public function deleteFeedback(Feedback $feedback)
    {
        $this->learningPathAssignmentFeedbackDataManager->deleteFeedback($feedback);
    }
}