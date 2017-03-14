<?php

namespace Chamilo\Core\Repository\ContentObject\LearningPath\Storage\Repository;

use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\LearningPath;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\LearningPathChild;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\DataManager\Repository\DataClassRepository;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 * Repository class to manage the data for the LearningPathChild
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class LearningPathChildRepository
{
    /**
     * @var DataClassRepository
     */
    protected $dataClassRepository;

    /**
     * LearningPathChildRepository constructor.
     *
     * @param DataClassRepository $dataClassRepository
     */
    public function __construct(DataClassRepository $dataClassRepository)
    {
        $this->dataClassRepository = $dataClassRepository;
    }

    /**
     * Retrieves the learning path children for a given learning path
     *
     * @param LearningPath $learningPath
     *
     * @return LearningPathChild[]
     */
    public function retrieveLearningPathChildrenForLearningPath(LearningPath $learningPath)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(
                LearningPathChild::class_name(), LearningPathChild::PROPERTY_PARENT_LEARNING_PATH_ID
            ),
            new StaticConditionVariable($learningPath->getId())
        );

        return $this->dataClassRepository->retrieves(
            LearningPathChild::class_name(), new DataClassRetrievesParameters($condition)
        );
    }

    /*****************************************************************************************************************
     * Fallback functionality for dataclass methods                                                                  *
     *****************************************************************************************************************/

    /**
     * @param DataClass $dataClass
     *
     * @return bool
     */
    public function create(DataClass $dataClass)
    {
        return $dataClass->create();
    }

    /**
     * @param DataClass $dataClass
     *
     * @return bool
     */
    public function update(DataClass $dataClass)
    {
        return $dataClass->update();
    }

    /**
     * @param DataClass $dataClass
     *
     * @return bool
     */
    public function delete(DataClass $dataClass)
    {
        return $dataClass->delete();
    }
}