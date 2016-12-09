<?php
namespace Chamilo\Application\Weblcms\Ajax\Component;

use Chamilo\Application\Weblcms\Rights\Entities\CoursePlatformGroupEntity;
use Chamilo\Application\Weblcms\Rights\Entities\CourseUserEntity;
use Chamilo\Application\Weblcms\Storage\DataClass\CourseEntityRelation;
use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Core\Group\Storage\DataClass\GroupRelUser;
use Chamilo\Libraries\Ajax\Component\GroupsFeedComponent;
use Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\AdvancedElementFinderElement;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Storage\Parameters\DataClassDistinctParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\Condition\PatternMatchCondition;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 * Feed to return the platform groups of this course
 * 
 * @author Sven Vanpoucke
 * @package application.weblcms
 */
class PlatformGroupsFeedComponent extends GroupsFeedComponent
{
    const PARAM_COURSE_ID = 'course_id';

    /**
     * Returns the required parameters
     * 
     * @return Array
     */
    public function getRequiredPostParameters()
    {
        return array(self::PARAM_COURSE_ID);
    }

    /**
     * Returns all the groups for this feed
     * 
     * @return ResultSet
     */
    public function retrieve_groups()
    {
        // Set the conditions for the search query
        $search_query = Request::post(self::PARAM_SEARCH_QUERY);
        if ($search_query && $search_query != '')
        {
            $query = '*' . $search_query . '*';
            $conditions[] = new PatternMatchCondition(
                new PropertyConditionVariable(Group::class_name(), Group::PROPERTY_NAME), 
                $query);
        }
        
        // Set the filter conditions
        $filter = Request::post(self::PARAM_FILTER);
        
        // Javascript filter
        if (! is_null($filter))
        {
            $filter_id = substr($filter, 2);
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(Group::class_name(), Group::PROPERTY_PARENT_ID), 
                new StaticConditionVariable($filter_id));
        }
        else
        {
            $course_id = Request::post(self::PARAM_COURSE_ID);
            
            $groupConditions = array();
            $groupConditions[] = new EqualityCondition(
                new PropertyConditionVariable(
                    CourseEntityRelation::class_name(), 
                    CourseEntityRelation::PROPERTY_COURSE_ID), 
                new StaticConditionVariable($course_id));
            $groupConditions[] = new EqualityCondition(
                new PropertyConditionVariable(
                    CourseEntityRelation::class_name(), 
                    CourseEntityRelation::PROPERTY_ENTITY_TYPE), 
                new StaticConditionVariable(CourseEntityRelation::ENTITY_TYPE_GROUP));
            
            $subscribed_group_ids = \Chamilo\Application\Weblcms\Course\Storage\DataManager::distinct(
                CourseEntityRelation::class_name(), 
                new DataClassDistinctParameters(
                    new AndCondition($groupConditions), 
                    CourseEntityRelation::PROPERTY_ENTITY_ID));
            
            if (count($subscribed_group_ids) == 0)
            {
                return;
            }
            
            $conditions[] = new InCondition(
                new PropertyConditionVariable(Group::class_name(), Group::PROPERTY_ID), 
                $subscribed_group_ids);
        }
        
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
        
        return \Chamilo\Core\Group\Storage\DataManager::retrieves(
            Group::class_name(), 
            new DataClassRetrievesParameters(
                $condition, 
                null, 
                null, 
                array(new OrderBy(new PropertyConditionVariable(Group::class_name(), Group::PROPERTY_NAME)))));
    }

    /**
     * Retrieves all the users for the selected group
     */
    public function get_user_ids()
    {
        $filter = Request::post(self::PARAM_FILTER);
        $filter_id = substr($filter, 2);
        
        if (! $filter_id)
        {
            return;
        }
        
        $condition = new EqualityCondition(
            new PropertyConditionVariable(GroupRelUser::class_name(), GroupRelUser::PROPERTY_GROUP_ID), 
            new StaticConditionVariable($filter_id));
        $relations = \Chamilo\Core\Group\Storage\DataManager::retrieves(
            GroupRelUser::class_name(), 
            new DataClassRetrievesParameters($condition));
        
        $user_ids = array();
        
        while ($relation = $relations->next_result())
        {
            $user_ids[] = $relation->get_user_id();
        }
        
        return $user_ids;
    }

    /**
     * Returns the element for a specific group
     * 
     * @return AdvancedElementFinderElement
     */
    public function get_group_element($group)
    {
        return new AdvancedElementFinderElement(
            CoursePlatformGroupEntity::ENTITY_TYPE . '_' . $group->get_id(), 
            'type type_group', 
            $group->get_name(), 
            $group->get_code(), 
            AdvancedElementFinderElement::TYPE_SELECTABLE_AND_FILTER);
    }

    /**
     * Returns the element for a specific user
     * 
     * @return AdvancedElementFinderElement
     */
    public function get_user_element($user)
    {
        return new AdvancedElementFinderElement(
            CourseUserEntity::ENTITY_TYPE . '_' . $user->get_id(), 
            'type type_user', 
            $user->get_fullname(), 
            $user->get_official_code());
    }
}
