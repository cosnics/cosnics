<?php

namespace Chamilo\Core\Repository\ContentObject\LearningPath\Service;

use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\LearningPathTreeNode;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\LearningPath;
use Chamilo\Core\Repository\Workspace\Repository\ContentObjectRepository;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;

/**
 * Service to manage learning paths
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class LearningPathService
{
    /**
     * @var ContentObjectRepository
     */
    protected $contentObjectRepository;

    /**
     * @var LearningPathTreeBuilder
     */
    protected $learningPathTreeBuilder;

    /**
     * @var LearningPathChildService
     */
    protected $learningPathChildService;

    /**
     * LearningPathService constructor.
     *
     * @param ContentObjectRepository $contentObjectRepository
     * @param LearningPathTreeBuilder $learningPathTreeBuilder
     * @param LearningPathChildService $learningPathChildService
     */
    public function __construct(
        ContentObjectRepository $contentObjectRepository, LearningPathTreeBuilder $learningPathTreeBuilder,
        LearningPathChildService $learningPathChildService
    )
    {
        $this->contentObjectRepository = $contentObjectRepository;
    }

    /**
     * Returns a list of learning paths
     *
     * @return LearningPath[]
     */
    public function getLearningPaths()
    {
        /** @var LearningPath[] $learningPaths */
        $learningPaths =
            $this->contentObjectRepository->findAll(LearningPath::class_name(), new DataClassRetrievesParameters())
                ->as_array();

        return $learningPaths;
    }

    /**
     * Copies one or multiple nodes from a given LearningPath to a given LearningPathTreeNode
     *
     * @param LearningPathTreeNode $toNode
     * @param LearningPath $fromLearningPath
     * @param User $user
     * @param array $selectedNodeIds
     * @param bool $copyInsteadOfReuse
     */
    public function copyNodesFromLearningPath(
        LearningPathTreeNode $toNode, LearningPath $fromLearningPath, User $user, $selectedNodeIds = array(),
        $copyInsteadOfReuse = false
    )
    {
        /** @var LearningPath $rootLearningPath */
        $rootLearningPath = $toNode->getLearningPathTree()->getRoot()->getContentObject();

        $fromLearningPathTree = $this->learningPathTreeBuilder->buildLearningPathTree($fromLearningPath);
        foreach ($selectedNodeIds as $selectedNodeId)
        {
            $selectedNode = $fromLearningPathTree->getLearningPathTreeNodeById($selectedNodeId);
            $this->copyNodeAndChildren($rootLearningPath, $toNode, $selectedNode, $user, $copyInsteadOfReuse);
        }
    }

    /**
     * Copies a given node and his children to the given learning path and tree node
     *
     * @param LearningPath $rootLearningPath
     * @param LearningPathTreeNode $toNode
     * @param LearningPathTreeNode $fromNode
     * @param User $user
     * @param bool $copyInsteadOfReuse
     */
    protected function copyNodeAndChildren(
        LearningPath $rootLearningPath, LearningPathTreeNode $toNode, LearningPathTreeNode $fromNode, User $user,
        $copyInsteadOfReuse = false
    )
    {
        $fromContentObject = $fromNode->getContentObject();

        $learningPathChild = $this->learningPathChildService->addContentObjectToLearningPath(
            $rootLearningPath, $toNode, $fromContentObject, $user
        );

        $newNode = $toNode->addChildNode(
            new LearningPathTreeNode($toNode->getLearningPathTree(), $fromContentObject, $learningPathChild)
        );

        foreach ($fromNode->getChildNodes() as $childNode)
        {
            $this->copyNodeAndChildren($rootLearningPath, $newNode, $childNode, $user, $copyInsteadOfReuse);
        }
    }
}