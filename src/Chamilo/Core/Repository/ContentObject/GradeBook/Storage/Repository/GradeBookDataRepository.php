<?php

namespace Chamilo\Core\Repository\ContentObject\GradeBook\Storage\Repository;

/*use Chamilo\Core\Repository\ContentObject\GradeBook\Domain\Exceptions\InvalidChildTypeException;
use Chamilo\Core\Repository\ContentObject\GradeBook\Storage\Entity\Choice;
use Chamilo\Core\Repository\ContentObject\GradeBook\Storage\Entity\CriteriumNode;
use Chamilo\Core\Repository\ContentObject\GradeBook\Storage\Entity\Level;*/
use Chamilo\Core\Repository\ContentObject\GradeBook\Storage\Entity\GradeBookData;
//use Chamilo\Core\Repository\ContentObject\GradeBook\Storage\Entity\TreeNode;
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

        /*foreach($rubricData->getTreeNodes() as $treeNode)
        {
            $this->saveEntity($treeNode, false);
        }

        foreach($rubricData->getLevels() as $level)
        {
            $this->saveEntity($level, false);
        }

        foreach($rubricData->getChoices() as $choice)
        {
            $this->saveEntity($choice, false);
        }*/

        $this->flush();

        /*foreach($rubricData->getRemovedEntities() as $removedEntity)
        {
            $this->removeEntity($removedEntity, false);
        }

        $this->flush();
        */
    }

    /**
     * @param GradeBookData $gradeBookData
     *
     * @throws \Doctrine\ORM\ORMException
     */
    function deleteGradeBookData(GradeBookData $gradeBookData)
    {
        /*try
        {
            $rubricData->setRootNode(null);
        }
        catch (InvalidChildTypeException $e) {}

        foreach($rubricData->getTreeNodes() as $treeNode)
        {
            $this->removeEntity($treeNode, false);
        }

        foreach($rubricData->getLevels() as $level)
        {
            $this->removeEntity($level, false);
        }

        foreach($rubricData->getChoices() as $choice)
        {
            $this->removeEntity($choice, false);
        }

        $this->flush();*/

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
    public function findEntireGradeBookById(int $gradeBookDataId, int $expectedVersion = null)
    {
        //$this->getEntityManager()->getConnection()->getConfiguration()->setSQLLogger(new EchoSQLLogger());

        $qb = $this->createQueryBuilder('gbd')
            /*->addSelect('tn')
            ->addSelect('rn')
            ->addSelect('cn')
            ->join('gbd.treeNodes', 'tn')
            ->join('gbd.rootNode', 'rn')
            ->leftJoin('tn.children', 'cn')*/
            ->where('gbd.id = :id')
            //->orderBy('tn.depth')
            //->addOrderBy('tn.sort')
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

        foreach ($gradeBookData->getGradeBookColumns() as $column)
        {
            $column->getGradeBookColumnSubItems()[0];
        }

        foreach ($gradeBookData->getGradeBookCategories() as $category)
        {
            $category->getGradeBookColumns()[0];
        }

        /*$choices = $rubricData->getChoices();
        foreach($choices as $choice)
        {
            $criteriumChoices = $choice->getCriterium()->getChoices();

            if($criteriumChoices instanceof PersistentCollection && !$criteriumChoices->isInitialized())
            {
                $criteriumChoices->setInitialized(true);
                $criteriumChoices->clear();
            }

            $criteriumChoices->add($choice);
        }*/
        // end preload for performance - thank you doctrine for not caching on foreign keys and not being able to join on a subclass of an inheritance
        // (maybe we should change the domain model?)

        return $gradeBookData;
    }

/*    /**
     * @param TreeNode $treeNode
     *
     * @throws \Doctrine\ORM\ORMException
     */
/*    public function removeTreeNode(TreeNode $treeNode)
    {
        $this->removeEntity($treeNode);
    }*/

/*    /**
     * @param Level $level
     *
     * @throws \Doctrine\ORM\ORMException
     */
/*    public function removeLevel(Level $level)
    {
        $this->removeEntity($level);
    }*/

/*    /**
     * @param Choice $choice
     *
     * @throws \Doctrine\ORM\ORMException
     */
/*    public function removeChoice(Choice $choice)
    {
        $this->removeEntity($choice);
    }*/
}
