<?php

namespace Chamilo\Application\Weblcms\Bridge\LearningPath\Assignment;

use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Service\LearningPathAssignmentService;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Entry;
use Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Bridge\Interfaces\FeedbackServiceBridgeInterface;
use Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Bridge\Interfaces\LearningPathAssignmentFeedbackDataManagerInterface;
use Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\Feedback;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\TreeNodeData;
use Chamilo\Core\User\Storage\DataClass\User;

class FeedbackServiceBridge implements FeedbackServiceBridgeInterface
{
    /**
     * @var \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Service\LearningPathAssignmentService
     */
    protected $learningPathAssignmentService;

    /**
     * FeedbackDataManager constructor.
     *
     * @param \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Service\LearningPathAssignmentService $learningPathService
     */
    public function __construct(LearningPathAssignmentService $learningPathService)
    {
        $this->learningPathAssignmentService = $learningPathService;
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\TreeNodeData $treeNodeData
     * @param integer $entityType
     *
     * @return integer
     */
    public function countDistinctFeedbackByTreeNodeDataAndEntityType(TreeNodeData $treeNodeData, $entityType)
    {
        return $this->learningPathAssignmentService->countDistinctFeedbackByTreeNodeDataAndEntityType(
            $treeNodeData, $entityType
        );
    }

    /**
     * @param TreeNodeData $treeNodeData
     * @param integer $entityType
     * @param integer $entityId
     *
     * @return integer
     */
    public function countFeedbackForTreeNodeDataByEntityTypeAndEntityId(
        TreeNodeData $treeNodeData, $entityType, $entityId
    )
    {
        return $this->learningPathAssignmentService->countFeedbackForTreeNodeDataByEntityTypeAndEntityId(
            $treeNodeData, $entityType, $entityId
        );
    }

    /**
     * @param TreeNodeData $treeNodeData
     * @param integer $entityType
     * @param integer $entityId
     *
     * @return integer
     */
    public function countDistinctFeedbackForTreeNodeDataEntityTypeAndId(
        TreeNodeData $treeNodeData, $entityType,
        $entityId
    )
    {
        return $this->learningPathAssignmentService->countDistinctFeedbackForTreeNodeDataEntityTypeAndId(
            $treeNodeData, $entityType, $entityId
        );
    }

    /**
     * @param Entry $entry
     *
     * @return \Chamilo\Libraries\Storage\Iterator\DataClassIterator | \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\Assignment\Feedback[]
     */
    public function getFeedbackByEntry(Entry $entry)
    {
        return $this->learningPathAssignmentService->findFeedbackByEntry($entry);
    }

    /**
     * @param integer $feedbackIdentifier
     *
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Feedback
     */
    public function getFeedbackByIdentifier($feedbackIdentifier)
    {
        return $this->learningPathAssignmentService->findFeedbackByIdentifier($feedbackIdentifier);
    }

    /**
     * @param Entry $entry
     *
     * @return integer
     */
    public function countFeedbackByEntry(Entry $entry)
    {
        return $this->learningPathAssignmentService->countFeedbackByEntry($entry);
    }

    /**
     * @param integer $entryIdentifier
     *
     * @return integer
     */
    public function countFeedbackByEntryIdentifier($entryIdentifier)
    {
        return $this->learningPathAssignmentService->countFeedbackByEntryIdentifier($entryIdentifier);
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
        $feedbackObject = new \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\LearningPath\Assignment\Feedback();
        $feedbackObject->setEntryId($entry->getId());

        $feedbackObject->set_user_id($user->getId());
        $feedbackObject->set_comment($feedback);
        $feedbackObject->set_creation_date(time());
        $feedbackObject->set_modification_date(time());

        if(!$feedbackObject->create())
        {
            throw new \RuntimeException('Could not create feedback in the database');
        }

        return $feedbackObject;
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\Feedback $feedback
     *
     * @throws \Exception
     */
    public function updateFeedback(Feedback $feedback)
    {
        if(!$feedback->update())
        {
            throw new \RuntimeException('Could not update feedback in the database');
        }
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\Feedback $feedback
     */
    public function deleteFeedback(Feedback $feedback)
    {
        if(!$feedback->delete())
        {
            throw new \RuntimeException('Could not delete feedback in the database');
        }
    }
}