<?php

namespace Chamilo\Application\Weblcms\Storage\Repository;

use Chamilo\Application\Weblcms\Course\Storage\DataClass\Course;
use Chamilo\Application\Weblcms\Course\Storage\DataManager;
use Chamilo\Application\Weblcms\CourseType\Storage\DataClass\CourseType;
use Chamilo\Application\Weblcms\Storage\DataClass\CourseEntityRelation;
use Chamilo\Application\Weblcms\Course\Storage\DataClass\CourseRelCourseSetting;
use Chamilo\Application\Weblcms\Storage\DataClass\CourseSetting;
use Chamilo\Application\Weblcms\Storage\DataClass\CourseTool;
use Chamilo\Application\Weblcms\Storage\Repository\Interfaces\CourseRepositoryInterface;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Join;
use Chamilo\Libraries\Storage\DataClass\Property\DataClassProperties;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\Variable\FixedPropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\Joins;
use Chamilo\Libraries\Storage\Query\Variable\PropertiesConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Parameters\RecordRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Core\User\Storage\DataClass\User;

/**
 * The repository class for the Course Entity
 *
 * @package application\bamaflex
 * @author Tom Goethals - Hogeschool Gent
 */
class CourseRepository implements CourseRepositoryInterface
{

    /**
     * @param \Chamilo\Application\Weblcms\Course\Storage\DataClass\Course $course
     * @param int $entityType
     * @param array $entityIdentifiers
     *
     * @return int
     */
    public function countCourseEntityRelationsByCourseAndEntityTypeAndIdentifiers(
        Course $course, int $entityType, array $entityIdentifiers = []
    )
    {
        $conditions = [];

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(CourseEntityRelation::class, CourseEntityRelation::PROPERTY_COURSE_ID),
            new StaticConditionVariable($course->getId())
        );

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(CourseEntityRelation::class, CourseEntityRelation::PROPERTY_ENTITY_TYPE),
            $entityType
        );

        $conditions[] = new InCondition(
            new PropertyConditionVariable(CourseEntityRelation::class, CourseEntityRelation::PROPERTY_ENTITY_ID),
            $entityIdentifiers
        );

        $condition = new AndCondition($conditions);

        return DataManager::count(CourseEntityRelation::class, new DataClassCountParameters($condition));
    }

    /**
     * @param \Chamilo\Application\Weblcms\Course\Storage\DataClass\Course $course
     *
     * @return \Chamilo\Libraries\Storage\Iterator\DataClassIterator
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     * @throws \Doctrine\DBAL\Exception
     */
    public function findAllUsersFromCourse(Course $course)
    {
        return DataManager::retrieve_all_course_users($course->getId());
    }

    /**
     * Returns a course by a given id
     *
     * @param int $courseId
     *
     * @return Course
     */
    public function findCourse($courseId)
    {
        return DataManager::retrieve_by_id(Course::class, $courseId);
    }

    /**
     * Returns a course by a given visual code
     *
     * @param string $visualCode
     *
     * @return Course
     */
    public function findCourseByVisualCode($visualCode)
    {
        return DataManager::retrieve_course_by_visual_code($visualCode);
    }

    /**
     * Returns the course group subscriptions by a given course and groups
     *
     * @param int $courseId
     * @param int[] $groupIds
     *
     * @return CourseEntityRelation[]
     */
    public function findCourseGroupSubscriptionsByCourseAndGroups($courseId, $groupIds)
    {
        $conditions = [];

        $conditions[] = new InCondition(
            new PropertyConditionVariable(CourseEntityRelation::class, CourseEntityRelation::PROPERTY_ENTITY_ID),
            $groupIds
        );

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                CourseEntityRelation::class, CourseEntityRelation::PROPERTY_ENTITY_TYPE
            ), new StaticConditionVariable(CourseEntityRelation::ENTITY_TYPE_GROUP)
        );

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(CourseEntityRelation::class, CourseEntityRelation::PROPERTY_COURSE_ID),
            new StaticConditionVariable($courseId)
        );

        $condition = new AndCondition($conditions);

        return DataManager::retrieves(
            CourseEntityRelation::class, new DataClassRetrievesParameters($condition)
        );
    }

    /**
     * Finds a course tool registration by a given tool name
     *
     * @param string $toolName
     *
     * @return CourseTool
     */
    public function findCourseToolByName($toolName)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(CourseTool::class, CourseTool::PROPERTY_NAME),
            new StaticConditionVariable($toolName)
        );

        return DataManager::retrieve(
            CourseTool::class, new DataClassRetrieveParameters($condition)
        );
    }

    /**
     * Returns the course user subscriptions by a given course and user
     *
     * @param int $courseId
     * @param int $userId
     *
     * @return \Chamilo\Application\Weblcms\Storage\DataClass\CourseEntityRelation
     */
    public function findCourseUserSubscriptionByCourseAndUser($courseId, $userId)
    {
        $conditions = [];

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(CourseEntityRelation::class, CourseEntityRelation::PROPERTY_ENTITY_ID),
            new StaticConditionVariable($userId)
        );

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                CourseEntityRelation::class, CourseEntityRelation::PROPERTY_ENTITY_TYPE
            ), new StaticConditionVariable(CourseEntityRelation::ENTITY_TYPE_USER)
        );

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(CourseEntityRelation::class, CourseEntityRelation::PROPERTY_COURSE_ID),
            new StaticConditionVariable($courseId)
        );

        $condition = new AndCondition($conditions);

        return DataManager::retrieve(
            CourseEntityRelation::class, new DataClassRetrieveParameters($condition)
        );
    }

    /**
     * Returns courses with an array of course id's.
     *
     * @param array $courseIds
     *
     * @return Course[]
     */
    function findCourses(array $courseIds)
    {
        $condition = new InCondition(
            new PropertyConditionVariable(Course::class, Course::PROPERTY_ID), $courseIds
        );

        return DataManager::retrieves(
            Course::class, new DataClassRetrievesParameters($condition)
        );
    }

    /**
     * @param \Chamilo\Application\Weblcms\CourseType\Storage\DataClass\CourseType $courseType
     * @param int[] $subscribedCourseIds
     *
     * @return mixed[]|Course[]
     */
    public function findCoursesByCourseTypeAndSubscribedCourseIds(
        CourseType $courseType, array $subscribedCourseIds = []
    )
    {
        $conditions = [];

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                Course::class, Course::PROPERTY_COURSE_TYPE_ID
            ), new StaticConditionVariable($courseType->getId())
        );

        $conditions[] = new InCondition(
            new PropertyConditionVariable(
                Course::class, Course::PROPERTY_ID
            ), $subscribedCourseIds
        );

        $condition = new AndCondition($conditions);

        $orderBy = array(new OrderBy(new PropertyConditionVariable(Course::class, Course::PROPERTY_TITLE)));

        return DataManager::retrieves(
            Course::class, new DataClassRetrievesParameters($condition, null, null, $orderBy)
        );
    }

    /**
     * @param int $courseTypeId
     *
     * @return Course[]
     */
    public function findCoursesByCourseTypeId(int $courseTypeId): array
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(
                Course::class, Course::PROPERTY_COURSE_TYPE_ID
            ), new StaticConditionVariable($courseTypeId)
        );

        $orderBy = array(new OrderBy(new PropertyConditionVariable(Course::class, Course::PROPERTY_TITLE)));

        return DataManager::retrieves(
            Course::class, new DataClassRetrievesParameters($condition, null, null, $orderBy)
        );
    }

    /**
     * Returns Courses with an array of course ids and a given set of parameters
     *
     * @param DataClassRetrievesParameters $retrievesParameters
     *
     * @return Course[]
     */
    public function findCoursesByParameters(DataClassRetrievesParameters $retrievesParameters)
    {
        return DataManager::retrieves(
            Course::class, $retrievesParameters
        );
    }

    /**
     * Returns courses where a user is subscribed
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @return Course[]
     */
    public function findCoursesForUser(User $user)
    {
        $orderBy = array(new OrderBy(new PropertyConditionVariable(Course::class, Course::PROPERTY_TITLE)));

        return DataManager::retrieve_all_courses_from_user(
            $user, null, null, null, $orderBy
        );
    }

    /**
     * @param array $groupIdentifiers
     *
     * @return \Chamilo\Application\Weblcms\Course\Storage\DataClass\Course[]
     */
    public function findCoursesWhereAtLeastOneGroupIsDirectlySubscribed(array $groupIdentifiers = [])
    {
        $conditions = [];

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(CourseEntityRelation::class, CourseEntityRelation::PROPERTY_ENTITY_TYPE),
            new StaticConditionVariable(CourseEntityRelation::ENTITY_TYPE_GROUP)
        );

        $conditions[] = new InCondition(
            new PropertyConditionVariable(CourseEntityRelation::class, CourseEntityRelation::PROPERTY_ENTITY_ID),
            $groupIdentifiers
        );

        $condition = new AndCondition($conditions);

        $joins = new Joins();
        $joins->add(
            new Join(
                CourseEntityRelation::class, new EqualityCondition(
                    new PropertyConditionVariable(Course::class, Course::PROPERTY_ID),
                    new PropertyConditionVariable(CourseEntityRelation::class, CourseEntityRelation::PROPERTY_COURSE_ID)
                )
            )
        );

        return DataManager::retrieves(
            Course::class, new DataClassRetrievesParameters($condition, null, null, [], $joins)
        );
    }

    /**
     * Returns courses where a user is subscribed as a student
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @return Course[]
     */
    public function findCoursesWhereUserIsStudent(User $user)
    {
        return DataManager::retrieve_courses_from_user_where_user_is_student(
            $user
        );
    }

    /**
     * Returns courses where a user is subscribed as a teacher
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @return Course[]
     */
    public function findCoursesWhereUserIsTeacher(User $user)
    {
        return DataManager::retrieve_courses_from_user_where_user_is_teacher(
            $user
        );
    }

    /**
     * Finds courses with his settings with given retrieve parameters (record, no dataclass)
     *
     * @param Condition $condition
     *
     * @return array[]
     */
    public function findCoursesWithTitularAndCourseSettings(Condition $condition = null)
    {
        $properties = new DataClassProperties();
        $properties->add(new PropertiesConditionVariable(Course::class));
        $properties->add(new PropertyConditionVariable(CourseSetting::class, CourseSetting::PROPERTY_NAME));
        $properties->add(new PropertyConditionVariable(CourseSetting::class, CourseSetting::PROPERTY_TOOL_ID));
        $properties->add(
            new PropertyConditionVariable(CourseRelCourseSetting::class, CourseRelCourseSetting::PROPERTY_VALUE)
        );

        $properties->add(
            new FixedPropertyConditionVariable(User::class, User::PROPERTY_FIRSTNAME, 'titular_firstname')
        );

        $properties->add(
            new FixedPropertyConditionVariable(User::class, User::PROPERTY_LASTNAME, 'titular_lastname')
        );

        $joins = new Joins();

        $joins->add(
            new Join(
                CourseRelCourseSetting::class, new EqualityCondition(
                    new PropertyConditionVariable(Course::class, Course::PROPERTY_ID), new PropertyConditionVariable(
                        CourseRelCourseSetting::class, CourseRelCourseSetting::PROPERTY_COURSE_ID
                    )
                )
            )
        );

        $joins->add(
            new Join(
                CourseSetting::class, new EqualityCondition(
                    new PropertyConditionVariable(
                        CourseRelCourseSetting::class, CourseRelCourseSetting::PROPERTY_COURSE_SETTING_ID
                    ), new PropertyConditionVariable(CourseSetting::class, CourseSetting::PROPERTY_ID)
                )
            )
        );

        $joins->add(
            new Join(
                User::class, new EqualityCondition(
                new PropertyConditionVariable(Course::class, Course::PROPERTY_TITULAR_ID),
                new PropertyConditionVariable(User::class, User::PROPERTY_ID)
            ), Join::TYPE_LEFT
            )
        );

        $recordRetrievesParameters =
            new RecordRetrievesParameters($properties, $condition, null, null, [], $joins);

        $courseRecords = DataManager::records(
            Course::class, $recordRetrievesParameters
        );

        $courses = [];

        foreach ($courseRecords as $record)
        {
            $id = $record['id'];

            if (!array_key_exists($id, $courses))
            {
                $courses[$id] = $record;

                unset($courses[$id]['name']);
                unset($courses[$id]['tool_id']);
                unset($courses[$id]['value']);
            }

            $courses[$id]['course_settings'][$record['name']] = $record['value'];
        }

        return $courses;
    }

    /**
     * Returns all groups directly subscribed to course by status
     *
     * @param $courseId
     * @param $status
     *
     * @return \Chamilo\Libraries\Storage\Iterator\DataClassIterator
     */
    public function findDirectSubscribedGroupsByStatus($courseId, $status = CourseEntityRelation::STATUS_STUDENT)
    {
        return DataManager::retrieve_groups_directly_subscribed_to_course_as_status(
            $courseId, $status
        );
    }

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @return int[]
     */
    public function findSubscribedCourseIdsForUser(User $user)
    {
        return DataManager::getSubscribedCourseIdentifiersByRelation($user);
    }

    /**
     * Finds the tool registrations
     *
     * @return CourseTool[]
     */
    public function findToolRegistrations()
    {
        return DataManager::retrieves(CourseTool::class);
    }

    /**
     * Returns all users subscribed to course by status
     *
     * @param $courseId
     * @param $status
     *
     * @return \Chamilo\Libraries\Storage\Iterator\DataClassIterator
     */
    public function findUsersByStatus($courseId, $status = CourseEntityRelation::STATUS_STUDENT)
    {
        return DataManager::retrieve_users_directly_subscribed_to_course_by_status(
            $courseId, $status
        );
    }
}
