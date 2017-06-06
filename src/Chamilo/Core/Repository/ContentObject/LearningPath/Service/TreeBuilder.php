<?php

namespace Chamilo\Core\Repository\ContentObject\LearningPath\Service;

use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\Tree;
use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\LearningPath;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\LearningPathChild;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\Repository\LearningPathChildRepository;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Workspace\Repository\ContentObjectRepository;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
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
     * @var LearningPathChildRepository
     */
    protected $learningPathChildRepository;

    /**
     * @var ContentObjectRepository
     */
    protected $contentObjectRepository;

    /**
     * @var TreeNode[][]
     */
    protected $treeNodesPerContentObjectId;

    /**
     * TreeBuilder constructor.
     *
     * @param LearningPathChildRepository $learningPathChildRepository
     * @param ContentObjectRepository $contentObjectRepository
     */
    public function __construct(
        LearningPathChildRepository $learningPathChildRepository,
        ContentObjectRepository $contentObjectRepository
    )
    {
        $this->learningPathChildRepository = $learningPathChildRepository;
        $this->contentObjectRepository = $contentObjectRepository;
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
        $this->learningPathChildRepository->clearLearningPathChildrenCache();

        $tree = new Tree();
        $rootTreeNode = new TreeNode($tree, $learningPath);

        $learningPathChildren = $this->learningPathChildRepository
            ->findLearningPathChildrenForLearningPath($learningPath);

        $orderedLearningPathChildren = array();

        foreach ($learningPathChildren as $learningPathChild)
        {
            $orderedLearningPathChildren[$learningPathChild->getParentLearningPathChildId()]
            [$learningPathChild->getDisplayOrder()] = $learningPathChild;
        }

        $this->addChildrenForSection(0, $orderedLearningPathChildren, $tree, $rootTreeNode);

        $this->addContentObjectsToTreeNodes();

        unset($this->treeNodesPerContentObjectId);

        return $tree;
    }

    /**
     * @param int $parentLearningPathChildId
     * @param LearningPathChild[][] $orderedLearningPathChildren
     * @param Tree $tree
     * @param TreeNode $parentTreeNode
     */
    protected function addChildrenForSection(
        $parentLearningPathChildId = 0, $orderedLearningPathChildren = array(), Tree $tree,
        TreeNode $parentTreeNode
    )
    {
        $learningPathChildrenForSection = $orderedLearningPathChildren[$parentLearningPathChildId];
        ksort($learningPathChildrenForSection);

        foreach ($learningPathChildrenForSection as $learningPathChild)
        {
            $treeNode = new TreeNode($tree, null, $learningPathChild);
            $parentTreeNode->addChildNode($treeNode);

            $this->treeNodesPerContentObjectId[$learningPathChild->getContentObjectId()][] =
                $treeNode;

            $this->addChildrenForSection(
                $learningPathChild->getId(), $orderedLearningPathChildren, $tree,
                $treeNode
            );
        }
    }

    /**
     * Adds the content objects to the learning path tree nodes in batch
     */
    protected function addContentObjectsToTreeNodes()
    {
        $contentObjectIds = array_keys($this->treeNodesPerContentObjectId);

        $contentObjects = $this->contentObjectRepository->findAll(
            ContentObject::class_name(),
            new DataClassRetrievesParameters(
                new InCondition(
                    new PropertyConditionVariable(ContentObject::class_name(), ContentObject::PROPERTY_ID),
                    $contentObjectIds
                )
            )
        );

        while($contentObject = $contentObjects->next_result())
        {
            /** @var ContentObject $contentObject */
            $nodes = $this->treeNodesPerContentObjectId[$contentObject->getId()];
            foreach($nodes as $node)
            {
                $node->setContentObject($contentObject);
            }
        }
    }
}