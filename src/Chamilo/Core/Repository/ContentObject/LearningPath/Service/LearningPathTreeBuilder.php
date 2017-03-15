<?php

namespace Chamilo\Core\Repository\ContentObject\LearningPath\Service;

use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\LearningPathTree;
use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\LearningPathTreeNode;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\LearningPath;
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

        $this->addChildrenForLearningPath($learningPath, $learningPathTree, $rootLearningPathTreeNode);

        return $learningPathTree;
    }

    /**
     * Adds the children for a given learning path to the learning path tree
     *
     * @param LearningPath $learningPath
     * @param LearningPathTree $learningPathTree
     * @param LearningPathTreeNode $parentLearningPathTreeNode
     */
    protected function addChildrenForLearningPath(
        LearningPath $learningPath, LearningPathTree $learningPathTree, LearningPathTreeNode $parentLearningPathTreeNode
    )
    {
        $learningPathChildren = $this->learningPathChildRepository
            ->retrieveLearningPathChildrenForLearningPath($learningPath);

        foreach ($learningPathChildren as $learningPathChild)
        {
            $contentObject = $this->contentObjectRepository->findById($learningPathChild->getContentObjectId());

            $learningPathTreeNode = new LearningPathTreeNode($learningPathTree, $contentObject);
            $learningPathTreeNode->setLearningPathChild($learningPathChild);
            $parentLearningPathTreeNode->addChildNode($learningPathTreeNode);

            if ($contentObject instanceof LearningPath)
            {
                $this->addChildrenForLearningPath($contentObject, $learningPathTree, $learningPathTreeNode);
            }
        }
    }
}