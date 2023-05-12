<?php
namespace Chamilo\Core\Repository\ContentObject\LearningPath\Service;

use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\LearningPath;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\TreeNodeData;
use Chamilo\Core\Repository\ContentObject\Section\Storage\DataClass\Section;
use Chamilo\Core\Repository\Service\ContentObjectCopierWrapper;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Workspace\Repository\ContentObjectRepository;
use Chamilo\Core\Repository\Workspace\Storage\DataClass\Workspace;
use Chamilo\Core\User\Storage\DataClass\User;

/**
 * Service to copy tree nodes from a given LearningPath to another LearningPath
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class TreeNodeCopier
{
    /**
     * @var ContentObjectCopierWrapper
     */
    protected $contentObjectCopierWrapper;

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
     * @param ContentObjectCopierWrapper $contentObjectCopierWrapper
     */
    public function __construct(
        ContentObjectRepository $contentObjectRepository, TreeBuilder $treeBuilder,
        TreeNodeDataService $treeNodeDataService, ContentObjectCopierWrapper $contentObjectCopierWrapper
    )
    {
        $this->contentObjectRepository = $contentObjectRepository;
        $this->treeBuilder = $treeBuilder;
        $this->treeNodeDataService = $treeNodeDataService;
        $this->contentObjectCopierWrapper = $contentObjectCopierWrapper;
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
        $newContentObjectIdentifiers =
            $this->contentObjectCopierWrapper->copyContentObject($node->getContentObject(), $user, $categoryId);

        return $this->contentObjectRepository->findById(array_pop($newContentObjectIdentifiers));
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
        LearningPath $rootLearningPath, TreeNode $toNode, TreeNode $fromNode, User $user, $copyInsteadOfReuse = false
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
     * Copies one or multiple nodes from a given LearningPath to a given TreeNode
     *
     * @param TreeNode $toNode
     * @param LearningPath $fromLearningPath
     * @param User $user
     * @param array $selectedNodeIds
     * @param bool $copyInsteadOfReuse
     */
    public function copyNodesFromLearningPath(
        TreeNode $toNode, LearningPath $fromLearningPath, User $user, $selectedNodeIds = [], $copyInsteadOfReuse = false
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
        LearningPath $rootLearningPath, TreeNode $toNode, TreeNode $fromNode, User $user, ContentObject $contentObject
    ): TreeNodeData
    {
        $treeNodeData = $fromNode->getTreeNodeData();

        $treeNodeData->setId(null);
        $treeNodeData->setUserId((int) $user->getId());
        $treeNodeData->setLearningPathId((int) $rootLearningPath->getId());
        $treeNodeData->setParentTreeNodeDataId((int) $toNode->getId());
        $treeNodeData->setContentObjectId((int) $contentObject->getId());
        $treeNodeData->setAddedDate(time());

        if ($fromNode->isRootNode())
        {
            /** @var LearningPath $learningPath */
            $learningPath = $fromNode->getContentObject();
            if ($learningPath->enforcesDefaultTraversingOrder() ||
                $fromNode->getTreeNodeData()->enforcesDefaultTraversingOrder())
            {
                $treeNodeData->setEnforceDefaultTraversingOrder(true);
            }
        }
        else
        {
            $treeNodeData->setEnforceDefaultTraversingOrder(
                $fromNode->getTreeNodeData()->enforcesDefaultTraversingOrder()
            );
        }

        $this->treeNodeDataService->createTreeNodeData($treeNodeData);

        return $treeNodeData;
    }

    /**
     * Prepares the content object for the copy action.
     * If the content object is a root node (e.g. a Learning Path) the
     * content object is always converted to a new Section.
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

            $this->contentObjectRepository->create($contentObject);
            $this->contentObjectRepository->copyIncludesFromContentObject(
                $fromNode->getContentObject(), $contentObject
            );

            return $contentObject;
        }

        if ($copyInsteadOfReuse)
        {
            return $this->copyContentObjectFromNode($fromNode, $user, $categoryId);
        }

        return $fromNode->getContentObject();
    }
}