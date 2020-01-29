<?php

namespace Chamilo\Core\Repository\ContentObject\Rubric\Storage\Repository;

use Chamilo\Core\Repository\ContentObject\Rubric\Storage\Entity\RubricData;
use Chamilo\Core\Repository\ContentObject\Rubric\Storage\Entity\TreeNode;
use Chamilo\Libraries\Storage\DataManager\Doctrine\ORM\CommonEntityRepository;

/**
 * Class RubricRepository
 *
 * @package Chamilo\Core\Repository\ContentObject\Rubric\Storage\Repository
 *
 * @author - Sven Vanpoucke - Hogeschool Gent
 */
class TreeNodeRepository extends CommonEntityRepository
{

    /**
     * @param TreeNode $treeNode
     *
     * @throws \Doctrine\ORM\ORMException
     */
    public function saveTreeNode(TreeNode $treeNode)
    {
        $this->saveEntity($treeNode);
    }

    /**
     * @param TreeNode $treeNode
     *
     * @return int
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Doctrine\ORM\TransactionRequiredException
     */
    public function getNextTreeNodeSort(TreeNode $treeNode)
    {
        $query = $this->getEntityManager()->createQuery(
            sprintf(
                'SELECT MAX(tn.sort) FROM %s tn WHERE tn.rubricData = :rubricData AND tn.parentNode = :parentNode',
                TreeNode::class
            )
        );

        $query->setParameter("parentNode", $treeNode->getParentNode());
        $query->setParameter("rubricData", $treeNode->getRubricData());
        $query->setLockMode(\Doctrine\DBAL\LockMode::PESSIMISTIC_WRITE);

        return $query->getSingleScalarResult() + 1;
    }

    /**
     * @param TreeNode $treeNode
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function removeTreeNode(TreeNode $treeNode)
    {
        $this->removeEntity($treeNode);
    }

    /**
     * @param TreeNode $treeNode
     */
    public function cleanSortForRemovedNode(TreeNode $treeNode)
    {
        $query = $this->getEntityManager()->createQuery(
            sprintf(
                'UPDATE %s tn SET tn.sort = tn.sort - 1 WHERE ' .
                'tn.rubricData = :rubricData AND tn.parentNode = :parentNode AND tn.sort > :treeNodeSort',
                TreeNode::class
            )
        );

        $query->setParameter("parentNode", $treeNode->getParentNode());
        $query->setParameter("rubricData", $treeNode->getRubricData());
        $query->setParameter("treeNodeSort", $treeNode->getSort());

        $query->execute();
    }

    /**
     * @param TreeNode $treeNode
     */
    public function prepareSortForInsertTreeNode(TreeNode $treeNode)
    {
        $query = $this->getEntityManager()->createQuery(
            sprintf(
                'UPDATE %s tn SET tn.sort = tn.sort + 1 WHERE ' .
                'tn.rubricData = :rubricData AND tn.parentNode = :parentNode AND tn.sort > :treeNodeSort',
                TreeNode::class
            )
        );

        $query->setParameter("parentNode", $treeNode->getParentNode());
        $query->setParameter("rubricData", $treeNode->getRubricData());
        $query->setParameter("treeNodeSort", $treeNode->getSort());

        $query->execute();
    }

    /**
     * @param TreeNode $parentNode
     * @param bool $force
     *
     * @return TreeNode[]
     */
    public function getChildrenForNode(TreeNode $parentNode, $force = false)
    {
        if($force)
        {
            foreach ($parentNode->getChildren() as $child)
            {
                $this->getEntityManager()->detach($child);
            }
        }

        $queryBuilder = $this->createQueryBuilder('tn')
            ->where('tn.rubricData = :rubricData')
            ->andWhere('tn.parentNode = :parentNode')
             ->setParameter("parentNode", $parentNode)
             ->setParameter("rubricData", $parentNode->getRubricData());

        return $queryBuilder->getQuery()->getResult();
    }

}
