<?php

namespace Chamilo\Core\Repository\ContentObject\LearningPath\Storage\Repository;

use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\LearningPath;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\LearningPathContentObjectRelation;
use Chamilo\Libraries\Storage\DataManager\Repository\DataClassRepository;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 * Repository class to manage the data for the LearningPathContentObjectRelation
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class LearningPathContentObjectRelationRepository
{
    /**
     * @var DataClassRepository
     */
    protected $dataClassRepository;

    /**
     * LearningPathContentObjectRelationRepository constructor.
     *
     * @param DataClassRepository $dataClassRepository
     */
    public function __construct(DataClassRepository $dataClassRepository)
    {
        $this->dataClassRepository = $dataClassRepository;
    }

    /**
     * Retrieves the LearningPathContentObjectRelations for a given learning path
     *
     * @param LearningPath $learningPath
     *
     * @return \Chamilo\Libraries\Storage\Iterator\DataClassIterator
     */
    public function retrieveLearningPathContentObjectRelationsForLearningPath(LearningPath $learningPath)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(
                LearningPathContentObjectRelation::class_name(),
                LearningPathContentObjectRelation::PROPERTY_LEARNING_PATH_ID
            ),
            new StaticConditionVariable($learningPath->getId())
        );

        return $this->dataClassRepository->retrieves(
            LearningPathContentObjectRelation::class_name(),
            new DataClassRetrievesParameters($condition)
        );
    }
}