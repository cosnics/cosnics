<?php

namespace Chamilo\Application\Weblcms\Storage\Repository;

use Chamilo\Application\Weblcms\Course\Storage\DataClass\Course;
use Chamilo\Application\Weblcms\Storage\DataClass\CourseEntityRelation;
use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Storage\DataClass\Property\DataClassProperties;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Parameters\RecordRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Join;
use Chamilo\Libraries\Storage\Query\Joins;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 * @package Chamilo\Application\Weblcms\Storage\Repository
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class CourseSubscriptionRepository
{
    /**
     * @var \Chamilo\Libraries\Storage\DataManager\Repository\DataClassRepository
     */
    protected $dataClassRepository;

    /**
     * CourseSubscriptionRepository constructor.
     *
     * @param \Chamilo\Libraries\Storage\DataManager\Repository\DataClassRepository $dataClassRepository
     */
    public function __construct(
        \Chamilo\Libraries\Storage\DataManager\Repository\DataClassRepository $dataClassRepository
    )
    {
        $this->dataClassRepository = $dataClassRepository;
    }

    /**
     * @param \Chamilo\Application\Weblcms\Storage\DataClass\CourseEntityRelation $courseEntityRelation
     *
     * @return bool
     */
    public function createCourseEntityRelation(CourseEntityRelation $courseEntityRelation)
    {
        return $this->dataClassRepository->create($courseEntityRelation);
    }

    /**
     * @param \Chamilo\Application\Weblcms\Storage\DataClass\CourseEntityRelation $courseEntityRelation
     *
     * @return bool
     */
    public function deleteCourseEntityRelation(CourseEntityRelation $courseEntityRelation)
    {
        return $this->dataClassRepository->delete($courseEntityRelation);
    }

    /**
     * @param \Chamilo\Core\Group\Storage\DataClass\Group $group
     * @param \Chamilo\Application\Weblcms\Course\Storage\DataClass\Course $course
     *
     * @return \Chamilo\Libraries\Storage\DataClass\CompositeDataClass|\Chamilo\Libraries\Storage\DataClass\DataClass
     */
    public function findCourseEntityRelationForCourseAndGroup(Group $group, Course $course)
    {
        return $this->findCourseEntityRelation($course, $group->getId(), CourseEntityRelation::ENTITY_TYPE_GROUP);
    }

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param \Chamilo\Application\Weblcms\Course\Storage\DataClass\Course $course
     *
     * @return \Chamilo\Libraries\Storage\DataClass\CompositeDataClass|\Chamilo\Libraries\Storage\DataClass\DataClass
     */
    public function findCourseEntityRelationForCourseAndUser(User $user, Course $course)
    {
        return $this->findCourseEntityRelation($course, $user->getId(), CourseEntityRelation::ENTITY_TYPE_USER);
    }

    /**
     * @param \Chamilo\Application\Weblcms\Course\Storage\DataClass\Course $course
     *
     * @return bool
     */
    public function removeEveryoneFromCourse(Course $course)
    {
        $condition = $this->getCourseEntityRelationCondition($course);

        return $this->dataClassRepository->deletes(CourseEntityRelation::class, $condition);
    }

    /**
     * @param \Chamilo\Application\Weblcms\Course\Storage\DataClass\Course $course
     * @param int $entityType
     * @param int|null $status
     *
     * @return \Chamilo\Libraries\Storage\Iterator\DataClassIterator
     */
    public function findSubscribedCourseEntities(Course $course, int $entityType, int $status = null)
    {
        $condition = $this->getCourseEntityRelationCondition($course, $entityType, null, $status);

        return $this->dataClassRepository->retrieves(
            CourseEntityRelation::class, new DataClassRetrievesParameters($condition)
        );
    }

    /**
     * @param \Chamilo\Application\Weblcms\Course\Storage\DataClass\Course $course
     * @param null $status
     *
     * @return \Chamilo\Libraries\Storage\Iterator\RecordIterator
     */
    public function findGroupsDirectlySubscribedToCourse(Course $course, $status = null)
    {
        $properties = new DataClassProperties();

        $properties->add(new PropertyConditionVariable(Group::class_name(), Group::PROPERTY_ID));
        $properties->add(new PropertyConditionVariable(Group::class_name(), Group::PROPERTY_NAME));
        $properties->add(new PropertyConditionVariable(Group::class_name(), Group::PROPERTY_DESCRIPTION));
        $properties->add(new PropertyConditionVariable(Group::class_name(), Group::PROPERTY_CODE));
        $properties->add(new PropertyConditionVariable(Group::class_name(), Group::PROPERTY_LEFT_VALUE));
        $properties->add(new PropertyConditionVariable(Group::class_name(), Group::PROPERTY_RIGHT_VALUE));

        $properties->add(
            new PropertyConditionVariable(CourseEntityRelation::class_name(), CourseEntityRelation::PROPERTY_STATUS)
        );

        $joins = new Joins();

        $joins->add(
            new Join(
                Group::class_name(),
                new EqualityCondition(
                    new PropertyConditionVariable(
                        CourseEntityRelation::class_name(), CourseEntityRelation::PROPERTY_ENTITY_ID
                    ),
                    new PropertyConditionVariable(Group::class_name(), Group::PROPERTY_ID)
                )
            )
        );

        $parameters = new RecordRetrievesParameters(
            $properties,
            $this->getCourseEntityRelationCondition($course, CourseEntityRelation::ENTITY_TYPE_GROUP, null, $status),
            null, null, [], $joins
        );

        return $this->dataClassRepository->records(CourseEntityRelation::class_name(), $parameters);
    }

    /**
     * @param Course $course
     * @param int $status
     *
     * @return \Chamilo\Libraries\Storage\Iterator\DataClassIterator
     */
    public function findUsersDirectlySubscribedToCourse(Course $course, int $status)
    {
        $properties = new DataClassProperties();

        $properties->add(new PropertyConditionVariable(User::class_name(), User::PROPERTY_ID));
        $properties->add(new PropertyConditionVariable(User::class_name(), User::PROPERTY_OFFICIAL_CODE));
        $properties->add(new PropertyConditionVariable(User::class_name(), User::PROPERTY_LASTNAME));
        $properties->add(new PropertyConditionVariable(User::class_name(), User::PROPERTY_FIRSTNAME));
        $properties->add(new PropertyConditionVariable(User::class_name(), User::PROPERTY_USERNAME));
        $properties->add(new PropertyConditionVariable(User::class_name(), User::PROPERTY_EMAIL));

        $properties->add(
            new PropertyConditionVariable(CourseEntityRelation::class_name(), CourseEntityRelation::PROPERTY_STATUS)
        );

        $joins = new Joins();

        $joins->add(
            new Join(
                CourseEntityRelation::class_name(),
                new EqualityCondition(
                    new PropertyConditionVariable(
                        CourseEntityRelation::class_name(), CourseEntityRelation::PROPERTY_ENTITY_ID
                    ),
                    new PropertyConditionVariable(User::class_name(), User::PROPERTY_ID)
                )
            )
        );

        $parameters = new RecordRetrievesParameters(
            $properties,
            $this->getCourseEntityRelationCondition($course, CourseEntityRelation::ENTITY_TYPE_USER, null, $status),
            null, null, [], $joins
        );

        return $this->dataClassRepository->records(User::class_name(), $parameters);
    }

    /**
     * @param \Chamilo\Application\Weblcms\Course\Storage\DataClass\Course $course
     * @param int $entityId
     * @param int $entityType
     *
     * @return \Chamilo\Libraries\Storage\DataClass\CompositeDataClass|\Chamilo\Libraries\Storage\DataClass\DataClass
     */
    protected function findCourseEntityRelation(Course $course, int $entityId, int $entityType)
    {
        $parameters = new DataClassRetrieveParameters(
            $this->getCourseEntityRelationCondition($course, $entityType, $entityId)
        );

        return $this->dataClassRepository->retrieve(CourseEntityRelation::class, $parameters);
    }

    /**
     * @param \Chamilo\Application\Weblcms\Course\Storage\DataClass\Course $course
     * @param int|null $entityType
     * @param int|null $entityId
     * @param int|null $status
     *
     * @return \Chamilo\Libraries\Storage\Query\Condition\AndCondition
     */
    protected function getCourseEntityRelationCondition(
        Course $course, int $entityType = null, int $entityId = null, int $status = null
    )
    {
        $conditions = array();

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(CourseEntityRelation::class_name(), CourseEntityRelation::PROPERTY_COURSE_ID),
            new StaticConditionVariable($course->getId())
        );

        if (!is_null($entityId))
        {
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(
                    CourseEntityRelation::class_name(), CourseEntityRelation::PROPERTY_ENTITY_ID
                ),
                new StaticConditionVariable($entityId)
            );
        }

        if (!is_null($entityType))
        {
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(
                    CourseEntityRelation::class_name(), CourseEntityRelation::PROPERTY_ENTITY_TYPE
                ),
                new StaticConditionVariable($entityType)
            );
        }

        if (!is_null($status))
        {
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(
                    CourseEntityRelation::class_name(), CourseEntityRelation::PROPERTY_STATUS
                ),
                new StaticConditionVariable($status)
            );
        }

        return new AndCondition($conditions);
    }

}
