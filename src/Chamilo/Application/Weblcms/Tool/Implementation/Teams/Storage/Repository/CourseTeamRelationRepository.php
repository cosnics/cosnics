<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Extension\Office365\Integration\Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\Repository;

use Chamilo\Application\Weblcms\Course\Storage\DataClass\Course;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Extension\Office365\Integration\Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseTeamRelation;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\Repository\CommonDataClassRepository;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 * Class CourseTeamRelationRepository
 */
class CourseTeamRelationRepository extends CommonDataClassRepository
{

    /**
     * @param Course $course
     * @return \Chamilo\Libraries\Storage\DataClass\DataClass | CourseTeamRelation
     */
    public function findByCourse(Course $course):\Chamilo\Libraries\Storage\DataClass\DataClass
    {
        $condition = $this->getConditionByCourse($course);

        return $this->dataClassRepository->retrieve(
            CourseTeamRelation::class, new DataClassRetrieveParameters($condition)
        );
    }

    /**
     * @param Course $course
     * @return EqualityCondition
     */
    protected function getConditionByCourse(Course $course): EqualityCondition
    {
        return new EqualityCondition(
            new PropertyConditionVariable(
                Course::class, Course::PROPERTY_ID
            ),
            new StaticConditionVariable($course->getId())
        );
    }
}