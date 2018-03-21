<?php

namespace Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Block\Assignment;

use Chamilo\Application\Weblcms\Course\Storage\DataManager as CourseDataManager;
use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\Assignment\Entry;
use Chamilo\Libraries\Storage\DataClass\DataClass;

/**
 * @package Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Block\Assignment
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class AssignmentUserScoresBlock extends AssignmentScoresBlock
{

    /**
     * @return int
     */
    protected function getAssignmentScoresEntityType()
    {
        return Entry::ENTITY_TYPE_USER;
    }

    /**
     * @param mixed $entity
     *
     * @return string
     */
    protected function renderEntityName($entity)
    {
        return \Chamilo\Core\User\Storage\DataClass\User::fullname(
            $entity[\Chamilo\Core\User\Storage\DataClass\User::PROPERTY_FIRSTNAME],
            $entity[\Chamilo\Core\User\Storage\DataClass\User::PROPERTY_LASTNAME]
        );
    }

    /**
     * @param int $course_id
     *
     * @return string[][]
     */
    protected function retrieveEntitiesForCourse($course_id)
    {
        return CourseDataManager::retrieve_all_course_users($course_id)->as_array();
    }

    /**
     * @param mixed $entity
     *
     * @return int
     */
    protected function getEntityId($entity)
    {
        return $entity[DataClass::PROPERTY_ID];
    }
}
