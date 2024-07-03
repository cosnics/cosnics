<?php
namespace Chamilo\Core\Repository\ContentObject\LearningPath\Storage\Repository;

use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\LearningPath;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\TreeNodeData;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\StorageParameters;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 * Repository class to manage the data for the TreeNodeData
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class TreeNodeDataRepository extends CommonDataClassRepository
{
    /**
     * Clears the learning path children cache
     *
     * @return bool
     */
    public function clearTreeNodesDataCache()
    {
        return $this->dataClassRepository->getDataClassRepositoryCache()->truncateClass(TreeNodeData::class);
    }

    /**
     * @param LearningPath $learningPath
     *
     * @return int
     */
    public function countTreeNodesDataForLearningPath(LearningPath $learningPath)
    {
        $condition = $this->getConditionForLearningPath($learningPath);

        return $this->dataClassRepository->count(TreeNodeData::class, new StorageParameters(condition: $condition));
    }

    /**
     * Deletes the record in the TreeNodeData table for the LearningPath (as individual step)
     *
     * @param LearningPath $learningPath
     *
     * @return bool
     */
    public function deleteTreeNodeDataForLearningPath(LearningPath $learningPath)
    {
        $conditions = [];

        $conditions[] = $this->getConditionForLearningPath($learningPath);

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(TreeNodeData::class, TreeNodeData::PROPERTY_CONTENT_OBJECT_ID),
            new StaticConditionVariable($learningPath->getId())
        );

        $condition = new AndCondition($conditions);

        return $this->dataClassRepository->deletes(TreeNodeData::class, $condition);
    }

    /**
     * Deletes every child object that belongs to a given learning path
     *
     * @param LearningPath $learningPath
     *
     * @return bool
     */
    public function deleteTreeNodesFromLearningPath(LearningPath $learningPath)
    {
        $condition = $this->getConditionForLearningPath($learningPath);

        return $this->dataClassRepository->deletes(TreeNodeData::class, $condition);
    }

    /**
     * Retrieves a learning path child by a given identifier
     *
     * @param int $treeNodeDataId
     *
     * @return TreeNodeData | \Chamilo\Libraries\Storage\DataClass\DataClass
     */
    public function findTreeNodeData($treeNodeDataId)
    {
        return $this->dataClassRepository->retrieveById(TreeNodeData::class, $treeNodeDataId);
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\LearningPath $learningPath
     *
     * @return \Chamilo\Libraries\Storage\DataClass\DataClass
     */
    public function findTreeNodeDataForLearningPathRoot(LearningPath $learningPath)
    {
        $conditions = [
            new EqualityCondition(
                new PropertyConditionVariable(TreeNodeData::class, TreeNodeData::PROPERTY_LEARNING_PATH_ID),
                new StaticConditionVariable($learningPath->getId())
            ),
            new EqualityCondition(
                new PropertyConditionVariable(TreeNodeData::class, TreeNodeData::PROPERTY_CONTENT_OBJECT_ID),
                new StaticConditionVariable($learningPath->getId())
            ),
        ];

        $condition = new AndCondition($conditions);

        return $this->dataClassRepository->retrieve(
            TreeNodeData::class, new StorageParameters(condition: $condition)
        );
    }

    /**
     * Retrieves the learning path children for given ContentObject's identified by id
     *
     * @param int[] $contentObjectIds
     *
     * @return TreeNodeData[] | \Doctrine\Common\Collections\ArrayCollection
     */
    public function findTreeNodesDataByContentObjects($contentObjectIds)
    {
        $condition = new InCondition(
            new PropertyConditionVariable(TreeNodeData::class, TreeNodeData::PROPERTY_CONTENT_OBJECT_ID),
            $contentObjectIds
        );

        return $this->dataClassRepository->retrieves(
            TreeNodeData::class, new StorageParameters(condition: $condition)
        );
    }

    /**
     * Retrieves the learning path children for a given userId
     *
     * @param int $userId
     *
     * @return TreeNodeData[] | \Doctrine\Common\Collections\ArrayCollection
     */
    public function findTreeNodesDataByUserId($userId)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(TreeNodeData::class, TreeNodeData::PROPERTY_USER_ID),
            new StaticConditionVariable($userId)
        );

        return $this->dataClassRepository->retrieves(
            TreeNodeData::class, new StorageParameters(condition: $condition)
        );
    }

    /**
     * Retrieves the learning path children for a given learning path
     *
     * @param LearningPath $learningPath
     *
     * @return TreeNodeData[] | \Doctrine\Common\Collections\ArrayCollection
     */
    public function findTreeNodesDataForLearningPath(LearningPath $learningPath)
    {
        $condition = $this->getConditionForLearningPath($learningPath);

        return $this->dataClassRepository->retrieves(
            TreeNodeData::class, new StorageParameters(condition: $condition)
        );
    }

    /**
     * Builds and returns the condition for the TreeNodeData objects of a given LearningPath
     *
     * @param LearningPath $learningPath
     *
     * @return EqualityCondition
     */
    protected function getConditionForLearningPath(LearningPath $learningPath)
    {
        return new EqualityCondition(
            new PropertyConditionVariable(TreeNodeData::class, TreeNodeData::PROPERTY_LEARNING_PATH_ID),
            new StaticConditionVariable($learningPath->getId())
        );
    }
}
