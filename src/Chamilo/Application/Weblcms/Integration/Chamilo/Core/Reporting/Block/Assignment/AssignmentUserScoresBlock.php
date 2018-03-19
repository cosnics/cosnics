<?php

namespace Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Block\Assignment;

use Chamilo\Application\Weblcms\Course\Storage\DataManager as CourseDataManager;
use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\Assignment\Entry;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Storage\DataClass\Publication;
use Chamilo\Core\Reporting\ReportingData;
use Chamilo\Core\Repository\Common\Renderer\ContentObjectRenderer;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Manager;
use Chamilo\Core\Repository\ContentObject\Assignment\Storage\DataClass\Assignment;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\ResultSet\ResultSet;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Storage\DataManager\DataManager;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Hogent\Application\Assignment\DataTransferObject\AssignmentPublication;

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
    protected function getEntityType()
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
