<?php
namespace Chamilo\Application\Weblcms\Course\OpenCourse\Storage\Repository;

use Chamilo\Application\Weblcms\Course\OpenCourse\Storage\Repository\Interfaces\OpenCourseRepositoryInterface;
use Chamilo\Application\Weblcms\Course\Storage\DataClass\Course;
use Chamilo\Application\Weblcms\Course\Storage\DataManager;
use Chamilo\Application\Weblcms\CourseType\Storage\DataClass\CourseType;
use Chamilo\Application\Weblcms\Storage\DataClass\CourseEntityRelation;
use Chamilo\Core\User\Roles\Storage\DataClass\Role;
use Chamilo\Libraries\Storage\DataManager\Repository\DataManagerRepository;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassDistinctParameters;
use Chamilo\Libraries\Storage\Parameters\RetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\Condition\NotCondition;
use Chamilo\Libraries\Storage\Query\Join;
use Chamilo\Libraries\Storage\Query\Joins;
use Chamilo\Libraries\Storage\Query\RetrieveProperties;
use Chamilo\Libraries\Storage\Query\Variable\PropertiesConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Repository to manage the data open courses
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class OpenCourseRepository extends DataManagerRepository implements OpenCourseRepositoryInterface
{

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
     * Counts the courses that are not open
     *
     * @param Condition $condition
     *
     * @return int
     */
    public function countClosedCourses(Condition $condition = null)
    {
        return DataManager::count(
            Course::class, new DataClassCountParameters($this->getClosedCoursesCondition($condition))
        );
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

        return DataManager::count(Course::class, $countParameters);
    }

    /**
     * Counts the open courses by the given user roles
     *
     * @param Role[]   $roles
     * @param Condition $condition
     *
     * @return int
     */
    public function countOpenCoursesByRoles($roles = [], Condition $condition = null)
    {
        return $this->countOpenCourses($this->getOpenCoursesCondition($condition, $this->getConditionForRoles($roles)));
    }

    /**
     * Retrieves the open courses
     *
     * @param Condition $condition
     * @param int $offset
     * @param int $count
     * @param \Chamilo\Libraries\Storage\Query\OrderBy $orderBy
     *
     * @return ArrayCollection
     */
    public function findAllOpenCourses(Condition $condition = null, $offset = null, $count = null, $orderBy = null)
    {
        return $this->findOpenCourses($this->getOpenCoursesCondition($condition), $offset, $count, $orderBy);
    }

    /**
     * Retrieves the courses that are not open
     *
     * @param Condition $condition
     * @param int $offset
     * @param int $count
     * @param \Chamilo\Libraries\Storage\Query\OrderBy $orderBy
     *
     * @return ArrayCollection
     */
    public function findClosedCourses(Condition $condition = null, $offset = null, $count = null, $orderBy = null)
    {
        return DataManager::retrieves(
            Course::class,
            new RetrievesParameters($this->getClosedCoursesCondition($condition), $count, $offset, $orderBy)
        );
    }

    /**
     * Helper function to find open courses
     *
     * @param Condition $condition
     * @param null $offset
     * @param null $count
     * @param \Chamilo\Libraries\Storage\Query\OrderBy $orderBy
     *
     * @return ArrayCollection
     */
    protected function findOpenCourses(Condition $condition = null, $offset = null, $count = null, $orderBy = null)
    {
        $properties = new RetrieveProperties();
        $properties->add(new PropertiesConditionVariable(Course::class));

        $properties->add(
            new PropertyConditionVariable(
                CourseType::class, CourseType::PROPERTY_TITLE, Course::PROPERTY_COURSE_TYPE_TITLE
            )
        );

        $recordsParameters = new RetrievesParameters(
            condition: $condition, count: $count, offset: $offset, orderBy: $orderBy, joins: $this->getOpenCoursesJoins(
        ), retrieveProperties: $properties
        );

        return DataManager::records(
            Course::class, $recordsParameters
        );
    }

    /**
     * Retrieves the open courses for the given user roles
     *
     * @param Role[]   $roles
     * @param Condition $condition
     * @param int $offset
     * @param int $count
     * @param \Chamilo\Libraries\Storage\Query\OrderBy $orderBy
     *
     * @return ArrayCollection
     */
    public function findOpenCoursesByRoles(
        $roles = [], Condition $condition = null, $offset = null, $count = null, $orderBy = null
    )
    {
        return $this->findOpenCourses(
            $this->getOpenCoursesCondition($condition, $this->getConditionForRoles($roles)), $offset, $count, $orderBy
        );
    }

    /**
     * Returns the condition for the closed courses
     *
     * @param Condition $condition
     * @param Condition $courseEntityRelationCondition
     *
     * @return Condition
     */
    protected function getClosedCoursesCondition(
        Condition $condition = null, Condition $courseEntityRelationCondition = null
    )
    {
        $conditions = [];
        $conditions[] = new NotCondition($this->getOpenCoursesCondition($condition, $courseEntityRelationCondition));

        if ($condition instanceof Condition)
        {
            $conditions[] = $condition;
        }

        return new AndCondition($conditions);
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
        $roleIds = [];
        foreach ($roles as $role)
        {
            $roleIds[] = $role->getId();
        }

        $courseEntityRelationCondition = new InCondition(
            new PropertyConditionVariable(CourseEntityRelation::class, CourseEntityRelation::PROPERTY_ENTITY_ID),
            $roleIds
        );

        return $courseEntityRelationCondition;
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
        $conditions = [];

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(CourseEntityRelation::class, CourseEntityRelation::PROPERTY_ENTITY_TYPE),
            new StaticConditionVariable(CourseEntityRelation::ENTITY_TYPE_ROLE)
        );

        if ($condition instanceof Condition)
        {
            $conditions[] = $condition;
        }

        $condition = new AndCondition($conditions);

        $distinctParameters = new DataClassDistinctParameters(
            $condition, new RetrieveProperties(
                [
                    new PropertyConditionVariable(CourseEntityRelation::class, CourseEntityRelation::PROPERTY_COURSE_ID)
                ]
            )
        );

        return DataManager::distinct(
            CourseEntityRelation::class, $distinctParameters
        );
    }

    /**
     * Returns the condition for open courses
     *
     * @param Condition $condition
     * @param Condition $courseEntityRelationCondition - Limit the open courses by a condition for the course entity
     *        table
     *
     * @return AndCondition
     */
    protected function getOpenCoursesCondition(
        Condition $condition = null, Condition $courseEntityRelationCondition = null
    )
    {
        $conditions = [];

        $conditions[] = new InCondition(
            new PropertyConditionVariable(Course::class, Course::PROPERTY_ID),
            $this->getCourseIdsWithRolesAttached($courseEntityRelationCondition)
        );

        if ($condition instanceof Condition)
        {
            $conditions[] = $condition;
        }

        return new AndCondition($conditions);
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
                CourseType::class, new EqualityCondition(
                new PropertyConditionVariable(Course::class, Course::PROPERTY_COURSE_TYPE_ID),
                new PropertyConditionVariable(CourseType::class, CourseType::PROPERTY_ID)
            ), Join::TYPE_LEFT
            )
        );

        return $joins;
    }

    /**
     * Returns the roles for a given open course
     *
     * @param Course $course
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getRolesForOpenCourse(Course $course)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(CourseEntityRelation::class, CourseEntityRelation::PROPERTY_COURSE_ID),
            new StaticConditionVariable($course->getId())
        );

        $joins = new Joins();

        $joins->add(
            new Join(
                CourseEntityRelation::class, new AndCondition(
                    [
                        new EqualityCondition(
                            new PropertyConditionVariable(
                                CourseEntityRelation::class, CourseEntityRelation::PROPERTY_ENTITY_ID
                            ), new PropertyConditionVariable(Role::class, Role::PROPERTY_ID)
                        ),
                        new EqualityCondition(
                            new PropertyConditionVariable(
                                CourseEntityRelation::class, CourseEntityRelation::PROPERTY_ENTITY_TYPE
                            ), new StaticConditionVariable(CourseEntityRelation::ENTITY_TYPE_ROLE)
                        )
                    ]
                )
            )
        );

        return DataManager::retrieves(
            Role::class, new RetrievesParameters($condition, null, null, null, $joins)
        );
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
            [
                new InCondition(
                    new PropertyConditionVariable(
                        CourseEntityRelation::class, CourseEntityRelation::PROPERTY_COURSE_ID
                    ), $courseIds
                ),
                new EqualityCondition(
                    new PropertyConditionVariable(
                        CourseEntityRelation::class, CourseEntityRelation::PROPERTY_ENTITY_TYPE
                    ), new StaticConditionVariable(CourseEntityRelation::ENTITY_TYPE_ROLE)
                )
            ]
        );

        return DataManager::deletes(
            CourseEntityRelation::class, $condition
        );
    }
}