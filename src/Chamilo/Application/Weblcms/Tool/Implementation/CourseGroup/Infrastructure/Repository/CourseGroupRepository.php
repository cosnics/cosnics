<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Infrastructure\Repository;

use Chamilo\Application\Weblcms\Course\Storage\DataClass\Course;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroup;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroupUserRelation;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\Repository\CommonDataClassRepository;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\Join;
use Chamilo\Libraries\Storage\Query\Joins;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 * Repository for the course groups
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class CourseGroupRepository extends CommonDataClassRepository implements CourseGroupRepositoryInterface
{

    /**
     * Counts the course groups in a given course
     *
     * @param int $courseId
     *
     * @return int
     */
    public function countCourseGroupsInCourse($courseId)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(CourseGroup::class, CourseGroup::PROPERTY_COURSE_CODE),
            new StaticConditionVariable($courseId)
        );

        return $this->dataClassRepository->count(
            CourseGroup::class,
            new DataClassCountParameters($condition)
        );
    }

    /**
     * @param \Chamilo\Application\Weblcms\Course\Storage\DataClass\Course $course
     * @param array $userIds
     *
     * @return \Chamilo\Libraries\Storage\Iterator\DataClassIterator|CourseGroup[]
     */
    public function findCourseGroupUserRelationsForCourseAndUserIds(Course $course, array $userIds = array())
    {
        $conditions = [];

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(CourseGroup::class, CourseGroup::PROPERTY_COURSE_CODE),
            new StaticConditionVariable($course->getId())
        );

        $conditions[] = new InCondition(
            new PropertyConditionVariable(
                CourseGroupUserRelation::class, CourseGroupUserRelation::PROPERTY_USER
            ),
            $userIds
        );

        $condition = new AndCondition($conditions);

        $joins = new Joins();

        $joins->add(
            new Join(
                CourseGroup::class,
                new EqualityCondition(
                    new PropertyConditionVariable(CourseGroup::class, CourseGroup::PROPERTY_ID),
                    new PropertyConditionVariable(
                        CourseGroupUserRelation::class, CourseGroupUserRelation::PROPERTY_COURSE_GROUP
                    )
                )
            )
        );

        return $this->dataClassRepository->retrieves(
            CourseGroupUserRelation::class, new DataClassRetrievesParameters($condition, null, null, array(), $joins)
        );
    }

    /**
     * @param \Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroup $courseGroup
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @return CourseGroup|\Chamilo\Libraries\Storage\DataClass\DataClass
     */
    public function findCourseGroupUserRelationByCourseGroupAndUser(CourseGroup $courseGroup, User $user)
    {
        $conditions = [];

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                CourseGroupUserRelation::class, CourseGroupUserRelation::PROPERTY_COURSE_GROUP
            ),
            new StaticConditionVariable($courseGroup->getId())
        );

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                CourseGroupUserRelation::class, CourseGroupUserRelation::PROPERTY_USER
            ),
            new StaticConditionVariable($user->getId())
        );

        $condition = new AndCondition($conditions);

        return $this->dataClassRepository->retrieve(
            CourseGroupUserRelation::class, new DataClassRetrieveParameters($condition)
        );
    }

}