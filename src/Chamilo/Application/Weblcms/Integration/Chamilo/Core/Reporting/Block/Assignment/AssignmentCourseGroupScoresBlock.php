<?php
namespace Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Block\Assignment;

use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\Assignment\Entry;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroup;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataManager as CourseGroupDataManager;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 * Reporting block with scores for the course groups.
 * 
 * @author Bert De Clercq (Hogeschool Gent)
 */
class AssignmentCourseGroupScoresBlock extends AssignmentScoresBlock
{

    /**
     * @return int
     */
    protected function getAssignmentScoresEntityType()
    {
        return Entry::ENTITY_TYPE_COURSE_GROUP;
    }

    /**
     * @param CourseGroup $entity
     *
     * @return string
     */
    protected function renderEntityName($entity)
    {
        return $entity->get_name();
    }

    /**
     * @param CourseGroup $entity
     *
     * @return int
     */
    protected function getEntityIdFromEntity($entity)
    {
        return $entity->getId();
    }

    /**
     * @param int $course_id
     *
     * @return mixed[]|CourseGroup[]
     */
    protected function retrieveEntitiesForCourse($course_id)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(CourseGroup::class_name(), CourseGroup::PROPERTY_COURSE_CODE),
            new StaticConditionVariable($course_id));

        return CourseGroupDataManager::retrieves(
            CourseGroup::class_name(),
            new DataClassRetrievesParameters($condition))->as_array();
    }
}
