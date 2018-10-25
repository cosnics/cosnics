<?php

namespace Chamilo\Application\Weblcms\Bridge\Assignment;

use Chamilo\Application\Weblcms\Bridge\Assignment\Service\NotificationService;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Interfaces\NotificationServiceBridgeInterface;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Feedback;
use Chamilo\Core\Repository\ContentObject\Assignment\Storage\DataClass\Assignment;
use Chamilo\Core\User\Storage\DataClass\User;

/**
 * @package Chamilo\Application\Weblcms\Bridge\Assignment
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class NotificationServiceBridge implements NotificationServiceBridgeInterface
{
    /**
     * @var \Chamilo\Application\Weblcms\Bridge\Assignment\Service\NotificationService
     */
    protected $notificationService;

    /**
     * @var ContentObjectPublication
     */
    protected $contentObjectPublication;

    /**
     * AssignmentDataProvider constructor.
     *
     * @param \Chamilo\Application\Weblcms\Bridge\Assignment\Service\NotificationService $notificationService
     */
    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * @param ContentObjectPublication $contentObjectPublication
     */
    public function setContentObjectPublication(ContentObjectPublication $contentObjectPublication)
    {
        if (!$contentObjectPublication->getContentObject() instanceof Assignment)
        {
            throw new \RuntimeException(
                'The given content object publication does not reference a valid assignment and should not be used'
            );
        }

        $this->contentObjectPublication = $contentObjectPublication;
    }

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @return int
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function countUnseenNotificationsForUser(User $user)
    {
        return $this->notificationService->countUnseenNotificationsForUserAndPublication(
            $user, $this->contentObjectPublication
        );
    }

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param int $offset
     * @param int $count
     *
     * @return \Chamilo\Core\Notification\Storage\Entity\Notification[]
     */
    public function getNotificationsForUser(User $user, $offset = null, $count = null)
    {
        return $this->notificationService->getNotificationsForUserAndPublication(
            $user, $this->contentObjectPublication, $offset, $count
        );
    }

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry|\Chamilo\Application\Weblcms\Bridge\Assignment\Storage\DataClass\Entry $entry
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function createNotificationForNewEntry(User $user, Entry $entry)
    {
        $this->notificationService->createNotificationForNewEntry($user, $this->contentObjectPublication, $entry);
    }

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry|\Chamilo\Application\Weblcms\Bridge\Assignment\Storage\DataClass\Entry $entry
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Feedback|\Chamilo\Application\Weblcms\Bridge\Assignment\Storage\DataClass\Feedback $feedback
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function createNotificationForNewFeedback(User $user, Entry $entry, Feedback $feedback)
    {
        $this->notificationService->createNotificationForNewFeedback(
            $user, $this->contentObjectPublication, $entry, $feedback
        );
    }
}