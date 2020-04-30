<?php
namespace Chamilo\Application\Weblcms\Ajax\Component;

use Chamilo\Application\Weblcms\Rights\Entities\CourseGroupEntity;
use Chamilo\Application\Weblcms\Rights\Entities\CourseUserEntity;
use Chamilo\Application\Weblcms\Storage\DataManager;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroup;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroupUserRelation;
use Chamilo\Libraries\Ajax\Component\GroupsFeedComponent;
use Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\AdvancedElementFinderElement;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\PatternMatchCondition;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 * Feed to return the course groups of this course
 *
 * @author Sven Vanpoucke
 * @package Chamilo\Application\Weblcms\Ajax\Component
 */
class CourseGroupsFeedComponent extends GroupsFeedComponent
{
    const PARAM_COURSE_ID = 'course_id';

    /**
     * Returns the required parameters
     *
     * @return string[]
     */
    public function getRequiredPostParameters()
    {
        return array(self::PARAM_COURSE_ID);
    }

    /**
     * Returns the element for a specific group
     *
     * @param \Chamilo\Core\Group\Storage\DataClass\Group $group
     *
     * @return AdvancedElementFinderElement
     */
    public function get_group_element($group)
    {
        $glyph = new FontAwesomeGlyph('users', array(), null, 'fas');

        return new AdvancedElementFinderElement(
            CourseGroupEntity::ENTITY_TYPE . '_' . $group->getId(), $glyph->getClassNamesString(), $group->get_name(),
            $group->get_description(), AdvancedElementFinderElement::TYPE_SELECTABLE_AND_FILTER
        );
    }

    /**
     * Returns the element for a specific user
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @return AdvancedElementFinderElement
     */
    public function get_user_element($user)
    {
        $glyph = new FontAwesomeGlyph('user', array(), null, 'fas');

        return new AdvancedElementFinderElement(
            CourseUserEntity::ENTITY_TYPE . '_' . $user->getId(), $glyph->getClassNamesString(), $user->get_fullname(),
            $user->get_official_code()
        );
    }

    /**
     * Retrieves all the users for the selected group
     */
    public function get_user_ids()
    {
        $filter = Request::post(self::PARAM_FILTER);
        $filter_id = substr($filter, 2);

        if (!$filter_id)
        {
            return array();
        }

        $condition = new EqualityCondition(
            new PropertyConditionVariable(
                CourseGroupUserRelation::class, CourseGroupUserRelation::PROPERTY_COURSE_GROUP
            ), new StaticConditionVariable($filter_id)
        );
        $relations = DataManager::retrieves(
            CourseGroupUserRelation::class, new DataClassRetrievesParameters($condition)
        );

        $user_ids = array();

        while ($relation = $relations->next_result())
        {
            $user_ids[] = $relation->get_user();
        }

        return $user_ids;
    }

    /**
     * Returns all the groups for this feed
     *
     * @return \Chamilo\Libraries\Storage\ResultSet\ResultSet
     */
    public function retrieve_groups()
    {
        // Set the conditions for the search query
        $search_query = Request::post(self::PARAM_SEARCH_QUERY);
        if ($search_query && $search_query != '')
        {
            $query = '*' . $search_query . '*';
            $conditions[] = new PatternMatchCondition(
                new PropertyConditionVariable(CourseGroup::class, CourseGroup::PROPERTY_NAME), $query
            );
        }

        // Set the course code
        $course_id = $this->getPostDataValue(self::PARAM_COURSE_ID);

        // Set the filter conditions
        $filter = Request::post(self::PARAM_FILTER);
        $filter_id = substr($filter, 2);

        if ($filter_id)
        {
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(CourseGroup::class, CourseGroup::PROPERTY_PARENT_ID),
                new StaticConditionVariable($filter_id)
            );
        }
        else
        {
            $root_course_group =
                \Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataManager::retrieve_course_group_root(
                    $course_id
                );

            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(CourseGroup::class, CourseGroup::PROPERTY_PARENT_ID),
                new StaticConditionVariable($root_course_group->get_id())
            );
        }

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(CourseGroup::class, CourseGroup::PROPERTY_COURSE_CODE),
            new StaticConditionVariable($course_id)
        );

        // Combine the conditions
        $count = count($conditions);
        if ($count > 1)
        {
            $condition = new AndCondition($conditions);
        }

        if ($count == 1)
        {
            $condition = $conditions[0];
        }

        return DataManager::retrieves(
            CourseGroup::class, new DataClassRetrievesParameters(
                $condition, null, null, array(
                    new OrderBy(new PropertyConditionVariable(CourseGroup::class, CourseGroup::PROPERTY_NAME))
                )
            )
        );
    }
}
