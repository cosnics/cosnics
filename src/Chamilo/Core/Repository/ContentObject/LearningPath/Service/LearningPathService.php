<?php

namespace Chamilo\Core\Repository\ContentObject\LearningPath\Service;

use Chamilo\Core\Repository\Common\Action\ContentObjectCopier;
use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\LearningPathTreeNode;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\LearningPath;
use Chamilo\Core\Repository\Workspace\PersonalWorkspace;
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
        $this->learningPathTreeBuilder = $learningPathTreeBuilder;
        $this->learningPathChildService = $learningPathChildService;
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
            $selectedNode = $fromLearningPathTree->getLearningPathTreeNodeById((int) $selectedNodeId);
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
        $contentObject = $fromNode->getContentObject();

        if($copyInsteadOfReuse)
        {
            $contentObjectCopier = new ContentObjectCopier(
                $user, array($contentObject->getId()), new PersonalWorkspace($contentObject->get_owner()),
                $contentObject->get_owner_id(), new PersonalWorkspace($user), $user->getId(),
                    $toNode->getContentObject()->get_parent_id()
            );

            $newContentObjectIdentifiers = $contentObjectCopier->run();
            $contentObject = $this->contentObjectRepository->findById(array_pop($newContentObjectIdentifiers));
        }

        $learningPathChild = $fromNode->getLearningPathChild();

        $learningPathChild->setId(null);
        $learningPathChild->setUserId((int) $user->getId());
        $learningPathChild->setLearningPathId((int) $rootLearningPath->getId());
        $learningPathChild->setParentLearningPathChildId((int) $toNode->getId());
        $learningPathChild->setContentObjectId((int) $contentObject->getId());
        $learningPathChild->setAddedDate(time());

        $this->learningPathChildService->createLearningPathChild($learningPathChild);

        $newNode = new LearningPathTreeNode($toNode->getLearningPathTree(), $contentObject, $learningPathChild);
        $toNode->addChildNode($newNode);

        foreach ($fromNode->getChildNodes() as $childNode)
        {
            $this->copyNodeAndChildren($rootLearningPath, $newNode, $childNode, $user, $copyInsteadOfReuse);
        }
    }
}