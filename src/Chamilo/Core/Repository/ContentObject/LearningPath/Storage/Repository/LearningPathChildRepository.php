<?php

namespace Chamilo\Core\Repository\ContentObject\LearningPath\Storage\Repository;

use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\LearningPath;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\LearningPathChild;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\DataClass\Property\DataClassProperties;
use Chamilo\Libraries\Storage\DataClass\Property\DataClassProperty;
use Chamilo\Libraries\Storage\DataManager\Repository\DataClassRepository;
use Chamilo\Libraries\Storage\Iterator\DataClassIterator;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
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
    public function retrieveLearningPathChildrenForLearningPath(LearningPath $learningPath)
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