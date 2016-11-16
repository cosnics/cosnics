<?php
namespace Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Block\Assignment;

use Chamilo\Application\Weblcms\Rights\Entities\CoursePlatformGroupEntity;
use Chamilo\Application\Weblcms\Storage\DataClass\CourseEntityRelation;
use Chamilo\Libraries\Storage\Parameters\DataClassDistinctParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 * Reporting block with score for the platform groups.
 * 
 * @author Bert De Clercq (Hogeschool Gent)
 */
class AssignmentPlatformGroupScoresBlock extends AssignmentGroupScoresBlock
{

    /**
     * Returns the submitter type for this reporting block.
     * 
     * @return int The submitter type
     */
    public function get_current_submitter_type()
    {
        return \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission::SUBMITTER_TYPE_PLATFORM_GROUP;
    }

    /**
     * Returns the groups for this reporting block.
     * 
     * @return array The groups
     */
    public function get_groups()
    {
        $conditions = array();
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(CourseEntityRelation::class_name(), CourseEntityRelation::PROPERTY_COURSE_ID), 
            new StaticConditionVariable($this->course_id));
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                CourseEntityRelation::class_name(), 
                CourseEntityRelation::PROPERTY_ENTITY_TYPE), 
            new StaticConditionVariable(CourseEntityRelation::ENTITY_TYPE_GROUP));
        
        $group_ids = \Chamilo\Application\Weblcms\Course\Storage\DataManager::distinct(
            CourseEntityRelation::class_name(), 
            new DataClassDistinctParameters(new AndCondition($conditions), CourseEntityRelation::PROPERTY_ENTITY_ID));
        
        return \Chamilo\Core\Group\Storage\DataManager::retrieve_groups_and_subgroups($group_ids)->as_array();
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
        $groups_resultset = \Chamilo\Core\Group\Storage\DataManager::retrieve_groups_and_subgroups(
            $target_entities[CoursePlatformGroupEntity::ENTITY_TYPE]);
        
        while ($group = $groups_resultset->next_result())
        {
            if ($group_id == $group->get_id())
            {
                return true;
            }
        }
        
        return false;
    }
}
