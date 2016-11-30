<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Infrastructure\Repository;

use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroup;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 * Repository for the course groups
 * 
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class CourseGroupRepository implements CourseGroupRepositoryInterface
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
            new StaticConditionVariable($courseId));
        
        return \Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataManager::count(
            CourseGroup::class_name(), 
            new DataClassCountParameters($condition));
    }
}