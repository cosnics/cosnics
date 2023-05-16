<?php
namespace Chamilo\Core\Notification\Storage\Repository;

use Chamilo\Core\Notification\Storage\Entity\Filter;
use Chamilo\Core\Notification\Storage\Entity\Notification;
use Chamilo\Core\Notification\Storage\Entity\UserNotification;
use Chamilo\Core\User\Storage\DataClass\User;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\ORM\Query\ResultSetMappingBuilder;

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
     * @param UserNotification[] $userNotifications
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function createUserNotifications($userNotifications = [])
    {
        foreach ($userNotifications as $userNotification)
        {
            $this->getEntityManager()->persist($userNotification);
        }

        $this->getEntityManager()->flush();
    }

    /**
     * @param \Chamilo\Core\Notification\Storage\Entity\NotificationContext[] $contexts
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param int $offset
     * @param int $count
     *
     * @return UserNotification[]
     */
    public function findUserNotificationsByContextsForUser(array $contexts, User $user, $offset = null, $count = null)
    {
        $queryBuilder = $this->getEntityManager()->createQueryBuilder()
            ->select('notification')
            ->addSelect('user')
            ->from(UserNotification::class, 'user')
            ->join('user.notification', 'notification')
            ->where('user.notificationContext IN (:contexts)')
            ->andWhere('user.userId =:userId')
            ->setParameter('contexts', $contexts)
            ->setParameter('userId', $user->getId())
            ->orderBy('user.date', 'DESC');

        if ($offset)
        {
            $queryBuilder->setFirstResult($offset);
        }

        if ($count)
        {
            $queryBuilder->setMaxResults($count);
        }

        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * @param array $contexts
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @return int
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function countUnseenNotificationsByContextsForUser(array $contexts, User $user)
    {
        $queryBuilder = $this->getEntityManager()->createQueryBuilder()
            ->select('COUNT(user.notification)')
            ->from(UserNotification::class, 'user')
            ->where('user.notificationContext IN (:contexts)')
            ->andWhere('user.userId =:userId')
            ->andWhere('user.viewed = false')
            ->setParameter('contexts', $contexts)
            ->setParameter('userId', $user->getId());

        return $queryBuilder->getQuery()->getSingleScalarResult();
    }

    /**
     * @param \Chamilo\Core\Notification\Storage\Entity\NotificationContext[] $contexts
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     */
    public function setNotificationsViewedForUserAndContexts(array $contexts, User $user)
    {
        $query = $this->getEntityManager()
            ->createQuery(
                'UPDATE ' . UserNotification::class .
                ' UN SET UN.viewed = true WHERE UN.userId = :userId AND UN.notificationContext IN (:contexts) AND UN.viewed = false'
            )
            ->setParameter('userId', $user->getId())
            ->setParameter('contexts', $contexts);

        $query->execute();
    }

    /**
     * @param Notification[] $notifications
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     */
    public function setNotificationsViewedForUser($notifications, User $user)
    {
        $query = $this->getEntityManager()
            ->createQuery(
                'UPDATE ' . UserNotification::class .
                ' UN SET UN.viewed = true WHERE UN.userId = :userId AND UN.notification IN (:notifications) AND UN.viewed = false'
            )
            ->setParameter('userId', $user->getId())
            ->setParameter('notifications', $notifications);

        $query->execute();
    }

    /**
     * @param \Chamilo\Core\Notification\Storage\Entity\Notification $notification
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     */
    public function setNotificationReadForUser(Notification $notification, User $user)
    {
        $query = $this->getEntityManager()
            ->createQuery(
                'UPDATE ' . UserNotification::class .
                ' UN SET UN.read = true, UN.viewed = true WHERE UN.userId = :userId AND UN.notification = :notification AND UN.read = false'
            )
            ->setParameter('userId', $user->getId())
            ->setParameter('notification', $notification);

        $query->execute();
    }

    /**
     * @param \Chamilo\Core\Notification\Storage\Entity\Notification $notification
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @return int
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function countUserNotificationsByNotificationAndUser(Notification $notification, User $user)
    {
        $queryBuilder = $this->getEntityManager()->createQueryBuilder()
            ->select('count(user.id)')
            ->from(UserNotification::class, 'user')
            ->where('user.notification = :notification')
            ->andWhere('user.userId = :userId')
            ->setParameter('notification', $notification)
            ->setParameter('userId', $user->getId());

        return $queryBuilder->getQuery()->getSingleScalarResult();
    }
}