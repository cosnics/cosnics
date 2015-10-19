<?php
namespace Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Block\Assignment;

use Chamilo\Application\Weblcms\Course\Storage\DataClass\CourseGroupRelation;
use Chamilo\Application\Weblcms\Rights\Entities\CoursePlatformGroupEntity;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;

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
        return \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission :: SUBMITTER_TYPE_PLATFORM_GROUP;
    }

    /**
     * Returns the groups for this reporting block.
     *
     * @return array The groups
     */
    public function get_groups()
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(CourseGroupRelation :: class_name(), CourseGroupRelation :: PROPERTY_COURSE_ID),
            new StaticConditionVariable($this->course_id));

        $course_groups_rels_resultset = $course_group_relations = \Chamilo\Application\Weblcms\Course\Storage\DataManager :: retrieves(
            CourseGroupRelation :: class_name(),
            new DataClassRetrievesParameters($condition));

        $group_ids = array();
        while ($group_rel = $course_groups_rels_resultset->next_result())
        {
            $group_ids[] = $group_rel->get_group_id();
        }

        return \Chamilo\Core\Group\Storage\DataManager :: retrieve_groups_and_subgroups($group_ids)->as_array();
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
        $groups_resultset = \Chamilo\Core\Group\Storage\DataManager :: retrieve_groups_and_subgroups(
            $target_entities[CoursePlatformGroupEntity :: ENTITY_TYPE]);

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
