<?php

namespace Chamilo\Core\Notification\Storage\Repository;

use Chamilo\Core\Notification\Storage\Entity\Filter;
use Doctrine\ORM\EntityRepository;

/**
 * @package Chamilo\Core\Notification\Storage\Repository
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class FilterRepository extends EntityRepository
{
    /**
     * @param string $filterPath
     *
     * @return \Chamilo\Core\Notification\Storage\Entity\Filter|object
     */
    public function findByPath($filterPath)
    {
        return $this->findOneBy(['path' => $filterPath]);
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