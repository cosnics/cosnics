<?php

namespace Chamilo\Core\Repository\ContentObject\Rubric\Storage\Repository;

use Chamilo\Core\Repository\ContentObject\Rubric\Domain\Exceptions\InvalidChildTypeException;
use Chamilo\Core\Repository\ContentObject\Rubric\Storage\Entity\Choice;
use Chamilo\Core\Repository\ContentObject\Rubric\Storage\Entity\Level;
use Chamilo\Core\Repository\ContentObject\Rubric\Storage\Entity\RubricData;
use Chamilo\Core\Repository\ContentObject\Rubric\Storage\Entity\TreeNode;
use Chamilo\Libraries\Storage\DataManager\Doctrine\ORM\CommonEntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\LockMode;
use Doctrine\ORM\PessimisticLockException;

/**
 * Class RubricDataRepository
 *
 * @package Chamilo\Core\Repository\ContentObject\Rubric\Storage\Repository
 *
 * @author - Sven Vanpoucke - Hogeschool Gent
 */
class RubricDataRepository extends CommonEntityRepository
{

    /**
     * @param RubricData $rubricData
     *
     * @throws \Doctrine\ORM\ORMException
     */
    public function saveRubricData(RubricData $rubricData)
    {
        $this->saveEntity($rubricData, false);

        foreach($rubricData->getTreeNodes() as $treeNode)
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
        }

        $this->flush();

        foreach($rubricData->getRemovedEntities() as $removedEntity)
        {
            $this->removeEntity($removedEntity, false);
        }

        $this->flush();
    }

    /**
     * @param RubricData $rubricData
     *
     * @throws \Doctrine\ORM\ORMException
     */
    function deleteRubricData(RubricData $rubricData)
    {
        try
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

        $this->flush();

        $this->removeEntity($rubricData);
    }

    /**
     * @param int $rubricDataId
     * @param int $expectedVersion
     *
     * @return RubricData
     *
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function findEntireRubricById(int $rubricDataId, int $expectedVersion = null)
    {
        $qb = $this->createQueryBuilder('rd')
            ->addSelect('tn')
            ->addSelect('rn')
            ->addSelect('lv')
            ->addSelect('ch')
            ->join('rd.treeNodes', 'tn')
            ->join('rd.rootNode', 'rn')
            ->leftJoin('rd.levels', 'lv')
            ->leftJoin('rd.choices', 'ch')
            ->where('rd.id = :id')
            ->orderBy('tn.depth')
            ->addOrderBy('tn.sort')
            ->setParameter('id', $rubricDataId);

        $rubricData = $qb->getQuery()->getSingleResult();

        if(!is_null($expectedVersion))
        {
            try
            {
                $this->getEntityManager()->lock($rubricData, LockMode::OPTIMISTIC, $expectedVersion);
            }
            catch (PessimisticLockException $ex)
            {
                // The doc throws a pessimistic lock exception which is impossible, just catching it here so it doesn't go to the other docblocks
            }
        }

        return $rubricData;
    }

    /**
     * @param TreeNode $treeNode
     *
     * @throws \Doctrine\ORM\ORMException
     */
    public function removeTreeNode(TreeNode $treeNode)
    {
        $this->removeEntity($treeNode);
    }

    /**
     * @param Level $level
     *
     * @throws \Doctrine\ORM\ORMException
     */
    public function removeLevel(Level $level)
    {
        $this->removeEntity($level);
    }

    /**
     * @param Choice $choice
     *
     * @throws \Doctrine\ORM\ORMException
     */
    public function removeChoice(Choice $choice)
    {
        $this->removeEntity($choice);
    }

}
