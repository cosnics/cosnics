<?php

namespace Chamilo\Core\Notification\Service;

use Chamilo\Core\Notification\Domain\TranslationContext;
use Chamilo\Core\Notification\Storage\Entity\Filter;
use Chamilo\Core\Notification\Storage\Entity\Notification;
use Chamilo\Core\Notification\Storage\Entity\UserNotification;
use Chamilo\Core\Notification\Storage\Repository\NotificationRepository;
use Chamilo\Core\User\Storage\DataClass\User;

/**
 * @package Chamilo\Core\Notification\Service
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class NotificationManager
{
    /**
     * @var \Chamilo\Core\Notification\Storage\Repository\NotificationRepository
     */
    protected $notificationRepository;

    /**
     * @var NotificationTranslator
     */
    protected $notificationTranslator;

    /**
     * NotificationManager constructor.
     *
     * @param \Chamilo\Core\Notification\Storage\Repository\NotificationRepository $notificationRepository
     * @param NotificationTranslator $notificationTranslator
     */
    public function __construct(
        NotificationRepository $notificationRepository, NotificationTranslator $notificationTranslator
    )
    {
        $this->notificationRepository = $notificationRepository;
        $this->notificationTranslator = $notificationTranslator;
    }

    /**
     * @param string $url
     * @param \Chamilo\Core\Notification\Domain\ViewingContext[] $viewingContexts
     * @param \DateTime $date
     * @param array $targetUserIds
     * @param Filter[] $filters
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function createNotificationForUsers(
        $url, array $viewingContexts, $date, $targetUserIds = [], $filters = []
    )
    {
        $notification = new Notification();

        foreach ($filters as $filter)
        {
            if (!$filter instanceof Filter)
            {
                throw new \InvalidArgumentException(
                    sprintf('The given filters should be an instance of Filter, %s given', get_class($filter))
                );
            }
        }

        $notification->setUrl($url)
            ->setDescriptionContext(
                $this->notificationTranslator->createNotificationDescriptionContext($viewingContexts)
            )
            ->setDate($date)
            ->setFilters($filters);

        $this->notificationRepository->createNotification($notification);

        $userNotifications = [];
        foreach ($targetUserIds as $targetUserId)
        {
            $userNotification = new UserNotification();
            $userNotification->setNotification($notification)
                ->setUserId($targetUserId)
                ->setRead(false);

            $userNotifications[] = $userNotification;
        }

        $this->notificationRepository->createUserNotifications($userNotifications);
    }

    /**
     * @param \Chamilo\Core\Notification\Storage\Entity\Filter $filter
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @return Notification[]
     */
    public function getNotificationsByFilterForUser(Filter $filter, User $user)
    {
        return $this->notificationRepository->findNotificationsByFilterForUser($filter, $user);
    }

    /**
     * @param \Chamilo\Core\Notification\Storage\Entity\Filter $filter
     * @param \Chamilo\Core\User\Storage\DataClass\User|null $user
     *
     * * @return Notification[]
     */
    public function getNotificationsByFilter(Filter $filter, User $user = null)
    {
        return $this->notificationRepository->findNotificationsByFilter($filter, $user);
    }
}