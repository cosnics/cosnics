<?php

namespace Chamilo\Core\Repository\ContentObject\LearningPath\Service;

use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\Tree;
use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\LearningPath;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\TreeNodeData;
use Chamilo\Core\Repository\ContentObject\Section\Storage\DataClass\Section;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
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
     * @var Tree[]
     */
    protected $cachedTrees;

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
     * Returns the tree for a given learning path
     *
     * @param LearningPath $learningPath
     *
     * @return Tree
     */
    public function getTree(LearningPath $learningPath)
    {
        if(!isset($this->cachedTrees[$learningPath->getId()]))
        {
            $this->cachedTrees[$learningPath->getId()] = $this->buildTree($learningPath);
        }

        return $this->cachedTrees[$learningPath->getId()];
    }

    /**
     * Builds the tree for a given learning path, calling this function multiple times will result in multiple
     * different trees
     *
     * @param LearningPath $learningPath
     *
     * @return Tree
     */
    public function buildTree(LearningPath $learningPath)
    {
        return $this->treeBuilder->buildTree($learningPath);
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
     * Toggles the enforce default traversing order of a given ContentObject identified by a given TreeNode
     *
     * @param TreeNode $treeNode
     */
    public function toggleEnforceDefaultTraversingOrder(TreeNode $treeNode)
    {
        $treeNodeData = $treeNode->getTreeNodeData();

        if (!$treeNodeData)
        {
            throw new \InvalidArgumentException(
                'The given learning path tree node does not have a valid learning path child object'
            );
        }

        $treeNodeData->setEnforceDefaultTraversingOrder(!$treeNodeData->enforcesDefaultTraversingOrder());

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