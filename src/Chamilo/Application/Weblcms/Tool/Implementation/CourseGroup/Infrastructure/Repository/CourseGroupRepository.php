<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Infrastructure\Repository;

use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroup;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroupUserRelation;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\Repository\CommonDataClassRepository;
use Chamilo\Libraries\Storage\DataClass\Property\DataClassProperties;
use Chamilo\Libraries\Storage\DataClass\Property\DataClassProperty;
use Chamilo\Libraries\Storage\DataManager\Repository\DataClassRepository;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassDistinctParameters;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 * Repository for the course groups
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class CourseGroupRepository extends CommonDataClassRepository
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
            new PropertyConditionVariable(CourseGroup::class_name(), CourseGroup::PROPERTY_COURSE_CODE),
            new StaticConditionVariable($courseId)
        );

        return $this->dataClassRepository->count(
            CourseGroup::class_name(),
            new DataClassCountParameters($condition)
        );
    }

    /**
     * @param CourseGroup $courseGroup
     *
     * @return int[]
     */
    public function getUserIdsDirectlySubscribedInGroup(CourseGroup $courseGroup)
    {
        $userProperty = new PropertyConditionVariable(
            CourseGroupUserRelation::class, CourseGroupUserRelation::PROPERTY_USER
        );

        return $this->dataClassRepository->distinct(
            CourseGroupUserRelation::class,
            new DataClassDistinctParameters(
                $this->getConditionForCourseGroupSubscriptionsByCourseGroup($courseGroup),
                new DataClassProperties([$userProperty])
            )
        );
    }

    /**
     * @param CourseGroup $courseGroup
     *
     * @return int
     */
    public function countMembersDirectlySubscribedInGroup(CourseGroup $courseGroup)
    {
        return $this->dataClassRepository->count(
            CourseGroupUserRelation::class,
            new DataClassCountParameters($this->getConditionForCourseGroupSubscriptionsByCourseGroup($courseGroup))
        );
    }

    /**
     * @param CourseGroupUserRelation $courseGroupUserRelation
     *
     * @return bool
     */
    public function createCourseGroupUserRelation(CourseGroupUserRelation $courseGroupUserRelation)
    {
        return $this->dataClassRepository->create($courseGroupUserRelation);
    }

    /**
     * @param CourseGroup $courseGroup
     */
    public function updateCourseGroup(CourseGroup $courseGroup)
    {
        return $this->dataClassRepository->update($courseGroup);
    }

    /**
     * @param CourseGroup $courseGroup
     *
     * @return Condition
     */
    protected function getConditionForCourseGroupSubscriptionsByCourseGroup($courseGroup)
    {
        return new EqualityCondition(
            new PropertyConditionVariable(
                CourseGroupUserRelation::class, CourseGroupUserRelation::PROPERTY_COURSE_GROUP
            ),
            new StaticConditionVariable($courseGroup->getId())
        );
    }
}
