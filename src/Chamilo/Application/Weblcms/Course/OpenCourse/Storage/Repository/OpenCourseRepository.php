<?php
namespace Chamilo\Application\Weblcms\Course\OpenCourse\Storage\Repository;

use Chamilo\Application\Weblcms\Course\OpenCourse\Storage\Repository\Interfaces\OpenCourseRepositoryInterface;
use Chamilo\Application\Weblcms\Course\Storage\DataClass\Course;
use Chamilo\Application\Weblcms\CourseType\Storage\DataClass\CourseType;
use Chamilo\Application\Weblcms\Storage\DataClass\CourseEntityRelation;
use Chamilo\Core\User\Roles\Storage\DataClass\Role;
use Chamilo\Libraries\Storage\DataClass\Property\DataClassProperties;
use Chamilo\Libraries\Storage\DataManager\Repository\DataManagerRepository;
use Chamilo\Libraries\Storage\Iterator\RecordIterator;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassDistinctParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Parameters\RecordRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\Condition\NotCondition;
use Chamilo\Libraries\Storage\Query\Join;
use Chamilo\Libraries\Storage\Query\Joins;
use Chamilo\Libraries\Storage\Query\Variable\FixedPropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\PropertiesConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 * Repository to manage the data open courses
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class OpenCourseRepository extends DataManagerRepository implements OpenCourseRepositoryInterface
{

    /**
     * Retrieves the open courses for the given user roles
     *
     * @param Role[]   $roles
     * @param Condition $condition
     * @param int $offset
     * @param int $count
     * @param OrderBy[] $orderBy
     *
     * @return RecordIterator
     */
    public function findOpenCoursesByRoles($roles = array(), Condition $condition = null, $offset = null, $count = null, $orderBy = array())
    {
        return $this->findOpenCourses(
            $this->getOpenCoursesCondition($condition, $this->getConditionForRoles($roles)),
            $offset,
            $count,
            $orderBy);
    }

    /**
     * Retrieves the open courses
     *
     * @param Condition $condition
     * @param int $offset
     * @param int $count
     * @param OrderBy[] $orderBy
     *
     * @return RecordIterator
     */
    public function findAllOpenCourses(Condition $condition = null, $offset = null, $count = null, $orderBy = array())
    {
        return $this->findOpenCourses($this->getOpenCoursesCondition($condition), $offset, $count, $orderBy);
    }

    /**
     * Helper function to find open courses
     *
     * @param Condition $condition
     * @param null $offset
     * @param null $count
     * @param array $orderBy
     *
     * @return RecordIterator
     */
    protected function findOpenCourses(Condition $condition = null, $offset = null, $count = null, $orderBy = array())
    {
        $properties = new DataClassProperties();
        $properties->add(new PropertiesConditionVariable(Course::class_name()));

        $properties->add(
            new FixedPropertyConditionVariable(
                CourseType::class_name(),
                CourseType::PROPERTY_TITLE,
                Course::PROPERTY_COURSE_TYPE_TITLE));

        $recordsParameters = new RecordRetrievesParameters(
            $properties,
            $condition,
            $count,
            $offset,
            $orderBy,
            $this->getOpenCoursesJoins());

        return \Chamilo\Application\Weblcms\Course\Storage\DataManager::records(
            Course::class_name(),
            $recordsParameters);
    }

    /**
     * Retrieves the courses that are not open
     *
     * @param Condition $condition
     * @param int $offset
     * @param int $count
     * @param OrderBy[] $orderBy
     *
     * @return RecordIterator
     */
    public function findClosedCourses(Condition $condition = null, $offset = null, $count = null, $orderBy = array())
    {
        return \Chamilo\Application\Weblcms\Course\Storage\DataManager::retrieves(
            Course::class_name(),
            new DataClassRetrievesParameters($this->getClosedCoursesCondition($condition), $count, $offset, $orderBy));
    }

    /**
     * Counts the open courses by the given user roles
     *
     * @param Role[]   $roles
     * @param Condition $condition
     *
     * @return int
     */
    public function countOpenCoursesByRoles($roles = array(), Condition $condition = null)
    {
        return $this->countOpenCourses($this->getOpenCoursesCondition($condition, $this->getConditionForRoles($roles)));
    }

    /**
     * Counts all the open courses
     *
     * @param Condition $condition
     *
     * @return int
     */
    public function countAllOpenCourses(Condition $condition = null)
    {
        return $this->countOpenCourses($this->getOpenCoursesCondition($condition));
    }

    /**
     * Helper function to count open courses
     *
     * @param Condition $condition
     *
     * @return int
     */
    protected function countOpenCourses(Condition $condition = null)
    {
        $countParameters = new DataClassCountParameters($condition, $this->getOpenCoursesJoins());

        return \Chamilo\Application\Weblcms\Course\Storage\DataManager::count(Course::class_name(), $countParameters);
    }

    /**
     * Counts the courses that are not open
     *
     * @param Condition $condition
     *
     * @return int
     */
    public function countClosedCourses(Condition $condition = null)
    {
        return \Chamilo\Application\Weblcms\Course\Storage\DataManager::count(
            Course::class_name(),
            new DataClassCountParameters($this->getClosedCoursesCondition($condition)));
    }

    /**
     * Returns the roles for a given open course
     *
     * @param Course $course
     *
     * @return \Chamilo\Libraries\Storage\ResultSet\ResultSet
     */
    public function getRolesForOpenCourse(Course $course)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(CourseEntityRelation::class_name(), CourseEntityRelation::PROPERTY_COURSE_ID),
            new StaticConditionVariable($course->getId()));

        $joins = new Joins();

        $joins->add(
            new Join(
                CourseEntityRelation::class_name(),
                new AndCondition(
                    array(
                        new EqualityCondition(
                            new PropertyConditionVariable(
                                CourseEntityRelation::class_name(),
                                CourseEntityRelation::PROPERTY_ENTITY_ID),
                            new PropertyConditionVariable(Role::class_name(), Role::PROPERTY_ID)),
                        new EqualityCondition(
                            new PropertyConditionVariable(
                                CourseEntityRelation::class_name(),
                                CourseEntityRelation::PROPERTY_ENTITY_TYPE),
                            new StaticConditionVariable(CourseEntityRelation::ENTITY_TYPE_ROLE))))));

        return \Chamilo\Core\User\Roles\Storage\DataManager::retrieves(
            Role::class_name(),
            new DataClassRetrievesParameters($condition, null, null, array(), $joins));
    }

    /**
     * Removes a course as an open course
     *
     * @param int[] $courseIds
     *
     * @return bool
     */
    public function removeCoursesAsOpenCourse($courseIds)
    {
        $condition = new AndCondition(
            array(
                new InCondition(
                    new PropertyConditionVariable(
                        CourseEntityRelation::class_name(),
                        CourseEntityRelation::PROPERTY_COURSE_ID),
                    $courseIds),
                new EqualityCondition(
                    new PropertyConditionVariable(
                        CourseEntityRelation::class_name(),
                        CourseEntityRelation::PROPERTY_ENTITY_TYPE),
                    new StaticConditionVariable(CourseEntityRelation::ENTITY_TYPE_ROLE))));

        return \Chamilo\Application\Weblcms\Course\Storage\DataManager::deletes(
            CourseEntityRelation::class_name(),
            $condition);
    }

    /**
     * Builds and returns the joins for open courses
     *
     * @return Joins
     */
    protected function getOpenCoursesJoins()
    {
        $joins = new Joins();

        $joins->add(
            new Join(
                CourseType::class_name(),
                new EqualityCondition(
                    new PropertyConditionVariable(Course::class_name(), Course::PROPERTY_COURSE_TYPE_ID),
                    new PropertyConditionVariable(CourseType::class_name(), CourseType::PROPERTY_ID)),
                Join::TYPE_LEFT));

        return $joins;
    }

    /**
     * Returns the condition for open courses
     *
     * @param Condition $condition
     * @param Condition $courseEntityRelationCondition - Limit the open courses by a condition for the course entity
     *        table
     * @return AndCondition
     */
    protected function getOpenCoursesCondition(Condition $condition = null, Condition $courseEntityRelationCondition = null)
    {
        $conditions = array();

        $conditions[] = new InCondition(
            new PropertyConditionVariable(Course::class_name(), Course::PROPERTY_ID),
            $this->getCourseIdsWithRolesAttached($courseEntityRelationCondition));

        if ($condition instanceof Condition)
        {
            $conditions[] = $condition;
        }

        return new AndCondition($conditions);
    }

    /**
     * Returns the condition for the closed courses
     *
     * @param Condition $condition
     * @param Condition $courseEntityRelationCondition
     *
     * @return Condition
     */
    protected function getClosedCoursesCondition(Condition $condition = null,
        Condition $courseEntityRelationCondition = null)
    {
        $conditions = array();
        $conditions[] = new NotCondition($this->getOpenCoursesCondition($condition, $courseEntityRelationCondition));

        if ($condition instanceof Condition)
        {
            $conditions[] = $condition;
        }

        return new AndCondition($conditions);
    }

    /**
     * Returns the course id's that have roles attached
     *
     * @param Condition $condition
     *
     * @return \int[]
     */
    protected function getCourseIdsWithRolesAttached(Condition $condition = null)
    {
        $conditions = array();

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(CourseEntityRelation::class_name(), CourseEntityRelation::PROPERTY_ENTITY_TYPE),
            new StaticConditionVariable(CourseEntityRelation::ENTITY_TYPE_ROLE));

        if ($condition instanceof Condition)
        {
            $conditions[] = $condition;
        }

        $condition = new AndCondition($conditions);

        $distinctParameters = new DataClassDistinctParameters($condition, CourseEntityRelation::PROPERTY_COURSE_ID);

        return \Chamilo\Application\Weblcms\Course\Storage\DataManager::distinct(
            CourseEntityRelation::class_name(),
            $distinctParameters);
    }

    /**
     * Builds the condition to retrieve CourseEntityRelations with specific roles
     *
     * @param Role[] $roles
     *
     * @return InCondition
     */
    protected function getConditionForRoles($roles)
    {
        $roleIds = array();
        foreach ($roles as $role)
        {
            $roleIds[] = $role->getId();
        }

        $courseEntityRelationCondition = new InCondition(
            new PropertyConditionVariable(CourseEntityRelation::class_name(), CourseEntityRelation::PROPERTY_ENTITY_ID),
            $roleIds);

        return $courseEntityRelationCondition;
    }
}