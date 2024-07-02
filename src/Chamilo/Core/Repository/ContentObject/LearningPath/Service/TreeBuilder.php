<?php
namespace Chamilo\Core\Repository\ContentObject\LearningPath\Service;

use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\Tree;
use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\LearningPath;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\Repository\TreeNodeDataRepository;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Workspace\Repository\ContentObjectRepository;
use Chamilo\Libraries\Storage\Parameters\DataClassParameters;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;

/**
 * Builds an in memory tree for an entire learning path based on a given root
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class TreeBuilder
{

    /**
     *
     * @var ContentObjectRepository
     */
    protected $contentObjectRepository;

    /**
     *
     * @var TreeNodeDataRepository
     */
    protected $treeNodeDataRepository;

    /**
     *
     * @var TreeNode[][]
     */
    protected $treeNodesPerContentObjectId;

    /**
     * TreeBuilder constructor.
     *
     * @param TreeNodeDataRepository $treeNodeDataRepository
     * @param ContentObjectRepository $contentObjectRepository
     */
    public function __construct(
        TreeNodeDataRepository $treeNodeDataRepository, ContentObjectRepository $contentObjectRepository
    )
    {
        $this->treeNodeDataRepository = $treeNodeDataRepository;
        $this->contentObjectRepository = $contentObjectRepository;
    }

    /**
     *
     * @param int $parentTreeNodeDataId
     * @param TreeNodeData[][] $orderedTreeNodesData
     * @param Tree $tree
     * @param TreeNode $parentTreeNode
     */
    protected function addChildrenForSection(
        $parentTreeNodeDataId = 0, $orderedTreeNodesData = [], Tree $tree, TreeNode $parentTreeNode = null
    )
    {
        $treeNodeDataForSection = $orderedTreeNodesData[$parentTreeNodeDataId];
        ksort($treeNodeDataForSection);

        foreach ($treeNodeDataForSection as $treeNodeData)
        {
            $treeNode = new TreeNode($tree, null, $treeNodeData);

            if ($parentTreeNode instanceof TreeNode)
            {
                $parentTreeNode->addChildNode($treeNode);
            }

            $this->treeNodesPerContentObjectId[$treeNodeData->getContentObjectId()][] = $treeNode;

            $this->addChildrenForSection($treeNodeData->getId(), $orderedTreeNodesData, $tree, $treeNode);
        }
    }

    /**
     * Adds the content objects to the learning path tree nodes in batch
     */
    protected function addContentObjectsToTreeNodes()
    {
        $contentObjectIds = array_keys($this->treeNodesPerContentObjectId);

        $contentObjects = $this->contentObjectRepository->findAll(
            ContentObject::class, new DataClassParameters(
                condition: new InCondition(
                    new PropertyConditionVariable(ContentObject::class, ContentObject::PROPERTY_ID), $contentObjectIds
                )
            )
        );

        foreach ($contentObjects as $contentObject)
        {
            /** @var ContentObject $contentObject */
            $nodes = $this->treeNodesPerContentObjectId[$contentObject->getId()];
            foreach ($nodes as $node)
            {
                $node->setContentObject($contentObject);
            }
        }
    }

    /**
     * Builds an in memory tree for an entire learning path based on a given root
     *
     * @param LearningPath $learningPath
     *
     * @return Tree
     */
    public function buildTree(LearningPath $learningPath)
    {
        $this->treeNodeDataRepository->clearTreeNodesDataCache();

        $tree = new Tree();

        $treeNodesData = $this->treeNodeDataRepository->findTreeNodesDataForLearningPath($learningPath);

        $orderedTreeNodesData = [];

        foreach ($treeNodesData as $treeNodeData)
        {
            $orderedTreeNodesData[$treeNodeData->getParentTreeNodeDataId()][$treeNodeData->getDisplayOrder()] =
                $treeNodeData;
        }

        $this->addChildrenForSection(0, $orderedTreeNodesData, $tree);

        $this->addContentObjectsToTreeNodes();

        unset($this->treeNodesPerContentObjectId);

        return $tree;
    }
}