<?php
namespace Chamilo\Application\Weblcms\Storage\Repository;

use Chamilo\Application\Weblcms\Course\Storage\DataClass\Course;
use Chamilo\Application\Weblcms\Storage\DataClass\CourseEntityRelation;
use Chamilo\Application\Weblcms\Course\Storage\DataClass\CourseRelCourseSetting;
use Chamilo\Application\Weblcms\Storage\DataClass\CourseSetting;
use Chamilo\Application\Weblcms\Storage\DataClass\CourseTool;
use Chamilo\Application\Weblcms\Storage\Repository\Interfaces\CourseRepositoryInterface;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Join;
use Chamilo\Libraries\Storage\DataClass\Property\DataClassProperties;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Variable\FixedPropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\Joins;
use Chamilo\Libraries\Storage\Query\Variable\PropertiesConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Parameters\RecordRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Storage\ResultSet\ResultSet;

/**
 * The repository class for the Course Entity
 * 
 * @package application\bamaflex
 * @author Tom Goethals - Hogeschool Gent
 */
class CourseRepository implements CourseRepositoryInterface
{

    /**
     * Returns a course by a given id
     * 
     * @param int $courseId
     *
     * @return Course
     */
    public function findCourse($courseId)
    {
        return \Chamilo\Application\Weblcms\Course\Storage\DataManager::retrieve_by_id(Course::class_name(), $courseId);
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
        return \Chamilo\Application\Weblcms\Course\Storage\DataManager::retrieve_course_by_visual_code($visualCode);
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
            new PropertyConditionVariable(Course::class_name(), Course::PROPERTY_ID), 
            $courseIds);
        
        $courses = \Chamilo\Application\Weblcms\Course\Storage\DataManager::retrieves(
            Course::class_name(), 
            new DataClassRetrievesParameters($condition));
        
        return $courses->as_array();
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
        $courses = \Chamilo\Application\Weblcms\Course\Storage\DataManager::retrieves(
            Course::class_name(), 
            $retrievesParameters);
        
        return $courses->as_array();
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
        return \Chamilo\Application\Weblcms\Course\Storage\DataManager::retrieve_all_courses_from_user($user)->as_array();
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
        return \Chamilo\Application\Weblcms\Course\Storage\DataManager::retrieve_courses_from_user_where_user_is_teacher(
            $user)->as_array();
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
        return \Chamilo\Application\Weblcms\Course\Storage\DataManager::retrieve_courses_from_user_where_user_is_student(
            $user)->as_array();
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
        $conditions = array();
        
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(CourseEntityRelation::class_name(), CourseEntityRelation::PROPERTY_ENTITY_ID), 
            new StaticConditionVariable($userId));
        
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(CourseEntityRelation::class_name(), CourseEntityRelation::PROPERTY_ENTITY_TYPE), 
            new StaticConditionVariable(CourseEntityRelation::ENTITY_TYPE_USER));
        
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(CourseEntityRelation::class_name(), CourseEntityRelation::PROPERTY_COURSE_ID), 
            new StaticConditionVariable($courseId));
        
        $condition = new AndCondition($conditions);
        
        return \Chamilo\Application\Weblcms\Course\Storage\DataManager::retrieve(
            CourseEntityRelation::class_name(), 
            new DataClassRetrieveParameters($condition));
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
        $conditions = array();
        
        $conditions[] = new InCondition(
            new PropertyConditionVariable(CourseEntityRelation::class_name(), CourseEntityRelation::PROPERTY_ENTITY_ID), 
            $groupIds);
        
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(CourseEntityRelation::class_name(), CourseEntityRelation::PROPERTY_ENTITY_TYPE), 
            new StaticConditionVariable(CourseEntityRelation::ENTITY_TYPE_GROUP));
        
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(CourseEntityRelation::class_name(), CourseEntityRelation::PROPERTY_COURSE_ID), 
            new StaticConditionVariable($courseId));
        
        $condition = new AndCondition($conditions);
        
        return \Chamilo\Application\Weblcms\Course\Storage\DataManager::retrieves(
            CourseEntityRelation::class_name(), 
            new DataClassRetrievesParameters($condition))->as_array();
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
            new PropertyConditionVariable(CourseTool::class_name(), CourseTool::PROPERTY_NAME), 
            new StaticConditionVariable($toolName));
        
        return \Chamilo\Application\Weblcms\Course\Storage\DataManager::retrieve(
            CourseTool::class_name(), 
            new DataClassRetrieveParameters($condition));
    }

    /**
     * Finds the tool registrations
     * 
     * @return CourseTool[]
     */
    public function findToolRegistrations()
    {
        return \Chamilo\Application\Weblcms\Course\Storage\DataManager::retrieves(CourseTool::class_name())->as_array();
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
        $properties->add(new PropertiesConditionVariable(Course::class_name()));
        $properties->add(new PropertyConditionVariable(CourseSetting::class_name(), CourseSetting::PROPERTY_NAME));
        $properties->add(new PropertyConditionVariable(CourseSetting::class_name(), CourseSetting::PROPERTY_TOOL_ID));
        $properties->add(
            new PropertyConditionVariable(CourseRelCourseSetting::class_name(), CourseRelCourseSetting::PROPERTY_VALUE));
        
        $properties->add(
            new FixedPropertyConditionVariable(User::class_name(), User::PROPERTY_FIRSTNAME, 'titular_firstname'));
        
        $properties->add(
            new FixedPropertyConditionVariable(User::class_name(), User::PROPERTY_LASTNAME, 'titular_lastname'));
        
        $joins = new Joins();
        
        $joins->add(
            new Join(
                CourseRelCourseSetting::class_name(), 
                new EqualityCondition(
                    new PropertyConditionVariable(Course::class_name(), Course::PROPERTY_ID), 
                    new PropertyConditionVariable(
                        CourseRelCourseSetting::class_name(), 
                        CourseRelCourseSetting::PROPERTY_COURSE_ID))));
        
        $joins->add(
            new Join(
                CourseSetting::class_name(), 
                new EqualityCondition(
                    new PropertyConditionVariable(
                        CourseRelCourseSetting::class_name(), 
                        CourseRelCourseSetting::PROPERTY_COURSE_SETTING_ID), 
                    new PropertyConditionVariable(CourseSetting::class_name(), CourseSetting::PROPERTY_ID))));
        
        $joins->add(
            new Join(
                User::class_name(), 
                new EqualityCondition(
                    new PropertyConditionVariable(Course::class_name(), Course::PROPERTY_TITULAR_ID), 
                    new PropertyConditionVariable(User::class_name(), User::PROPERTY_ID)), 
                Join::TYPE_LEFT));
        
        $recordRetrievesParameters = new RecordRetrievesParameters($properties, $condition, null, null, array(), $joins);
        
        $courseRecords = \Chamilo\Application\Weblcms\Course\Storage\DataManager::records(
            Course::class_name(), 
            $recordRetrievesParameters);
        
        $courses = array();
        
        while ($record = $courseRecords->next_result())
        {
            $id = $record['id'];
            
            if (! array_key_exists($id, $courses))
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
     * Returns all users subscribed to course by status
     * 
     * @param $courseId
     * @param $status
     * @return ResultSet
     */
    public function findUsersByStatus($courseId, $status = CourseEntityRelation::STATUS_STUDENT)
    {
        return \Chamilo\Application\Weblcms\Course\Storage\DataManager::retrieve_users_directly_subscribed_to_course_by_status(
            $courseId, 
            $status);
    }

    /**
     * Returns all groups directly subscribed to course by status
     * 
     * @param $courseId
     * @param $status
     * @return ResultSet
     */
    public function findDirectSubscribedGroupsByStatus($courseId, $status = CourseEntityRelation::STATUS_STUDENT)
    {
        return \Chamilo\Application\Weblcms\Course\Storage\DataManager::retrieve_groups_directly_subscribed_to_course_as_status(
            $courseId, 
            $status);
    }
}
