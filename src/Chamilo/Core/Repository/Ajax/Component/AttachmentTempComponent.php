<?php
namespace Chamilo\Libraries\Ajax\Component;

use Chamilo\Application\Weblcms\Rights\Entities\CoursePlatformGroupEntity;
use Chamilo\Application\Weblcms\Rights\Entities\CourseUserEntity;
use Chamilo\Application\Weblcms\Storage\DataClass\CourseEntityRelation;
use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Core\Group\Storage\DataClass\GroupRelUser;
use Chamilo\Core\Group\Storage\DataManager;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Ajax\Manager;
use Chamilo\Libraries\Architecture\JsonAjaxResult;
use Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\AdvancedElementFinderElement;
use Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\AdvancedElementFinderElements;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Storage\DataClass\Property\DataClassProperties;
use Chamilo\Libraries\Storage\Parameters\DataClassDistinctParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\Condition\PatternMatchCondition;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * Feed to return the course groups of this course
 *
 * @author Sven Vanpoucke
 * @package Chamilo\Libraries\Ajax\Component
 */
abstract class GroupsFeedComponent extends Manager
{
    const PARAM_COURSE_ID = 'course_id';

    const PARAM_FILTER = 'filter';

    const PARAM_OFFSET = 'offset';

    const PARAM_SEARCH_QUERY = 'query';

    const PROPERTY_ELEMENTS = 'elements';

    const PROPERTY_TOTAL_ELEMENTS = 'total_elements';

    /**
     *
     * @var integer
     */
    protected $user_count = 0;

    /**
     *
     * @see \Chamilo\Libraries\Architecture\Application\Application::run()
     */
    public function run()
    {
        $result = new JsonAjaxResult();

        $elements = $this->get_elements();
        $elements = $elements->as_array();

        $result->set_property(self::PROPERTY_ELEMENTS, $elements);

        if ($this->user_count > 0)
        {
            $result->set_property(self::PROPERTY_TOTAL_ELEMENTS, $this->user_count);
        }

        $result->display();
    }

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
     * Returns all the elements for this feed
     *
     * @return \Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\AdvancedElementFinderElements
     */
    private function get_elements()
    {
        $elements = new AdvancedElementFinderElements();
        $glyph = new FontAwesomeGlyph('folder', array(), null, 'fas');

        // Add groups
        $groups = $this->retrieve_groups();
        if ($groups && $groups->size() > 0)
        {
            // Add group category
            $group_category = new AdvancedElementFinderElement(
                'groups', $glyph->getClassNamesString(), Translation::get('Groups'), Translation::get('Groups')
            );
            $elements->add_element($group_category);

            while ($group = $groups->next_result())
            {
                $group_category->add_child($this->get_group_element($group));
            }
        }

        // Add users
        $users = $this->retrieve_users();
        if ($users && $users->count() > 0)
        {
            // Add user category
            $user_category = new AdvancedElementFinderElement('users', $glyph->getClassNamesString(), 'Users', 'Users');
            $elements->add_element($user_category);

            foreach ($users as $user)
            {
                $user_category->add_child($this->get_user_element($user));
            }
        }

        return $elements;
    }

    /**
     * Returns the element for a specific group
     *
     * @return AdvancedElementFinderElement
     */
    public function get_group_element($group)
    {
        $glyph = new FontAwesomeGlyph('users', array(), null, 'fas');

        return new AdvancedElementFinderElement(
            CoursePlatformGroupEntity::ENTITY_TYPE . '_' . $group->get_id(), $glyph->getClassNamesString(),
            $group->get_name(), $group->get_code(), AdvancedElementFinderElement::TYPE_SELECTABLE_AND_FILTER
        );
    }

    /**
     *
     * @return integer
     */
    protected function get_offset()
    {
        $offset = Request::post(self::PARAM_OFFSET);
        if (!isset($offset) || is_null($offset))
        {
            $offset = 0;
        }

        return $offset;
    }

    /**
     * Returns the element for a specific user
     *
     * @return AdvancedElementFinderElement
     */
    public function get_user_element($user)
    {
        $glyph = new FontAwesomeGlyph('user', array(), null, 'fas');

        return new AdvancedElementFinderElement(
            CourseUserEntity::ENTITY_TYPE . '_' . $user->get_id(), $glyph->getClassNamesString(), $user->get_fullname(),
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
            new PropertyConditionVariable(GroupRelUser::class_name(), GroupRelUser::PROPERTY_GROUP_ID),
            new StaticConditionVariable($filter_id)
        );
        $relations = DataManager::retrieves(
            GroupRelUser::class_name(), new DataClassRetrievesParameters($condition)
        );

        $user_ids = array();

        while ($relation = $relations->next_result())
        {
            $user_ids[] = $relation->get_user_id();
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
                new PropertyConditionVariable(Group::class_name(), Group::PROPERTY_NAME), $query
            );
        }

        // Set the filter conditions
        $filter = Request::post(self::PARAM_FILTER);

        // Javascript filter
        if (!is_null($filter))
        {
            $filter_id = substr($filter, 2);
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(Group::class_name(), Group::PROPERTY_PARENT_ID),
                new StaticConditionVariable($filter_id)
            );
        }
        else
        {
            $course_id = Request::post(self::PARAM_COURSE_ID);

            $groupConditions = array();
            $groupConditions[] = new EqualityCondition(
                new PropertyConditionVariable(
                    CourseEntityRelation::class_name(), CourseEntityRelation::PROPERTY_COURSE_ID
                ), new StaticConditionVariable($course_id)
            );
            $groupConditions[] = new EqualityCondition(
                new PropertyConditionVariable(
                    CourseEntityRelation::class_name(), CourseEntityRelation::PROPERTY_ENTITY_TYPE
                ), new StaticConditionVariable(CourseEntityRelation::ENTITY_TYPE_GROUP)
            );

            $subscribed_group_ids = \Chamilo\Application\Weblcms\Course\Storage\DataManager::distinct(
                CourseEntityRelation::class_name(), new DataClassDistinctParameters(
                    new AndCondition($groupConditions), new DataClassProperties(
                        array(
                            new PropertyConditionVariable(
                                CourseEntityRelation::class, CourseEntityRelation::PROPERTY_ENTITY_ID
                            )
                        )
                    )
                )
            );

            if (count($subscribed_group_ids) == 0)
            {
                return;
            }

            $conditions[] = new InCondition(
                new PropertyConditionVariable(Group::class_name(), Group::PROPERTY_ID), $subscribed_group_ids
            );
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

        return DataManager::retrieves(
            Group::class_name(), new DataClassRetrievesParameters(
                $condition, null, null,
                array(new OrderBy(new PropertyConditionVariable(Group::class_name(), Group::PROPERTY_NAME)))
            )
        );
    }

    /**
     * Retrieves all the users for the selected group
     *
     * @return \Chamilo\Libraries\Storage\ResultSet\ResultSet
     */
    private function retrieve_users()
    {
        $conditions = array();

        $user_ids = $this->get_user_ids();
        if (count($user_ids) == 0)
        {
            return;
        }

        $conditions[] =
            new InCondition(new PropertyConditionVariable(User::class_name(), User::PROPERTY_ID), $user_ids);

        $search_query = Request::post(self::PARAM_SEARCH_QUERY);

        // Set the conditions for the search query
        if ($search_query && $search_query != '')
        {
            $conditions[] = Utilities::query_to_condition(
                $search_query, array(
                    new PropertyConditionVariable(User::class_name(), User::PROPERTY_USERNAME),
                    new PropertyConditionVariable(User::class_name(), User::PROPERTY_FIRSTNAME),
                    new PropertyConditionVariable(User::class_name(), User::PROPERTY_LASTNAME)
                )
            );
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

        $this->user_count = $this->getUserService()->countUsers($condition);

        return $this->getUserService()->findUsers(
            $condition, $this->get_offset(), 100, array(
                new OrderBy(new PropertyConditionVariable(User::class_name(), User::PROPERTY_LASTNAME)),
                new OrderBy(new PropertyConditionVariable(User::class_name(), User::PROPERTY_FIRSTNAME))
            )
        );
    }
}
