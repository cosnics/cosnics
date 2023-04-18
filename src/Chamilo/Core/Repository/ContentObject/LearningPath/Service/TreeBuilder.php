<?php

namespace Chamilo\Core\Repository\ContentObject\LearningPath\Service;

use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\Tree;
use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode;
use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNodeConfigurationInterface;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\LearningPath;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\TreeNodeData;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\Repository\TreeNodeDataRepository;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Workspace\Repository\ContentObjectRepository;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use JMS\Serializer\Serializer;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Builds an in memory tree for an entire learning path based on a given root
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class TreeBuilder
{

    /**
     *
     * @var TreeNodeDataRepository
     */
    protected $treeNodeDataRepository;

    /**
     *
     * @var ContentObjectRepository
     */
    protected $contentObjectRepository;

    /**
     *
     * @var TreeNode[][]
     */
    protected $treeNodesPerContentObjectId;

    /**
     * @var \JMS\Serializer\Serializer
     */
    protected $serializer;

    /**
     * TreeBuilder constructor.
     *
     * @param TreeNodeDataRepository $treeNodeDataRepository
     * @param ContentObjectRepository $contentObjectRepository
     * @param \JMS\Serializer\Serializer $serializer
     */
    public function __construct(
        TreeNodeDataRepository $treeNodeDataRepository,
        ContentObjectRepository $contentObjectRepository, Serializer $serializer
    )
    {
        $this->treeNodeDataRepository = $treeNodeDataRepository;
        $this->contentObjectRepository = $contentObjectRepository;
        $this->serializer = $serializer;
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

        $orderedTreeNodesData = array();

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

    /**
     *
     * @param int $parentTreeNodeDataId
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\TreeNodeData[][] $orderedTreeNodesData
     * @param Tree $tree
     * @param TreeNode $parentTreeNode
     */
    protected function addChildrenForSection(
        $parentTreeNodeDataId = 0, $orderedTreeNodesData = array(), Tree $tree,
        TreeNode $parentTreeNode = null
    )
    {
        $treeNodeDataForSection = $orderedTreeNodesData[$parentTreeNodeDataId];
        if(is_array($treeNodeDataForSection))
            ksort($treeNodeDataForSection);

        foreach ($treeNodeDataForSection as $treeNodeData)
        {
            $configuration = $this->getConfigurationFromTreeNodeData($treeNodeData);
            $treeNode = new TreeNode($tree, null, $treeNodeData, $configuration);

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
            ContentObject::class_name(),
            new DataClassRetrievesParameters(
                new InCondition(
                    new PropertyConditionVariable(ContentObject::class_name(), ContentObject::PROPERTY_ID),
                    $contentObjectIds
                )
            )
        );

        while ($contentObject = $contentObjects->next_result())
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
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\TreeNodeData $treeNodeData
     *
     * @return \Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNodeConfigurationInterface|null
     */
    protected function getConfigurationFromTreeNodeData(TreeNodeData $treeNodeData): ?TreeNodeConfigurationInterface
    {
        $serializedConfiguration = $treeNodeData->getConfiguration();
        if (empty($serializedConfiguration))
        {
            return null;
        }

        $configurationClass = $treeNodeData->getConfigurationClass();
        $interfaces = class_implements($configurationClass);

        if (!class_exists($configurationClass) || !array_key_exists(TreeNodeConfigurationInterface::class, $interfaces))
        {
            throw new \InvalidArgumentException(
                'The given configuration class in the TreeNodeData does not implement the TreeNodeConfigurationInterface'
            );
        }

        return $this->serializer->deserialize($serializedConfiguration, $configurationClass, 'json');
    }
}