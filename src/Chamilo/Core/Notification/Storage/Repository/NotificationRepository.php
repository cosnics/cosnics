<?php

namespace Chamilo\Core\Notification\Storage\Repository;

use Chamilo\Core\Notification\Storage\Entity\Notification;
use Chamilo\Core\Notification\Storage\Entity\UserNotification;
use Doctrine\ORM\EntityRepository;

/**
 * @package Chamilo\Core\Notification\Storage\Repository
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class NotificationRepository extends EntityRepository
{

    /**
     * @param \Chamilo\Core\Notification\Storage\Entity\Notification $notification
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function createNotification(Notification $notification)
    {
        $this->getEntityManager()->persist($notification);
        $this->getEntityManager()->flush();
    }

    /**
     * @param array $userNotifications
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function createUserNotifications($userNotifications = [])
    {
        foreach($userNotifications as $userNotification)
        {
            $this->getEntityManager()->persist($userNotification);
        }

        $this->getEntityManager()->flush();
    }
}