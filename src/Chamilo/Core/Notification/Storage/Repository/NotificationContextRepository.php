<?php

namespace Chamilo\Core\Notification\Storage\Repository;

use Chamilo\Core\Notification\Storage\Entity\NotificationContext;
use Doctrine\ORM\EntityRepository;

/**
 * @package Chamilo\Core\Notification\Storage\Repository
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class NotificationContextRepository extends EntityRepository
{
    /**
     * @param string $filterPath
     *
     * @return \Chamilo\Core\Notification\Storage\Entity\NotificationContext|object
     */
    public function findByPath($filterPath)
    {
        return $this->findOneBy(['path' => $filterPath]);
    }

    /**
     * @param \Chamilo\Core\Notification\Storage\Entity\NotificationContext $notificationContext
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function createNotificationContext(NotificationContext $notificationContext)
    {
        $this->getEntityManager()->persist($notificationContext);
        $this->getEntityManager()->flush();
    }
}