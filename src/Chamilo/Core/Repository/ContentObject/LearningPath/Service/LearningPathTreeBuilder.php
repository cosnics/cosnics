<?php

namespace Chamilo\Core\Repository\ContentObject\LearningPath\Service;

use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\LearningPathTree;
use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\LearningPathTreeNode;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\LearningPath;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\LearningPathChild;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\Repository\LearningPathChildRepository;
use Chamilo\Core\Repository\Workspace\Repository\ContentObjectRepository;

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
            ->retrieveLearningPathChildrenForLearningPath($learningPath);

        $orderedLearningPathChildren = array();

        foreach ($learningPathChildren as $learningPathChild)
        {
            $orderedLearningPathChildren[$learningPathChild->getSectionContentObjectId(
            )][$learningPathChild->getDisplayOrder()] = $learningPathChild;
        }

        $this->addChildrenForSection(0, $orderedLearningPathChildren, $learningPathTree, $rootLearningPathTreeNode);

        return $learningPathTree;
    }

    /**
     * @param int $sectionId
     * @param LearningPathChild[][] $orderedLearningPathChildren
     * @param LearningPathTree $learningPathTree
     * @param LearningPathTreeNode $parentLearningPathTreeNode
     */
    protected function addChildrenForSection(
        $sectionId = 0, $orderedLearningPathChildren = array(), LearningPathTree $learningPathTree,
        LearningPathTreeNode $parentLearningPathTreeNode
    )
    {
        $learningPathChildrenForSection = $orderedLearningPathChildren[$sectionId];
        ksort($learningPathChildrenForSection);

        foreach ($learningPathChildrenForSection as $learningPathChild)
        {
            $contentObject = $this->contentObjectRepository->findById($learningPathChild->getContentObjectId());

            $learningPathTreeNode = new LearningPathTreeNode($learningPathTree, $contentObject, $learningPathChild);
            $parentLearningPathTreeNode->addChildNode($learningPathTreeNode);

            $this->addChildrenForSection(
                $learningPathChild->getId(), $orderedLearningPathChildren, $learningPathTree,
                $learningPathTreeNode
            );
        }
    }
}