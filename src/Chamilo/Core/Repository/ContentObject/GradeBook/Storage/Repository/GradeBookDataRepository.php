<?php

namespace Chamilo\Core\Repository\ContentObject\GradeBook\Storage\Repository;

use Chamilo\Core\Repository\ContentObject\GradeBook\Storage\Entity\GradeBookData;
use Chamilo\Libraries\Storage\DataManager\Doctrine\ORM\CommonEntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\LockMode;
use Doctrine\DBAL\Logging\EchoSQLLogger;
use Doctrine\ORM\PersistentCollection;
use Doctrine\ORM\PessimisticLockException;
use Doctrine\ORM\Query\Expr\Join;

/**
 * Class GradeBookDataRepository
 *
 * @package Chamilo\Core\Repository\ContentObject\GradeBook\Storage\Repository
 *
 * @author - Stefan Gabriëls - Hogeschool Gent
 */
class GradeBookDataRepository extends CommonEntityRepository
{

    /**
     * @param GradeBookData $gradeBookData
     *
     * @throws \Doctrine\ORM\ORMException
     */
    public function saveGradeBookData(GradeBookData $gradeBookData)
    {
        $this->saveEntity($gradeBookData, false);

        foreach ($gradeBookData->getGradeBookCategories() as $category)
        {
            $this->saveEntity($category, false);
        }

        foreach ($gradeBookData->getGradeBookColumns() as $column)
        {
            $this->saveEntity($column, false);
        }

        foreach ($gradeBookData->getGradeBookItems() as $item)
        {
            $this->saveEntity($item, false);
        }

        foreach ($gradeBookData->getGradeBookScores() as $score)
        {
            $this->saveEntity($score, false);
        }

        $this->flush();

        foreach ($gradeBookData->getRemovedEntities() as $removedEntity)
        {
            $this->removeEntity($removedEntity, false);
        }

        $this->flush();
    }

    /**
     * @param GradeBookData $gradeBookData
     *
     * @throws \Doctrine\ORM\ORMException
     */
    function deleteGradeBookData(GradeBookData $gradeBookData)
    {
        $this->removeEntity($gradeBookData);
    }

    /**
     * @param int $gradeBookDataId
     * @param int $expectedVersion
     *
     * @return GradeBookData
     *
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function findGradeBookDataById(int $gradeBookDataId, int $expectedVersion = null)
    {
        $qb = $this->createQueryBuilder('gbd')
            ->where('gbd.id = :id')
            ->setParameter('id', $gradeBookDataId);

        /** @var GradeBookData $gradeBookData */
        $gradeBookData = $qb->getQuery()->getSingleResult();

        if (!is_null($expectedVersion))
        {
            try
            {
                $this->getEntityManager()->lock($gradeBookData, LockMode::OPTIMISTIC, $expectedVersion);
            }
            catch (PessimisticLockException $ex)
            {
                // The doc throws a pessimistic lock exception which is impossible, just catching it here so it doesn't go to the other docblocks
            }
        }

        // preload for performance - voodoo magic
        $gradeBookData->getGradeBookCategories()[0];
        $gradeBookData->getGradeBookItems()[0];
        $gradeBookData->getGradeBookColumns()[0];
        //$gradeBookData->getGradeBookScores()[0];

        foreach ($gradeBookData->getGradeBookColumns() as $column)
        {
            $column->getGradeBookColumnSubItems()[0];
            //$column->getGradeBookScores()[0];
        }

        foreach ($gradeBookData->getGradeBookCategories() as $category)
        {
            $category->getGradeBookColumns()[0];
        }

        // end preload for performance - thank you doctrine for not caching on foreign keys and not being able to join on a subclass of an inheritance
        // (maybe we should change the domain model?)

        return $gradeBookData;
    }
}
