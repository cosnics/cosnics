<?php

namespace Chamilo\Core\Repository\ContentObject\LearningPath\Service;

use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\LearningPathTreeNode;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\LearningPath;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;

/**
 * This class validates new learning path children to make sure that it is not possible to create loops in
 * the given learning path. Uses the learning path tree to determine the limitations of the learning path tree
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class LearningPathChildValidator
{
    /**
     * @var LearningPathTreeBuilder
     */
    protected $learningPathTreeBuilder;

    /**
     * LearningPathChildValidator constructor.
     *
     * @param LearningPathTreeBuilder $learningPathTreeBuilder
     */
    public function __construct(LearningPathTreeBuilder $learningPathTreeBuilder)
    {
        $this->learningPathTreeBuilder = $learningPathTreeBuilder;
    }

    /**
     * Checks whether or not a content object can be added to a learning path. Uses the LearningPathTree nodes.
     *
     * @param LearningPathTreeNode $parentLearningPathTreeNode
     * @param ContentObject $childContentObject
     *
     * @return bool
     */
    public function canContentObjectBeAdded(
        LearningPathTreeNode $parentLearningPathTreeNode, ContentObject $childContentObject
    )
    {
        if(!$childContentObject instanceof LearningPath)
        {
            return true;
        }

        if(!$parentLearningPathTreeNode->getContentObject() instanceof LearningPath)
        {
            return false;
        }

        $selfAndParentContentObjectIds = $parentLearningPathTreeNode->getPathAsContentObjectIds();

        $childLearningPathTree = $this->learningPathTreeBuilder->buildLearningPathTree($childContentObject);
        $childTreeContentObjectIds = array();
        
        foreach($childLearningPathTree->getLearningPathTreeNodes() as $childLearningPathTreeNode)
        {
            $childTreeContentObjectIds[] = $childLearningPathTreeNode->getContentObject()->getId();
        }

        return count(array_intersect($childTreeContentObjectIds, $selfAndParentContentObjectIds)) == 0;
    }

    /**
     * Returns a list of content object ids that can not be added to the giving learning path tree node
     *
     * @param LearningPathTreeNode $learningPathTreeNode
     *
     * @return array
     */
    public function getContentObjectIdsThatCanNotBeAddedTo(LearningPathTreeNode $learningPathTreeNode)
    {
        /** @var LearningPathTreeNode[] $learningPathTreeNodes */
        $learningPathTreeNodes = array();
        $learningPathTreeNodes[$learningPathTreeNode->getStep()] = $learningPathTreeNode;

        $learningPathTreeNodes += $learningPathTreeNode->getParentNodes();
        $learningPathTreeNodes += $learningPathTreeNode->getChildNodes();

        ksort($learningPathTreeNodes);

        $contentObjectIds = array();

        foreach($learningPathTreeNodes as $learningPathTreeNode)
        {
            $contentObjectIds[] = $learningPathTreeNode->getContentObject()->getId();
        }

        return $contentObjectIds;
    }
}