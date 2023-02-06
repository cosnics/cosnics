<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Infrastructure\Repository;

use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroup;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroupUserRelation;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\Repository\CommonDataClassRepository;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Storage\DataClass\Property\DataClassProperties;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassDistinctParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
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

    public function getCourseGroupsInCourse(int $courseId)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(CourseGroup::class_name(), CourseGroup::PROPERTY_COURSE_CODE),
            new StaticConditionVariable($courseId)
        );

        return $this->dataClassRepository->retrieves(
            CourseGroup::class_name(),
            new DataClassRetrievesParameters($condition)
        );
    }

    /**
     * @param int $courseId
     * @return \Chamilo\Libraries\Storage\Iterator\DataClassIterator
     */
    public function getCourseGroupsInCourse(int $courseId)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(CourseGroup::class_name(), CourseGroup::PROPERTY_COURSE_CODE),
            new StaticConditionVariable($courseId)
        );

        return $this->dataClassRepository->retrieves(
            CourseGroup::class_name(),
            new DataClassRetrievesParameters($condition)
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

    /**
     * @param int $courseGroupId
     *
     * @return bool|\Chamilo\Libraries\Storage\DataClass\CompositeDataClass|\Chamilo\Libraries\Storage\DataClass\DataClass
     */
    public function getCourseGroupById(int $courseGroupId)
    {
        return $this->dataClassRepository->retrieveById(CourseGroup::class, $courseGroupId);
    }

    /**
     * @param User $user
     * @param CourseGroup $courseGroup
     *
     * @return CourseGroupUserRelation|\Chamilo\Libraries\Storage\DataClass\CompositeDataClass|\Chamilo\Libraries\Storage\DataClass\DataClass|false
     */
    public function retrieveUserSubscriptionInCourseGroup(User $user, CourseGroup $courseGroup)
    {
        $condition = $this->getCourseGroupUserCondition($user, $courseGroup);

        return $this->dataClassRepository->retrieve(
            CourseGroupUserRelation::class, new DataClassRetrieveParameters($condition)
        );
    }

    /**
     * @param CourseGroup $courseGroup
     * @param User $user
     *
     * @return \Chamilo\Libraries\Storage\DataClass\CompositeDataClass|\Chamilo\Libraries\Storage\DataClass\DataClass|false
     */
    public function removeUserFromCourseGroup(CourseGroup $courseGroup, User $user)
    {
        $condition = $this->getCourseGroupUserCondition($user, $courseGroup);

        return $this->dataClassRepository->deletes(CourseGroupUserRelation::class, $condition);
    }

    /**
     * @param User $user
     * @param CourseGroup $courseGroup
     *
     * @return AndCondition
     */
    protected function getCourseGroupUserCondition(User $user, CourseGroup $courseGroup): AndCondition
    {
        $conditions = [];

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(CourseGroupUserRelation::class, CourseGroupUserRelation::PROPERTY_USER),
            new StaticConditionVariable($user->getId())
        );

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                CourseGroupUserRelation::class, CourseGroupUserRelation::PROPERTY_COURSE_GROUP
            ),
            new StaticConditionVariable($courseGroup->getId())
        );

        $condition = new AndCondition($conditions);

        return $condition;
    }

    public function createCourseGroup(CourseGroup $courseGroup)
    {
        return $courseGroup->create();
    }

    /**
     * @param int $courseId
     *
     * @return \Chamilo\Libraries\Storage\DataClass\CompositeDataClass|\Chamilo\Libraries\Storage\DataClass\DataClass|false|CourseGroup
     */
    public function getRootCourseGroup(int $courseId)
    {
        $conditions = array();

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(CourseGroup::class_name(), CourseGroup::PROPERTY_COURSE_CODE),
            new StaticConditionVariable($courseId)
        );

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(CourseGroup::class_name(), CourseGroup::PROPERTY_PARENT_ID),
            new StaticConditionVariable(0)
        );

        $condition = new AndCondition($conditions);

        return $this->dataClassRepository->retrieve(
            CourseGroup::class_name(), new DataClassRetrieveParameters($condition)
        );
    }

}
