<?php

namespace Chamilo\Core\Notification\Storage\Repository;

use Chamilo\Core\Notification\Storage\Entity\Filter;
use Chamilo\Core\Notification\Storage\Entity\Notification;
use Chamilo\Core\Notification\Storage\Entity\UserNotification;
use Chamilo\Core\User\Storage\DataClass\User;
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

    /**
     * @param \Chamilo\Core\Notification\Storage\Entity\Filter $filter
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @return Notification[]
     */
    public function findNotificationsByFilterForUser(Filter $filter, User $user)
    {
        $queryBuilder = $this->getEntityManager()->createQueryBuilder()
            ->select('notification')
            ->select('filter')
            ->select('user')
            ->from(UserNotification::class, 'user')
            ->join('user.notifications', 'notification')
            ->join('notification.filters', 'filter')
            ->where('filter.id = :filterId')
            ->andWhere('user.userId =:userId')
            ->setParameter('filterId', $filter->getId())
            ->setParameter('userId', $user->getId());

        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * @param \Chamilo\Core\Notification\Storage\Entity\Filter $filter
     *
     * * @return Notification[]
     */
    public function findNotificationsByFilter(Filter $filter)
    {
        $queryBuilder = $this->getEntityManager()->createQueryBuilder()
            ->select('notification')
            ->select('filter')
            ->select('user')
            ->from(Filter::class, 'filter')
            ->join('filter.notifications', 'notification')
            ->where('filter.id = :filterId')
            ->setParameter('filterId', $filter->getId());

        return $queryBuilder->getQuery()->getResult();
    }
}