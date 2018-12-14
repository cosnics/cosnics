<?php

namespace Chamilo\Core\Repository\ContentObject\LearningPath\Service;

use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode;
use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNodeConfigurationInterface;
use Chamilo\Core\Repository\ContentObject\LearningPath\Exception\TreeNodeNotFoundException;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\LearningPath;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\TreeNodeData;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\Repository\TreeNodeDataRepository;
use Chamilo\Core\User\Storage\DataClass\User;
use JMS\Serializer\Serializer;

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
     * @var \JMS\Serializer\Serializer
     */
    protected $serializer;

    /**
     * TreeNodeDataService constructor.
     *
     * @param TreeNodeDataRepository $treeNodeDataRepository
     * @param \JMS\Serializer\Serializer $serializer
     */
    public function __construct(TreeNodeDataRepository $treeNodeDataRepository, Serializer $serializer)
    {
        $this->treeNodeDataRepository = $treeNodeDataRepository;
        $this->serializer = $serializer;
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
     * @return \Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\TreeNodeData
     *
     * @throws \Chamilo\Core\Repository\ContentObject\LearningPath\Exception\TreeNodeNotFoundException
     */
    public function getTreeNodeDataById($treeNodeDataId)
    {
        $treeNodeData = $this->treeNodeDataRepository->findTreeNodeData($treeNodeDataId);
        if (!$treeNodeData)
        {
            throw new TreeNodeNotFoundException($treeNodeDataId);
        }

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

        if ($rootLearningPath->enforcesDefaultTraversingOrder())
        {
            $treeNodeData->setEnforceDefaultTraversingOrder(true);
        }

        $this->createTreeNodeData($treeNodeData);

        return $treeNodeData;
    }

    /**
     * Updates the TreeNodeData for a giving learning path. Used to sync the enforceDefaultTraversingOrder option
     *
     * @param LearningPath $learningPath
     */
    public function updateTreeNodeDataForLearningPath(LearningPath $learningPath)
    {
        $treeNodeData = $this->treeNodeDataRepository->findTreeNodeDataForLearningPathRoot($learningPath);

        if (!$treeNodeData instanceof TreeNodeData)
        {
            throw new \RuntimeException('No TreeNodeData was found for LearningPath ' . $learningPath->getId());
        }

        $treeNodeData->setEnforceDefaultTraversingOrder($learningPath->enforcesDefaultTraversingOrder());
        $this->updateTreeNodeData($treeNodeData);
    }

    /**
     * Deletes the record in the TreeNodeData table for the LearningPath (as individual step)
     *
     * @param LearningPath $learningPath
     */
    public function deleteTreeNodeDataForLearningPath(LearningPath $learningPath)
    {
        if (!$this->treeNodeDataRepository->deleteTreeNodeDataForLearningPath($learningPath))
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
     * Helper function to create the learning path child in the database
     *
     * @param TreeNodeData $treeNodeData
     */
    public function updateTreeNodeData(TreeNodeData $treeNodeData)
    {
        if (!$this->treeNodeDataRepository->update($treeNodeData))
        {
            throw new \RuntimeException(
                sprintf(
                    'Could not update a TreeNodeDataObject for learning path %s parent %s and child %s',
                    $treeNodeData->getLearningPathId(), $treeNodeData->getParentTreeNodeDataId(),
                    $treeNodeData->getContentObjectId()
                )
            );
        }
    }

    /**
     * Helper function to create the learning path child in the database
     *
     * @param TreeNodeData $treeNodeData
     */
    public function deleteTreeNodeData(TreeNodeData $treeNodeData)
    {
        if (!$this->treeNodeDataRepository->delete($treeNodeData))
        {
            throw new \RuntimeException(
                sprintf(
                    'Could not update a TreeNodeDataObject for learning path %s parent %s and child %s',
                    $treeNodeData->getLearningPathId(), $treeNodeData->getParentTreeNodeDataId(),
                    $treeNodeData->getContentObjectId()
                )
            );
        }
    }

    /**
     * Deletes the tree nodes from a given LearningPath
     *
     * @param LearningPath $learningPath
     */
    public function deleteTreeNodesFromLearningPath(LearningPath $learningPath)
    {
        if (!$this->treeNodeDataRepository->deleteTreeNodesFromLearningPath($learningPath))
        {
            throw new \RuntimeException('Could not delete the tree nodes for learning path ' . $learningPath->getId());
        }
    }

    /**
     * Returns the number of tree nodes data for a given LearningPath
     *
     * @param LearningPath $learningPath
     *
     * @return bool
     */
    public function countTreeNodesDataForLearningPath(LearningPath $learningPath)
    {
        return $this->treeNodeDataRepository->countTreeNodesDataForLearningPath($learningPath);
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode $treeNode
     */
    public function storeConfigurationForTreeNode(TreeNode $treeNode)
    {
        $configuration = $treeNode->getConfiguration();
        if (!$configuration instanceof TreeNodeConfigurationInterface)
        {
            throw new \InvalidArgumentException(
                sprintf('The given treenode configuration for treenode %s is invalid.', $treeNode->getId())
            );
        }

        $serializedConfiguration = $this->serializer->serialize($configuration, 'json');
        $treeNodeData = $treeNode->getTreeNodeData();
        if(!$treeNodeData instanceof  TreeNodeData)
        {
            throw new \RuntimeException('The given treenode does not support a valid TreeNodeData class');
        }

        $treeNodeData->setConfiguration($serializedConfiguration);
        $treeNodeData->setConfigurationClass(get_class($configuration));

        $this->updateTreeNodeData($treeNodeData);
    }
}