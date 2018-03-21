<?php

namespace Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Block\Assignment;

use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\Assignment\Entry;
use Chamilo\Application\Weblcms\Storage\DataClass\CourseEntityRelation;
use Chamilo\Libraries\Storage\DataClass\Property\DataClassProperties;
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
class AssignmentPlatformGroupScoresBlock extends AssignmentScoresBlock
{
    /**
     * @return int
     */
    protected function getAssignmentScoresEntityType()
    {
        return Entry::ENTITY_TYPE_PLATFORM_GROUP;
    }

    /**
     * @param \Chamilo\Core\Group\Storage\DataClass\Group $entity
     *
     * @return string
     */
    protected function renderEntityName($entity)
    {
        return $entity->get_name();
    }

    /**
     * @param \Chamilo\Core\Group\Storage\DataClass\Group $entity
     *
     * @return int
     */
    protected function getAssignmentScoresEntityId($entity)
    {
        return $entity->getId();
    }

    /**
     * @param int $course_id
     *
     * @return mixed | \Chamilo\Core\Group\Storage\DataClass\Group[]
     */
    protected function retrieveEntitiesForCourse($course_id)
    {
        $conditions = array();
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(CourseEntityRelation::class_name(), CourseEntityRelation::PROPERTY_COURSE_ID),
            new StaticConditionVariable($course_id)
        );
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                CourseEntityRelation::class_name(), CourseEntityRelation::PROPERTY_ENTITY_TYPE
            ),
            new StaticConditionVariable(CourseEntityRelation::ENTITY_TYPE_GROUP)
        );

        $group_ids = \Chamilo\Application\Weblcms\Course\Storage\DataManager::distinct(
            CourseEntityRelation::class_name(),
            new DataClassDistinctParameters(
                new AndCondition($conditions),
                new DataClassProperties(
                    array(
                        new PropertyConditionVariable(
                            CourseEntityRelation::class,
                            CourseEntityRelation::PROPERTY_ENTITY_ID
                        )
                    )
                )
            )
        );

        return \Chamilo\Core\Group\Storage\DataManager::retrieve_groups_and_subgroups($group_ids)->as_array();
    }
}
