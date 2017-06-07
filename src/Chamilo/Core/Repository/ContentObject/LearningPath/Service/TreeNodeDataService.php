<?php

namespace Chamilo\Core\Repository\ContentObject\LearningPath\Service;

use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\LearningPath;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\TreeNodeData;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\Repository\TreeNodeDataRepository;
use Chamilo\Core\Repository\ContentObject\Section\Storage\DataClass\Section;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\User\Storage\DataClass\User;
use Money\InvalidArgumentException;

/**
 * Service class to manage TreeNodeData classes
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class TreeNodeDataService
{
    /**
     * @var TreeNodeDataRepository
     */
    protected $treeNodeDataRepository;

    /**
     * TreeNodeDataService constructor.
     *
     * @param TreeNodeDataRepository $treeNodeDataRepository
     */
    public function __construct(TreeNodeDataRepository $treeNodeDataRepository)
    {
        $this->treeNodeDataRepository = $treeNodeDataRepository;
    }

    /**
     * Returns the TreeNodeData objects that belong to a given learning path
     *
     * @param LearningPath $learningPath
     *
     * @return TreeNodeData[] | \Chamilo\Libraries\Storage\Iterator\DataClassIterator
     */
    public function getTreeNodesDataForLearningPath(LearningPath $learningPath)
    {
        return $this->treeNodeDataRepository->findTreeNodesDataForLearningPath($learningPath);
    }

    /**
     * Returns the TreeNodeData objects that belong to a given content object ids (not as parent)
     *
     * @param int[] $contentObjectIds
     *
     * @return TreeNodeData[]|\Chamilo\Libraries\Storage\Iterator\DataClassIterator
     */
    public function getTreeNodesDataByContentObjects($contentObjectIds)
    {
        return $this->treeNodeDataRepository->findTreeNodesDataByContentObjects($contentObjectIds);
    }

    /**
     * Returns the TreeNodeData objects that belong to a given user
     *
     * @param int $userId
     *
     * @return TreeNodeData[]|\Chamilo\Libraries\Storage\Iterator\DataClassIterator
     */
    public function getTreeNodesDataByUserId($userId)
    {
        if (!is_int($userId) || $userId <= 0)
        {
            throw new \InvalidArgumentException('The given user id must be a valid integer and must be bigger than 0');
        }

        return $this->treeNodeDataRepository->findTreeNodesDataByUserId($userId);
    }

    /**
     * Returns a TreeNodeData by a given identifier
     *
     * @param int $treeNodeDataId
     *
     * @return TreeNodeData
     */
    public function getTreeNodeDataById($treeNodeDataId)
    {
        $treeNodeData = $this->treeNodeDataRepository->findTreeNodeData($treeNodeDataId);
        if (!$treeNodeData)
        {
            throw new \RuntimeException(
                sprintf('The given learning path child with id %s could not be found', $treeNodeDataId)
            );
        }

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

        $this->createTreeNodeData($treeNodeData);

        return $treeNodeData;
    }

    /**
     * Creates the TreeNodeData record for the LearningPath itself
     *
     * @param LearningPath $rootLearningPath
     * @param User $user
     *
     * @return TreeNodeData
     */
    public function createTreeNodeDataForLearningPath(LearningPath $rootLearningPath, User $user)
    {
        $treeNodeData = new TreeNodeData();

        $treeNodeData->setLearningPathId((int) $rootLearningPath->getId());
        $treeNodeData->setParentTreeNodeDataId(0);
        $treeNodeData->setContentObjectId((int) $rootLearningPath->getId());
        $treeNodeData->setUserId((int) $user->getId());
        $treeNodeData->setAddedDate(time());

        $this->createTreeNodeData($treeNodeData);

        return $treeNodeData;
    }

    /**
     * Deletes the record in the TreeNodeData table for the LearningPath (as individual step)
     *
     * @param LearningPath $learningPath
     */
    public function deleteTreeNodeDataForLearningPath(LearningPath $learningPath)
    {
        if(!$this->treeNodeDataRepository->deleteTreeNodeDataForLearningPath($learningPath))
        {
            throw new \RuntimeException(
                sprintf(
                    'Could not delete the TreeNodeDataObject for learning path %s',
                    $learningPath->getId()
                )
            );
        }
    }

    /**
     * Helper function to create the learning path child in the database
     *
     * @param TreeNodeData $treeNodeData
     */
    public function createTreeNodeData(TreeNodeData $treeNodeData)
    {
        if (!$this->treeNodeDataRepository->create($treeNodeData))
        {
            throw new \RuntimeException(
                sprintf(
                    'Could not create a TreeNodeDataObject for learning path %s parent %s and child %s',
                    $treeNodeData->getLearningPathId(), $treeNodeData->getParentTreeNodeDataId(),
                    $treeNodeData->getContentObjectId()
                )
            );
        }
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
            throw new InvalidArgumentException(
                sprintf('The given ContentObject type %s is not a valid content object', $contentObjectType)
            );
        }

        /** @var ContentObject $contentObject */
        $contentObject = new $contentObjectType();
        $contentObject->set_title($title);
        $contentObject->set_owner_id($user->getId());

        if (!$this->treeNodeDataRepository->create($contentObject))
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
    public function updateContentObjectInTreeNodeData(
        TreeNode $treeNode, ContentObject $newContentObject
    )
    {
        $treeNodeData = $treeNode->getTreeNodeData();
        $treeNodeData->setContentObjectId((int) $newContentObject->getId());

        if (!$this->treeNodeDataRepository->update($treeNodeData))
        {
            throw new \RuntimeException(
                sprintf(
                    'Could not update the TreeNodeDataObject for learning path %s parent %s and child %s',
                    $treeNodeData->getLearningPathId(), $treeNodeData->getParentTreeNodeDataId(),
                    $treeNodeData->getContentObjectId()
                )
            );
        }
    }

    /**
     * Moves a content object from a learning path to a different learning path. The content object and the
     * parent learning path is identified by the learning path tree
     *
     * @param TreeNode $selectedTreeNode
     * @param TreeNode $parentTreeNode
     * @param int $newDisplayOrder
     */
    public function moveContentObjectToOtherLearningPath(
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

        if (!$this->treeNodeDataRepository->update($treeNodeData))
        {
            throw new \RuntimeException(
                sprintf(
                    'Could not update the TreeNodeDataObject for learning path %s parent %s and child %s',
                    $treeNodeData->getLearningPathId(), $treeNodeData->getParentTreeNodeDataId(),
                    $treeNodeData->getContentObjectId()
                )
            );
        }
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

        if (!$this->treeNodeDataRepository->update($treeNodeData))
        {
            throw new \RuntimeException(
                sprintf(
                    'Could not update the TreeNodeDataObject for learning path %s parent %s and child %s',
                    $treeNodeData->getLearningPathId(), $treeNodeData->getParentTreeNodeDataId(),
                    $treeNodeData->getContentObjectId()
                )
            );
        }
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

        if (!$this->treeNodeDataRepository->update($contentObject))
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
     *
     * @todo move to learning path service
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

        if (!$this->treeNodeDataRepository->delete($treeNodeData))
        {
            throw new \RuntimeException(
                sprintf(
                    'Could not delete the TreeNodeDataObject for learning path %s parent %s and child %s',
                    $treeNodeData->getLearningPathId(), $treeNodeData->getParentTreeNodeDataId(),
                    $treeNodeData->getContentObjectId()
                )
            );
        }

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
        if (!$this->treeNodeDataRepository->deleteChildrenFromLearningPath($learningPath))
        {
            throw new \RuntimeException('Could not empty the learning path with id ' . $learningPath->getId());
        }
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
        return $this->treeNodeDataRepository->countTreeNodesDataForLearningPath($learningPath) == 0;
    }
}