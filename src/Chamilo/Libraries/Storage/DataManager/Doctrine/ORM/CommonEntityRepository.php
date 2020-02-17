<?php

namespace Chamilo\Libraries\Storage\DataManager\Doctrine\ORM;

use Doctrine\ORM\EntityRepository;

/**
 * Class with common entity repository functionality like transactions, save / removal of entities
 *
 * Class CommonEntityRepository
 *
 * @package Chamilo\Libraries\Storage\DataManager\Doctrine\ORM
 *
 * @author - Sven Vanpoucke - Hogeschool Gent
 */
abstract class CommonEntityRepository extends EntityRepository
{
    /**
     * @param $entity
     * @param bool $flush
     *
     * @throws \Doctrine\ORM\ORMException
     */
    protected function saveEntity($entity, $flush = true)
    {
        $this->getEntityManager()->persist($entity);
        if($flush)
        {
            $this->flush();
        }
    }

    /**
     * @param $entity
     * @param bool $flush
     *
     * @throws \Doctrine\ORM\ORMException
     */
    protected function removeEntity($entity, $flush = true)
    {
        $this->getEntityManager()->remove($entity);
        if($flush)
        {
            $this->flush();
        }
    }

    /**
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    protected function flush()
    {
        $this->getEntityManager()->flush();
    }

    /**
     * @param \Closure $callable
     *
     * @return mixed
     *
     * @throws \Exception
     */
    public function executeTransaction(\Closure $callable)
    {
        $this->getEntityManager()->beginTransaction();

        try
        {
            $result = call_user_func($callable);
            $this->getEntityManager()->commit();

            return $result;
        }
        catch(\Exception $ex)
        {
            $this->getEntityManager()->rollback();
            throw $ex;
        }
    }

    /**
     * @param $entity
     *
     * @throws \Doctrine\ORM\ORMException
     */
    public function refreshEntity($entity)
    {
        $this->getEntityManager()->refresh($entity);
    }
}
