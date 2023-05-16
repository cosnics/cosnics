<?php
namespace Chamilo\Core\Notification\Storage\Repository;

use Chamilo\Core\Notification\Storage\Entity\Filter;
use Chamilo\Core\Notification\Storage\Entity\NotificationContext;
use Doctrine\ORM\EntityRepository;

/**
 * @package Chamilo\Core\Notification\Storage\Repository
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class FilterRepository extends EntityRepository
{
    /**
     * @param \Chamilo\Core\Notification\Storage\Entity\NotificationContext $notificationContext
     *
     * @return object|Filter
     */
    public function findFilterByNotificationContext(NotificationContext $notificationContext)
    {
        return $this->findOneBy(array('notificationContext' => $notificationContext));
    }

    /**
     * @param \Chamilo\Core\Notification\Storage\Entity\Filter $filter
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function createFilter(Filter $filter)
    {
        $this->getEntityManager()->persist($filter);
        $this->getEntityManager()->flush();
    }
}