<?php

namespace Chamilo\Core\Repository\ContentObject\LearningPath\Service;

use Chamilo\Core\Repository\Common\Action\ContentObjectCopier;
use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\LearningPath;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\TreeNodeData;
use Chamilo\Core\Repository\ContentObject\Section\Storage\DataClass\Section;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
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
     * @var TreeBuilder
     */
    protected $treeBuilder;

    /**
     * @var TreeNodeDataService
     */
    protected $treeNodeDataService;

    /**
     * LearningPathService constructor.
     *
     * @param ContentObjectRepository $contentObjectRepository
     * @param TreeBuilder $treeBuilder
     * @param TreeNodeDataService $treeNodeDataService
     */
    public function __construct(
        ContentObjectRepository $contentObjectRepository, TreeBuilder $treeBuilder,
        TreeNodeDataService $treeNodeDataService
    )
    {
        $this->contentObjectRepository = $contentObjectRepository;
        $this->treeBuilder = $treeBuilder;
        $this->treeNodeDataService = $treeNodeDataService;
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
     * Copies one or multiple nodes from a given LearningPath to a given TreeNode
     *
     * @param TreeNode $toNode
     * @param LearningPath $fromLearningPath
     * @param User $user
     * @param array $selectedNodeIds
     * @param bool $copyInsteadOfReuse
     */
    public function copyNodesFromLearningPath(
        TreeNode $toNode, LearningPath $fromLearningPath, User $user, $selectedNodeIds = array(),
        $copyInsteadOfReuse = false
    )
    {
        /** @var LearningPath $rootLearningPath */
        $rootLearningPath = $toNode->getTree()->getRoot()->getContentObject();

        $fromTree = $this->treeBuilder->buildTree($fromLearningPath);
        foreach ($selectedNodeIds as $selectedNodeId)
        {
            $selectedNode = $fromTree->getTreeNodeById((int) $selectedNodeId);
            $this->copyNodeAndChildren($rootLearningPath, $toNode, $selectedNode, $user, $copyInsteadOfReuse);
        }
    }

    /**
     * Copies a given node and his children to the given learning path and tree node
     *
     * @param LearningPath $rootLearningPath
     * @param TreeNode $toNode
     * @param TreeNode $fromNode
     * @param User $user
     * @param bool $copyInsteadOfReuse
     */
    protected function copyNodeAndChildren(
        LearningPath $rootLearningPath, TreeNode $toNode, TreeNode $fromNode, User $user,
        $copyInsteadOfReuse = false
    )
    {
        $contentObject = $this->prepareContentObjectForCopy(
            $fromNode, $user, $toNode->getContentObject()->get_parent_id(), $copyInsteadOfReuse
        );

        $treeNodeData = $this->copyTreeNodeData($rootLearningPath, $toNode, $fromNode, $user, $contentObject);

        $newNode = new TreeNode($toNode->getTree(), $contentObject, $treeNodeData);
        $toNode->addChildNode($newNode);

        foreach ($fromNode->getChildNodes() as $childNode)
        {
            $this->copyNodeAndChildren($rootLearningPath, $newNode, $childNode, $user, $copyInsteadOfReuse);
        }
    }

    /**
     * Prepares the content object for the copy action.
     *
     * If the content object is a root node (e.g. a Learning Path) the
     * content object is always converted to a new Section.
     *
     * If the copy flag is set, the content object will be physically copied
     *
     * @param TreeNode $fromNode
     * @param User $user
     * @param int $categoryId
     * @param bool $copyInsteadOfReuse
     *
     * @return ContentObject
     */
    protected function prepareContentObjectForCopy(
        TreeNode $fromNode, User $user, $categoryId, $copyInsteadOfReuse = false
    )
    {
        if ($fromNode->isRootNode())
        {
            $contentObject = new Section();

            $contentObject->set_owner_id($user->getId());
            $contentObject->set_title($fromNode->getContentObject()->get_title());
            $contentObject->set_description($fromNode->getContentObject()->get_description());

            $contentObject->create();

            return $contentObject;
        }

        if ($copyInsteadOfReuse)
        {
            return $this->copyContentObjectFromNode($fromNode, $user, $categoryId);
        }

        return $fromNode->getContentObject();
    }

    /**
     * Copies a given content object
     *
     * @param TreeNode $node
     * @param User $user
     * @param int $categoryId
     *
     * @return Section|ContentObject
     */
    protected function copyContentObjectFromNode(TreeNode $node, User $user, $categoryId)
    {
        $contentObject = $node->getContentObject();

        $contentObjectCopier = new ContentObjectCopier(
            $user, array($contentObject->getId()), new PersonalWorkspace($contentObject->get_owner()),
            $contentObject->get_owner_id(), new PersonalWorkspace($user), $user->getId(),
            $categoryId
        );

        $newContentObjectIdentifiers = $contentObjectCopier->run();
        return $this->contentObjectRepository->findById(array_pop($newContentObjectIdentifiers));
    }

    /**
     * Copies a learning path child from a given node to a new node
     *
     * @param LearningPath $rootLearningPath
     * @param TreeNode $toNode
     * @param TreeNode $fromNode
     * @param User $user
     * @param ContentObject $contentObject
     *
     * @return TreeNodeData
     */
    protected function copyTreeNodeData(
        LearningPath $rootLearningPath, TreeNode $toNode, TreeNode $fromNode, User $user,
        ContentObject $contentObject
    ): TreeNodeData
    {
        if ($fromNode->isRootNode())
        {
            $treeNodeData = new TreeNodeData();
        }
        else
        {
            $treeNodeData = $fromNode->getTreeNodeData();
        }

        $treeNodeData->setId(null);
        $treeNodeData->setUserId((int) $user->getId());
        $treeNodeData->setLearningPathId((int) $rootLearningPath->getId());
        $treeNodeData->setParentTreeNodeDataId((int) $toNode->getId());
        $treeNodeData->setContentObjectId((int) $contentObject->getId());
        $treeNodeData->setAddedDate(time());

        $this->treeNodeDataService->createTreeNodeData($treeNodeData);

        return $treeNodeData;
    }

    /**
     * Adds a given content object to a learning path. Validates the content object to make sure that the
     * system does not create a cycle. Uses the Tree for calculations.
     *
     * @param LearningPath $rootLearningPath
     * @param TreeNode $currentTreeNode
     * @param ContentObject $childContentObject
     *
     * @param User $user
     *
     * @return TreeNodeData
     */
    public function addContentObjectToLearningPath(
        LearningPath $rootLearningPath, TreeNode $currentTreeNode,
        ContentObject $childContentObject, User $user
    )
    {
        $parentTreeNode = $currentTreeNode->getContentObject() instanceof Section ||
        $currentTreeNode->isRootNode() ?
            $currentTreeNode : $currentTreeNode->getParentNode();

        $treeNodeData = new TreeNodeData();

        $treeNodeData->setLearningPathId((int) $rootLearningPath->getId());
        $treeNodeData->setParentTreeNodeDataId((int) $parentTreeNode->getId());
        $treeNodeData->setContentObjectId((int) $childContentObject->getId());
        $treeNodeData->setUserId((int) $user->getId());
        $treeNodeData->setAddedDate(time());

        $this->treeNodeDataService->createTreeNodeData($treeNodeData);

        return $treeNodeData;
    }

    /**
     * @param string $contentObjectType
     * @param LearningPath $learningPath
     * @param TreeNode $currentTreeNode
     * @param User $user
     * @param string $title
     *
     * @return TreeNodeData
     */
    public function createAndAddContentObjectToLearningPath(
        $contentObjectType, LearningPath $learningPath, TreeNode $currentTreeNode, User $user, $title = '...'
    )
    {
        if (!class_exists($contentObjectType) || !is_subclass_of($contentObjectType, ContentObject::class_name()))
        {
            throw new \InvalidArgumentException(
                sprintf('The given ContentObject type %s is not a valid content object', $contentObjectType)
            );
        }

        /** @var ContentObject $contentObject */
        $contentObject = new $contentObjectType();
        $contentObject->set_title($title);
        $contentObject->set_owner_id($user->getId());

        if (!$this->contentObjectRepository->create($contentObject))
        {
            throw new \RuntimeException(sprintf('Could not create a new ContentObject of type %s', $contentObjectType));
        }

        return $this->addContentObjectToLearningPath(
            $learningPath, $currentTreeNode, $contentObject, $user
        );
    }

    /**
     * Updates a content object for a given learning path child. Uses the Tree.
     * Validates the content object to make sure that the system does not create a cycle.
     *
     * @param TreeNode $treeNode
     * @param ContentObject $newContentObject
     */
    public function updateContentObjectInTreeNode(
        TreeNode $treeNode, ContentObject $newContentObject
    )
    {
        $treeNodeData = $treeNode->getTreeNodeData();
        $treeNodeData->setContentObjectId((int) $newContentObject->getId());

        $this->treeNodeDataService->updateTreeNodeData($treeNodeData);
    }

    /**
     * Moves a content object from a learning path to a different learning path. The content object and the
     * parent learning path is identified by the learning path tree
     *
     * @param TreeNode $selectedTreeNode
     * @param TreeNode $parentTreeNode
     * @param int $newDisplayOrder
     */
    public function moveContentObjectToNewParent(
        TreeNode $selectedTreeNode, TreeNode $parentTreeNode,
        $newDisplayOrder = null
    )
    {
        $treeNodeData = $selectedTreeNode->getTreeNodeData();

        if ($treeNodeData->getParentTreeNodeDataId() != $parentTreeNode->getId())
        {
            $treeNodeData->setParentTreeNodeDataId(
                (int) $parentTreeNode->getId()
            );
        }

        if (isset($newDisplayOrder))
        {
            $treeNodeData->setDisplayOrder((int) $newDisplayOrder);
        }

        $this->treeNodeDataService->updateTreeNodeData($treeNodeData);
    }

    /**
     * Toggles the blocked status of a given ContentObject identified by a given TreeNode
     *
     * @param TreeNode $treeNode
     */
    public function toggleContentObjectBlockedStatus(TreeNode $treeNode)
    {
        $treeNodeData = $treeNode->getTreeNodeData();

        if (!$treeNodeData)
        {
            throw new \InvalidArgumentException(
                'The given learning path tree node does not have a valid learning path child object'
            );
        }

        $treeNodeData->setBlocked(!$treeNodeData->isBlocked());

        $this->treeNodeDataService->updateTreeNodeData($treeNodeData);
    }

    /**
     * Updates the title of a given ContentObject identified by a given TreeNode
     *
     * @param TreeNode $treeNode
     * @param string $newTitle
     */
    public function updateContentObjectTitle(TreeNode $treeNode, $newTitle = null)
    {
        if (empty($newTitle) || !is_string($newTitle))
        {
            throw new \InvalidArgumentException('The given title should not be empty and should be a valid string');
        }

        $contentObject = $treeNode->getContentObject();

        if (!$contentObject instanceof ContentObject)
        {
            throw new \RuntimeException(
                sprintf(
                    'The given TreeNode with id %s does not have a valid content object attached',
                    $treeNode->getId()
                )
            );
        }

        $contentObject->set_title($newTitle);

        if (!$this->contentObjectRepository->update($contentObject))
        {
            throw new \RuntimeException(
                sprintf('Could not update the Contentobject with id %S', $contentObject->getId())
            );
        }
    }

    /**
     * Deletes a content object from a learning path. The relation between the learning path and the content object
     * is defined by the learning path tree node
     *
     * @param TreeNode $treeNode
     */
    public function deleteContentObjectFromLearningPath(TreeNode $treeNode)
    {
        $treeNodeData = $treeNode->getTreeNodeData();

        if (!$treeNodeData)
        {
            throw new \InvalidArgumentException(
                'The given learning path tree node does not have a valid learning path child object'
            );
        }

        $this->treeNodeDataService->deleteTreeNodeData($treeNodeData);

        $childNodes = $treeNode->getChildNodes();
        foreach ($childNodes as $childNode)
        {
            $this->deleteContentObjectFromLearningPath($childNode);
        }
    }

    /**
     * Empties the given learning path by removing all the children
     *
     * @param LearningPath $learningPath
     */
    public function emptyLearningPath(LearningPath $learningPath)
    {
        $this->treeNodeDataService->deleteTreeNodesFromLearningPath($learningPath);
    }

    /**
     * Checks whether or not the given learning path is empty
     *
     * @param LearningPath $learningPath
     *
     * @return bool
     */
    public function isLearningPathEmpty(LearningPath $learningPath)
    {
        return $this->treeNodeDataService->countTreeNodesDataForLearningPath($learningPath) == 0;
    }
}