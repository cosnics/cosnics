<?php

namespace Chamilo\Core\Notification\Service;

use Chamilo\Core\Notification\Storage\Entity\Filter;
use Chamilo\Core\Notification\Storage\Entity\Notification;
use Chamilo\Core\Notification\Storage\Entity\UserNotification;

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
     * @param string $url
     * @param array $descriptionContext
     * @param \DateTime $date
     * @param array $targetUserIds
     * @param Filter[] $filters
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function createNotificationForUsers($url, $descriptionContext, $date, $targetUserIds = [], $filters = [])
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
            ->setDescriptionContext(json_encode($descriptionContext))
            ->setDate($date)
            ->setFilters($filters);

        $this->notificationRepository->createNotification($notification);

        $userNotifications = [];
        foreach($targetUserIds as $targetUserId)
        {
            $userNotification = new UserNotification();
            $userNotification->setNotification($notification)
                    ->setUserId($targetUserId)
                    ->setRead(false);

            $userNotifications[] = $userNotification;
        }

        $this->notificationRepository->createUserNotifications($userNotifications);
    }
}