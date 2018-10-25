<?php

namespace Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Interfaces;

use Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Feedback;
use Chamilo\Core\User\Storage\DataClass\User;

/**
 * Interface NotificationServiceBridge
 *
 * @package Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Interfaces
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
interface NotificationServiceBridgeInterface
{
    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @return int
     */
    public function countUnseenNotificationsForUser(User $user);

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param int $offset
     * @param int $count
     *
     * @return \Chamilo\Core\Notification\Storage\Entity\Notification[]
     */
    public function getNotificationsForUser(User $user, $offset = null, $count = null);

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry $entry
     */
    public function createNotificationForNewEntry(User $user, Entry $entry);

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry $entry
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Feedback $feedback
     */
    public function createNotificationForNewFeedback(User $user, Entry $entry, Feedback $feedback);
}