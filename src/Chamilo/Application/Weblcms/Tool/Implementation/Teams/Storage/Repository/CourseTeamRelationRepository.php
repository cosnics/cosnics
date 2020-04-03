<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\Teams\Storage\Repository;

use Chamilo\Application\Weblcms\Course\Storage\DataClass\Course;
use Chamilo\Application\Weblcms\Tool\Implementation\Teams\Storage\DataClass\CourseTeamRelation;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\Repository\CommonDataClassRepository;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
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
     *
     * @return CourseTeamRelation
     */
    public function findByCourse(Course $course): ?CourseTeamRelation
    {
        $condition = $this->getConditionByCourse($course);

        $courseTeamRelation = $this->dataClassRepository->retrieve(
            CourseTeamRelation::class, new DataClassRetrieveParameters($condition)
        );

        if (!$courseTeamRelation instanceof CourseTeamRelation)
        {
            return null;
        }

        return $courseTeamRelation;
    }

    /**
     * @param CourseTeamRelation $courseTeamRelation
     *
     * @return bool
     */
    public function updateCourseTeamRelation(CourseTeamRelation $courseTeamRelation)
    {
        return $this->dataClassRepository->update($courseTeamRelation);
    }

    /**
     * @param Course $course
     *
     * @return AndCondition
     */
    protected function getConditionByCourse(Course $course): AndCondition
    {
        $conditions = [];

        $conditions[] =  new EqualityCondition(
            new PropertyConditionVariable(
                CourseTeamRelation::class, CourseTeamRelation::PROPERTY_COURSE_ID
            ),
            new StaticConditionVariable((string) $course->getId())
        );

        $conditions[] =  new EqualityCondition(
            new PropertyConditionVariable(
                CourseTeamRelation::class, CourseTeamRelation::PROPERTY_ACTIVE
            ),
            new StaticConditionVariable(1)
        );

        return new AndCondition($conditions);
    }
}
