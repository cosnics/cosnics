<?php
namespace Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Block\Assignment;

use Chamilo\Application\Weblcms\Rights\Entities\CourseGroupEntity;
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
class AssignmentCourseGroupScoresBlock extends AssignmentGroupScoresBlock
{

    /**
     * Returns the submitter type for this reporting block.
     *
     * @return int The submitter type
     */
    public function get_current_submitter_type()
    {
        return \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission :: SUBMITTER_TYPE_COURSE_GROUP;
    }

    /**
     * Returns the groups for this reporting block.
     *
     * @return array The groups
     */
    public function get_groups()
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(CourseGroup :: class_name(), CourseGroup :: PROPERTY_PARENT_ID),
            new StaticConditionVariable($this->course_id));

        return CourseGroupDataManager :: retrieves(
            CourseGroup :: class_name(),
            new DataClassRetrievesParameters($condition))->as_array();
    }

    /**
     * Returns true if the group with the given group id belongs to the given target entities and false otherwise.
     *
     * @param $target_entities array The target entities
     * @param $group_id int The group id
     * @return boolean True if the given group id belongs to the given target entities
     */
    public function is_target_entity_group($target_entities, $group_id)
    {
        foreach ($target_entities[CourseGroupEntity :: ENTITY_TYPE] as $course_group_id)
        {
            if ($group_id == $course_group_id)
            {
                return true;
            }
        }

        return false;
    }
}
