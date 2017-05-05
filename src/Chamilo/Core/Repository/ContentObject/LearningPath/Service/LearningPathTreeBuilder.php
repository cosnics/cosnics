<?php

namespace Chamilo\Core\Repository\ContentObject\LearningPath\Service;

use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\LearningPathTree;
use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\LearningPathTreeNode;
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
class LearningPathTreeBuilder
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
     * @var LearningPathTreeNode[][]
     */
    protected $learningPathTreeNodesPerContentObjectId;

    /**
     * LearningPathTreeBuilder constructor.
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
     * @return LearningPathTree
     */
    public function buildLearningPathTree(LearningPath $learningPath)
    {
        $this->learningPathChildRepository->clearLearningPathChildrenCache();

        $learningPathTree = new LearningPathTree();
        $rootLearningPathTreeNode = new LearningPathTreeNode($learningPathTree, $learningPath);

        $learningPathChildren = $this->learningPathChildRepository
            ->findLearningPathChildrenForLearningPath($learningPath);

        $orderedLearningPathChildren = array();

        foreach ($learningPathChildren as $learningPathChild)
        {
            $orderedLearningPathChildren[$learningPathChild->getParentLearningPathChildId()]
            [$learningPathChild->getDisplayOrder()] = $learningPathChild;
        }

        $this->addChildrenForSection(0, $orderedLearningPathChildren, $learningPathTree, $rootLearningPathTreeNode);

        $this->addContentObjectsToLearningPathTreeNodes();

        unset($this->learningPathTreeNodesPerContentObjectId);

        return $learningPathTree;
    }

    /**
     * @param int $parentLearningPathChildId
     * @param LearningPathChild[][] $orderedLearningPathChildren
     * @param LearningPathTree $learningPathTree
     * @param LearningPathTreeNode $parentLearningPathTreeNode
     */
    protected function addChildrenForSection(
        $parentLearningPathChildId = 0, $orderedLearningPathChildren = array(), LearningPathTree $learningPathTree,
        LearningPathTreeNode $parentLearningPathTreeNode
    )
    {
        $learningPathChildrenForSection = $orderedLearningPathChildren[$parentLearningPathChildId];
        ksort($learningPathChildrenForSection);

        foreach ($learningPathChildrenForSection as $learningPathChild)
        {
            $learningPathTreeNode = new LearningPathTreeNode($learningPathTree, null, $learningPathChild);
            $parentLearningPathTreeNode->addChildNode($learningPathTreeNode);

            $this->learningPathTreeNodesPerContentObjectId[$learningPathChild->getContentObjectId()][] =
                $learningPathTreeNode;

            $this->addChildrenForSection(
                $learningPathChild->getId(), $orderedLearningPathChildren, $learningPathTree,
                $learningPathTreeNode
            );
        }
    }

    /**
     * Adds the content objects to the learning path tree nodes in batch
     */
    protected function addContentObjectsToLearningPathTreeNodes()
    {
        $contentObjectIds = array_keys($this->learningPathTreeNodesPerContentObjectId);

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
            $nodes = $this->learningPathTreeNodesPerContentObjectId[$contentObject->getId()];
            foreach($nodes as $node)
            {
                $node->setContentObject($contentObject);
            }
        }
    }
}