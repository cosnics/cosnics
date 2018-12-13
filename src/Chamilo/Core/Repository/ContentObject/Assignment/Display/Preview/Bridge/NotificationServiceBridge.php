<?php

namespace Chamilo\Core\Repository\ContentObject\Assignment\Display\Preview\Bridge;

use Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\EntryAttachment;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Feedback;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Score;
use Chamilo\Core\User\Storage\DataClass\User;

/**
 * @package Chamilo\Core\Repository\ContentObject\Assignment\Display\Preview\Bridge
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class NotificationServiceBridge implements \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Interfaces\NotificationServiceBridgeInterface
{

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @return int
     */
    public function countUnseenNotificationsForUser(User $user)
    {
        // TODO: Implement countUnseenNotificationsForUser() method.
    }

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param int $offset
     * @param int $count
     *
     * @return \Chamilo\Core\Notification\Domain\NotificationDTO[]
     */
    public function getNotificationsForUser(User $user, $offset = null, $count = null)
    {
        // TODO: Implement getNotificationsForUser() method.
    }

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry $entry
     */
    public function createNotificationForNewEntry(User $user, Entry $entry)
    {
        // TODO: Implement createNotificationForNewEntry() method.
    }

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry $entry
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Feedback $feedback
     */
    public function createNotificationForNewFeedback(User $user, Entry $entry, Feedback $feedback)
    {
        // TODO: Implement createNotificationForNewFeedback() method.
    }

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry $entry
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Score $score
     */
    public function createNotificationForNewScore(User $user, Entry $entry, Score $score)
    {
        // TODO: Implement createNotificationForNewScore() method.
    }

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry $entry
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\EntryAttachment $entryAttachment
     */
    public function createNotificationForNewEntryAttachment(User $user, Entry $entry, EntryAttachment $entryAttachment)
    {
        // TODO: Implement createNotificationForNewEntryAttachment() method.
    }
}