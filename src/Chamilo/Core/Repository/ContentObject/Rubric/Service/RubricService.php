<?php

namespace Chamilo\Core\Repository\ContentObject\Rubric\Service;

use Chamilo\Core\Repository\ContentObject\Rubric\Storage\Entity\RubricData;
use Chamilo\Core\Repository\ContentObject\Rubric\Storage\Entity\RubricNode;
use Chamilo\Core\Repository\ContentObject\Rubric\Storage\Entity\TreeNode;
use Chamilo\Core\Repository\ContentObject\Rubric\Storage\Repository\RubricDataRepository;
use Chamilo\Core\Repository\ContentObject\Rubric\Storage\Repository\TreeNodeRepository;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class RubricService
 *
 * @package Chamilo\Core\Repository\ContentObject\Rubric\Storage\Service
 *
 * @author - Sven Vanpoucke - Hogeschool Gent
 */
class RubricService
{
    /**
     * @var RubricDataRepository
     */
    protected $rubricDataRepository;

    /**
     * @var TreeNodeRepository
     */
    protected $treeNodeRepository;

    /**
     * RubricService constructor.
     *
     * @param RubricDataRepository $rubricDataRepository
     * @param TreeNodeRepository $treeNodeRepository
     */
    public function __construct(RubricDataRepository $rubricDataRepository, TreeNodeRepository $treeNodeRepository)
    {
        $this->rubricDataRepository = $rubricDataRepository;
        $this->treeNodeRepository = $treeNodeRepository;
    }

    /**
     * @param string $rubricName
     * @param bool $useScores
     *
     * @return RubricData
     * @throws \Doctrine\ORM\ORMException
     */
    public function createRubric(string $rubricName, bool $useScores = true)
    {
        $rubricData = new RubricData($rubricName, $useScores);

        $this->saveRubric($rubricData);

        return $rubricData;
    }

    /**
     * @param RubricData $rubricData
     *
     * @throws \Doctrine\ORM\ORMException
     */
    public function saveRubric(RubricData $rubricData)
    {
        $this->rubricDataRepository->saveRubricData($rubricData);
    }

    /**
     * These methods CAN be used to have a fully working, SQL and TRANSACTIONAL based sorting system. However, due
     * to a ?bug/feature? in Doctrine ORM it is currently not possible to update the domain model once the SQL queries
     * have been executed meaning that the domain model you are working with is becoming invalid.
     *
     * For that reason we have chosen to change the sorts in the domain model instead and update all the changes to the
     * database using the doctrine entity management logic (UnitOfWork).
     *
     * This will work on projects like this one where the data structure is already completely built in memory upon
     * retrieval and has limited number of data entities. In a situation where a lot of changes need to be made to the
     * sort value (like 1000+) it is recommended to still do this SQL based. For this latter case, the code below is not
     * removed so it can be consulted in the future as a reference.
     */
//    /**
//     * @param RubricData $rubricData
//     * @param TreeNode $newNode
//     * @param TreeNode $parentNode
//     *
//     * @throws \Exception
//     */
//    public function addTreeNode(RubricData $rubricData, TreeNode $newNode, TreeNode $parentNode)
//    {
//        $this->rubricDataRepository->executeTransaction(
//            function () use ($rubricData, $newNode, $parentNode) {
//                $parentNode->addChild($newNode);
//                $rubricData->addTreeNode($newNode);
//
//                if (!$newNode->getSort())
//                {
//                    $newSort = $this->treeNodeRepository->getNextTreeNodeSort($newNode);
//                    $newNode->setSort($newSort);
//                }
//                else
//                {
//                    $this->treeNodeRepository->prepareSortForInsertTreeNode($newNode);
//                }
//
//                $this->treeNodeRepository->saveTreeNode($newNode);
//            }
//        );
//
//        // Update the changed tree nodes from the parent node so the domain model stays correct
//        // Due to a bug this is currently not possible. Refreshing the parent entity does not update the children correctly
//    }
//
//    /**
//     * @param TreeNode $treeNode
//     *
//     * @throws \Exception
//     */
//    public function removeTreeNode(TreeNode $treeNode)
//    {
//        $parentNode = $treeNode->getParentNode();
//
//        $this->rubricDataRepository->executeTransaction(
//            function () use ($treeNode) {
//                $this->treeNodeRepository->removeTreeNode($treeNode);
//                $this->treeNodeRepository->cleanSortForRemovedNode($treeNode);
//
//                $treeNode->getParentNode()->removeChild($treeNode);
//                $treeNode->getRubricData()->removeTreeNode($treeNode);
//            }
//        );
//
//        // Update the changed tree nodes from the parent node so the domain model stays correct
//        // Due to a bug this is currently not possible. Refreshing the parent entity does not update the children correctly
//    }
//
//    /**
//     * @param TreeNode $treeNode
//     * @param int $newOrder
//     * @param TreeNode|null $newParentNode
//     *
//     * @throws \Exception
//     */
//    public function moveTreeNode(TreeNode $treeNode, TreeNode $newParentNode, int $newOrder = 0)
//    {
//        if (!$treeNode->getParentNode() instanceof TreeNode)
//        {
//            throw new \RuntimeException('Root nodes can not be moved');
//        }
//
//        $currentParentNode = $treeNode->getParentNode();
//
//        $this->rubricDataRepository->executeTransaction(
//            function () use ($treeNode, $newParentNode, $newOrder, $currentParentNode) {
//                $this->treeNodeRepository->cleanSortForRemovedNode($treeNode);
//                $treeNode->getParentNode()->removeChild($treeNode);
//
//                $newParentNode->addChild($treeNode);
//                if ($newOrder > 0)
//                {
//                    $treeNode->setSort($newOrder);
//                    $this->treeNodeRepository->prepareSortForInsertTreeNode($treeNode);
//                }
//                else
//                {
//                    $newSort = $this->treeNodeRepository->getNextTreeNodeSort($treeNode);
//                    $treeNode->setSort($newSort);
//                }
//
//                $this->treeNodeRepository->saveTreeNode($treeNode);
//
//            }
//        );
//    }
}
