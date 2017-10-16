<?php

namespace Chamilo\Core\Repository\ContentObject\LearningPath\Storage\Repository;

use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\LearningPath;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\TreeNodeData;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
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
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\LearningPath $learningPath
     *
     * @return \Chamilo\Libraries\Storage\DataClass\DataClass
     */
    public function findTreeNodeDataForLearningPathRoot(LearningPath $learningPath)
    {
        $conditions = [
            new EqualityCondition(
                new PropertyConditionVariable(TreeNodeData::class_name(), TreeNodeData::PROPERTY_LEARNING_PATH_ID),
                new StaticConditionVariable($learningPath->getId())
            ),
            new EqualityCondition(
                new PropertyConditionVariable(TreeNodeData::class_name(), TreeNodeData::PROPERTY_CONTENT_OBJECT_ID),
                new StaticConditionVariable($learningPath->getId())
            ),
        ];

        $condition = new AndCondition($conditions);

        return $this->dataClassRepository->retrieve(TreeNodeData::class, new DataClassRetrieveParameters($condition));
    }

    /**
     * Retrieves the learning path children for a given learning path
     *
     * @param LearningPath $learningPath
     *
     * @return TreeNodeData[] | \Chamilo\Libraries\Storage\Iterator\DataClassIterator
     */
    public function findTreeNodesDataForLearningPath(LearningPath $learningPath)
    {
        $condition = $this->getConditionForLearningPath($learningPath);

        return $this->dataClassRepository->retrieves(
            TreeNodeData::class_name(),
            new DataClassRetrievesParameters($condition, null, null)
        );
    }

    /**
     * @param LearningPath $learningPath
     *
     * @return int
     */
    public function countTreeNodesDataForLearningPath(LearningPath $learningPath)
    {
        $condition = $this->getConditionForLearningPath($learningPath);

        return $this->dataClassRepository->count(TreeNodeData::class_name(), new DataClassCountParameters($condition));
    }

    /**
     * Retrieves the learning path children for given ContentObject's identified by id
     *
     * @param int[] $contentObjectIds
     *
     * @return TreeNodeData[] | \Chamilo\Libraries\Storage\Iterator\DataClassIterator
     */
    public function findTreeNodesDataByContentObjects($contentObjectIds)
    {
        $condition = new InCondition(
            new PropertyConditionVariable(TreeNodeData::class_name(), TreeNodeData::PROPERTY_CONTENT_OBJECT_ID),
            $contentObjectIds
        );

        return $this->dataClassRepository->retrieves(
            TreeNodeData::class_name(),
            new DataClassRetrievesParameters($condition)
        );
    }

    /**
     * Retrieves the learning path children for a given userId
     *
     * @param int $userId
     *
     * @return TreeNodeData[] | \Chamilo\Libraries\Storage\Iterator\DataClassIterator
     */
    public function findTreeNodesDataByUserId($userId)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(TreeNodeData::class_name(), TreeNodeData::PROPERTY_USER_ID),
            new StaticConditionVariable($userId)
        );

        return $this->dataClassRepository->retrieves(
            TreeNodeData::class_name(),
            new DataClassRetrievesParameters($condition)
        );
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
        return $this->dataClassRepository->retrieveById(TreeNodeData::class_name(), $treeNodeDataId);
    }

    /**
     * Clears the learning path children cache
     *
     * @return bool
     */
    public function clearTreeNodesDataCache()
    {
        return $this->dataClassRepository->getDataClassRepositoryCache()->truncate(TreeNodeData::class_name());
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

        return $this->dataClassRepository->deletes(TreeNodeData::class_name(), $condition);
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
            new PropertyConditionVariable(TreeNodeData::class_name(), TreeNodeData::PROPERTY_LEARNING_PATH_ID),
            new StaticConditionVariable($learningPath->getId())
        );
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
        $conditions = array();

        $conditions[] = $this->getConditionForLearningPath($learningPath);

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(TreeNodeData::class_name(), TreeNodeData::PROPERTY_CONTENT_OBJECT_ID),
            new StaticConditionVariable($learningPath->getId())
        );

        $condition = new AndCondition($conditions);

        return $this->dataClassRepository->deletes(TreeNodeData::class_name(), $condition);
    }
}
