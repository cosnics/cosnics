<?php

namespace Chamilo\Core\Repository\ContentObject\LearningPath\Display\Preview\Bridge\Assignment;

use Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\EntryAttachment;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Feedback;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Score;
use Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Bridge\Interfaces\NotificationServiceBridgeInterface;
use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode;
use Chamilo\Core\User\Storage\DataClass\User;

/**
 * @package Chamilo\Core\Repository\ContentObject\Assignment\Display\Preview\Bridge
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class NotificationServiceBridge implements NotificationServiceBridgeInterface
{
    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode $treeNode
     *
     * @return int
     */
    public function countUnseenNotificationsForUserAndTreeNode(User $user, TreeNode $treeNode)
    {
        // TODO: Implement countUnseenNotificationsForUserAndTreeNode() method.
    }

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode $treeNode
     * @param int $offset
     * @param int $count
     *
     * @return \Chamilo\Core\Notification\Domain\NotificationDTO[]
     */
    public function getNotificationsForUserAndTreeNode(User $user, TreeNode $treeNode, $offset = null, $count = null)
    {
        // TODO: Implement getNotificationsForUserAndTreeNode() method.
    }

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode $treeNode
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Bridge\Storage\DataClass\Entry $entry
     */
    public function createNotificationForNewEntry(
        User $user, TreeNode $treeNode,
        \Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Bridge\Storage\DataClass\Entry $entry
    )
    {
        // TODO: Implement createNotificationForNewEntry() method.
    }

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode $treeNode
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Bridge\Storage\DataClass\Entry $entry
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Bridge\Storage\DataClass\Feedback $feedback
     */
    public function createNotificationForNewFeedback(
        User $user, TreeNode $treeNode,
        \Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Bridge\Storage\DataClass\Entry $entry,
        \Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Bridge\Storage\DataClass\Feedback $feedback
    )
    {
        // TODO: Implement createNotificationForNewFeedback() method.
    }

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode $treeNode
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Bridge\Storage\DataClass\Entry $entry
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Bridge\Storage\DataClass\Score $score
     */
    public function createNotificationForNewScore(
        User $user, TreeNode $treeNode,
        \Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Bridge\Storage\DataClass\Entry $entry,
        \Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Bridge\Storage\DataClass\Score $score
    )
    {
        // TODO: Implement createNotificationForNewScore() method.
    }

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode $treeNode
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Bridge\Storage\DataClass\Entry $entry
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Bridge\Storage\DataClass\EntryAttachment $entryAttachment
     */
    public function createNotificationForNewEntryAttachment(
        User $user, TreeNode $treeNode,
        \Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Bridge\Storage\DataClass\Entry $entry,
        \Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Bridge\Storage\DataClass\EntryAttachment $entryAttachment
    )
    {
        // TODO: Implement createNotificationForNewEntryAttachment() method.
    }
}
