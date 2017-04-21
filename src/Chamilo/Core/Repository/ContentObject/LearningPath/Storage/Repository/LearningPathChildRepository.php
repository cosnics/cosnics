<?php

namespace Chamilo\Core\Repository\ContentObject\LearningPath\Storage\Repository;

use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\LearningPath;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\LearningPathChild;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\Iterator\DataClassIterator;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 * Repository class to manage the data for the LearningPathChild
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class LearningPathChildRepository extends CommonDataClassRepository
{
    /**
     * Retrieves the learning path children for a given learning path
     *
     * @param LearningPath $learningPath
     *
     * @return LearningPathChild[] | DataClassIterator
     */
    public function findLearningPathChildrenForLearningPath(LearningPath $learningPath)
    {
        $condition = $this->getConditionForLearningPath($learningPath);

        return $this->dataClassRepository->retrieves(
            LearningPathChild::class_name(), new DataClassRetrievesParameters(
                $condition, null, null,
                new OrderBy(
                    new PropertyConditionVariable(
                        LearningPathChild::class_name(), LearningPathChild::PROPERTY_PARENT_LEARNING_PATH_CHILD_ID
                    ),
                    new PropertyConditionVariable(
                        LearningPathChild::class_name(), LearningPathChild::PROPERTY_DISPLAY_ORDER
                    )
                )
            )
        );
    }

    /**
     * @param LearningPath $learningPath
     *
     * @return int
     */
    public function countLearningPathChildrenForLearningPath(LearningPath $learningPath)
    {
        $condition = $this->getConditionForLearningPath($learningPath);

        return $this->dataClassRepository->count(
            LearningPathChild::class_name(), new DataClassCountParameters($condition)
        );
    }

    /**
     * Retrieves the learning path children for given ContentObject's identified by id
     *
     * @param int[] $contentObjectIds
     *
     * @return LearningPathChild[] | DataClassIterator
     */
    public function findLearningPathChildrenByContentObjects($contentObjectIds)
    {
        $condition = new InCondition(
            new PropertyConditionVariable(
                LearningPathChild::class_name(), LearningPathChild::PROPERTY_CONTENT_OBJECT_ID
            ),
            $contentObjectIds
        );

        return $this->dataClassRepository->retrieves(
            LearningPathChild::class_name(), new DataClassRetrievesParameters($condition)
        );
    }

    /**
     * Retrieves the learning path children for a given userId
     *
     * @param int $userId
     *
     * @return LearningPathChild[] | DataClassIterator
     */
    public function findLearningPathChildrenByUserId($userId)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(
                LearningPathChild::class_name(), LearningPathChild::PROPERTY_USER_ID
            ),
            new StaticConditionVariable($userId)
        );

        return $this->dataClassRepository->retrieves(
            LearningPathChild::class_name(), new DataClassRetrievesParameters($condition)
        );
    }

    /**
     * Retrieves a learning path child by a given identifier
     *
     * @param int $learningPathChildId
     *
     * @return LearningPathChild | DataClass
     */
    public function findLearningPathChild($learningPathChildId)
    {
        return $this->dataClassRepository->retrieveById(LearningPathChild::class_name(), $learningPathChildId);
    }

    /**
     * Clears the learning path children cache
     *
     * @return bool
     */
    public function clearLearningPathChildrenCache()
    {
        return $this->dataClassRepository->getDataClassRepositoryCache()->truncate(
            LearningPathChild::class_name()
        );
    }

    /**
     * Deletes every child object that belongs to a given learning path
     *
     * @param LearningPath $learningPath
     *
     * @return bool
     */
    public function deleteChildrenFromLearningPath(LearningPath $learningPath)
    {
        $condition = $this->getConditionForLearningPath($learningPath);
        return $this->dataClassRepository->deletes(LearningPathChild::class_name(), $condition);
    }

    /**
     * Builds and returns the condition for the LearningPathChild objects of a given LearningPath
     *
     * @param LearningPath $learningPath
     *
     * @return EqualityCondition
     */
    protected function getConditionForLearningPath(LearningPath $learningPath)
    {
        return new EqualityCondition(
            new PropertyConditionVariable(
                LearningPathChild::class_name(), LearningPathChild::PROPERTY_LEARNING_PATH_ID
            ),
            new StaticConditionVariable($learningPath->getId())
        );
    }
}