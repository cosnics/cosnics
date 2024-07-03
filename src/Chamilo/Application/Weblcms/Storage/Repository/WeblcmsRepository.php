<?php
namespace Chamilo\Application\Weblcms\Storage\Repository;

use Chamilo\Application\Weblcms\Course\Storage\DataClass\Course;
use Chamilo\Application\Weblcms\Course\Storage\DataManager;
use Chamilo\Application\Weblcms\Storage\DataClass\CourseEntityRelation;
use Chamilo\Application\Weblcms\Storage\Repository\Interfaces\WeblcmsRepositoryInterface;
use Chamilo\Core\User\Service\UserService;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\DependencyInjection\DependencyInjectionContainerBuilder;
use Chamilo\Libraries\Storage\Cache\DataClassRepositoryCache;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\StorageParameters;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 * Wrapper for the weblcms data manager
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class WeblcmsRepository implements WeblcmsRepositoryInterface
{

    /**
     * Clears the cache for the CourseEntityRelation class
     */
    public function clearCourseEntityRelationCache()
    {
        $this->getDataClassRepositoryCache()->truncateClass(CourseEntityRelation::class);
    }

    /**
     * @return \Chamilo\Libraries\Storage\Cache\DataClassRepositoryCache
     */
    protected function getDataClassRepositoryCache()
    {
        return DependencyInjectionContainerBuilder::getInstance()->createContainer()->get(
            DataClassRepositoryCache::class
        );
    }

    /**
     * @return \Chamilo\Core\User\Service\UserService
     */
    private function getUserService()
    {
        $containerBuilder = DependencyInjectionContainerBuilder::getInstance();
        $container = $containerBuilder->createContainer();

        return $container->get(UserService::class);
    }

    /**
     * Retrieves a course by a code
     *
     * @param string $courseCode
     *
     * @return Course
     */
    public function retrieveCourseByCode($courseCode)
    {
        return DataManager::retrieve_course_by_visual_code($courseCode);
    }

    /**
     * Retrieves a course entity relation by a given entity and course.
     * The entity is defined by a type
     * and an identifier.
     *
     * @param int $entityType
     * @param int $entityId
     * @param int $courseId
     *
     * @return \Chamilo\Application\Weblcms\Storage\DataClass\CourseEntityRelation
     */
    public function retrieveCourseEntityRelationByEntityAndCourse($entityType, $entityId, $courseId)
    {
        $conditions = [];

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                CourseEntityRelation::class, CourseEntityRelation::PROPERTY_ENTITY_TYPE
            ), new StaticConditionVariable($entityType)
        );

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(CourseEntityRelation::class, CourseEntityRelation::PROPERTY_ENTITY_ID),
            new StaticConditionVariable($entityId)
        );

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(CourseEntityRelation::class, CourseEntityRelation::PROPERTY_COURSE_ID),
            new StaticConditionVariable($courseId)
        );

        $condition = new AndCondition($conditions);

        return DataManager::retrieve(
            CourseEntityRelation::class, new StorageParameters(condition: $condition)
        );
    }

    /**
     * Retrieves a user by a username
     *
     * @param string $username
     *
     * @return User
     */
    public function retrieveUserByUsername($username)
    {
        return $this->getUserService()->findUserByUsername($username);
    }
}