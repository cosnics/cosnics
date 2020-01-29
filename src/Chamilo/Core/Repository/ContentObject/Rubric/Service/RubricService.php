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
        $rubricData = new RubricData();
        $rubricData->setUseScores($useScores);

        $rootNode = new RubricNode($rubricName);
        $rootNode->setSort(1);
        $rubricData->setRootNode($rootNode);

        $this->rubricDataRepository->saveRubricData($rubricData);

        return $rubricData;
    }

    /**
     * @param RubricData $rubricData
     * @param TreeNode $newNode
     * @param TreeNode $parentNode
     *
     * @throws \Exception
     */
    public function addTreeNode(RubricData $rubricData, TreeNode $newNode, TreeNode $parentNode)
    {
        $this->rubricDataRepository->executeTransaction(
            function () use ($rubricData, $newNode, $parentNode) {
                $parentNode->addChild($newNode);
                $rubricData->addTreeNode($newNode);

                if (!$newNode->getSort())
                {
                    $newSort = $this->treeNodeRepository->getNextTreeNodeSort($newNode);
                    $newNode->setSort($newSort);
                }
                else
                {
                    $this->treeNodeRepository->prepareSortForInsertTreeNode($newNode);
                }

                $this->treeNodeRepository->saveTreeNode($newNode);
            }
        );

        // Update the changed tree nodes from the parent node so the domain model stays correct
        $parentNode->setChildren(new ArrayCollection($this->treeNodeRepository->getChildrenForNode($parentNode, true)));
    }

    /**
     * @param TreeNode $treeNode
     *
     * @throws \Exception
     */
    public function removeTreeNode(TreeNode $treeNode)
    {
        $parentNode = $treeNode->getParentNode();

        $this->rubricDataRepository->executeTransaction(
            function () use ($treeNode) {
                $this->treeNodeRepository->removeTreeNode($treeNode);
                $this->treeNodeRepository->cleanSortForRemovedNode($treeNode);

                $treeNode->getParentNode()->removeChild($treeNode);
                $treeNode->getRubricData()->removeTreeNode($treeNode);
            }
        );

        // Update the changed tree nodes from the parent node so the domain model stays correct
        $parentNode->setChildren(new ArrayCollection($this->treeNodeRepository->getChildrenForNode($parentNode, true)));
    }

    /**
     * @param TreeNode $treeNode
     * @param int $newOrder
     * @param TreeNode|null $newParentNode
     *
     * @throws \Exception
     */
    public function moveTreeNode(TreeNode $treeNode, TreeNode $newParentNode, int $newOrder = 0)
    {
        if (!$treeNode->getParentNode() instanceof TreeNode)
        {
            throw new \RuntimeException('Root nodes can not be moved');
        }

        $currentParentNode = $treeNode->getParentNode();

        $this->rubricDataRepository->executeTransaction(
            function () use ($treeNode, $newParentNode, $newOrder, $currentParentNode) {
                $this->treeNodeRepository->cleanSortForRemovedNode($treeNode);
                $treeNode->getParentNode()->removeChild($treeNode);

                $newParentNode->addChild($treeNode);
                if ($newOrder > 0)
                {
                    $treeNode->setSort($newOrder);
                    $this->treeNodeRepository->prepareSortForInsertTreeNode($treeNode);
                }
                else
                {
                    $newSort = $this->treeNodeRepository->getNextTreeNodeSort($treeNode);
                    $treeNode->setSort($newSort);
                }

                $this->treeNodeRepository->saveTreeNode($treeNode);

                $currentParentNode->setChildren(
                    new ArrayCollection($this->treeNodeRepository->getChildrenForNode($currentParentNode, true))
                );

                $newParentNode->setChildren(
                    new ArrayCollection($this->treeNodeRepository->getChildrenForNode($newParentNode, true))
                );
            }
        );
    }
}
