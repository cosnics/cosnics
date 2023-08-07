<?php
namespace Chamilo\Libraries\Ajax\Component;

use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\JsonAjaxResult;
use Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\AdvancedElementFinderElement;
use Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\AdvancedElementFinderElements;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * Feed to return the course groups of this course
 *
 * @author Sven Vanpoucke
 * @package Chamilo\Libraries\Ajax\Component
 */
abstract class GroupsFeedComponent extends \Chamilo\Libraries\Ajax\Manager
{
    const PARAM_SEARCH_QUERY = 'query';
    const PARAM_OFFSET = 'offset';
    const PARAM_FILTER = 'filter';
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
     * Returns all the elements for this feed
     *
     * @return \Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\AdvancedElementFinderElements
     */
    private function get_elements()
    {
        $elements = new AdvancedElementFinderElements();

        // Add groups
        $groups = $this->retrieve_groups();
        if ($groups && $groups->size() > 0)
        {
            // Add group category
            $group_category = new AdvancedElementFinderElement(
                'groups',
                'category',
                Translation::get('Groups'),
                Translation::get('Groups'));
            $elements->add_element($group_category);

            while ($group = $groups->next_result())
            {
                $group_category->add_child($this->get_group_element($group));
            }
        }

        // Add users
        $users = $this->retrieve_users();
        if ($users && $users->size() > 0)
        {
            // Add user category
            $user_category = new AdvancedElementFinderElement('users', 'category', 'Users', 'Users');
            $elements->add_element($user_category);

            while ($user = $users->next_result())
            {
                $user_category->add_child($this->get_user_element($user));
            }
        }

        return $elements;
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
        if (!is_array($user_ids) || count($user_ids) == 0)
        {
            return;
        }

        $conditions[] = new InCondition(new PropertyConditionVariable(User::class_name(), User::PROPERTY_ID), $user_ids);

        $search_query = Request::post(self::PARAM_SEARCH_QUERY);

        // Set the conditions for the search query
        if ($search_query && $search_query != '')
        {
            $conditions[] = Utilities::query_to_condition(
                $search_query,
                array(
                    new PropertyConditionVariable(User::class_name(), User::PROPERTY_USERNAME),
                    new PropertyConditionVariable(User::class_name(), User::PROPERTY_FIRSTNAME),
                    new PropertyConditionVariable(User::class_name(), User::PROPERTY_LASTNAME)));
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

        $this->user_count = \Chamilo\Core\User\Storage\DataManager::count(
            \Chamilo\Core\User\Storage\DataClass\User::class_name(),
            new DataClassCountParameters($condition));

        return \Chamilo\Core\User\Storage\DataManager::retrieves(
            \Chamilo\Core\User\Storage\DataClass\User::class_name(),
            new DataClassRetrievesParameters(
                $condition,
                100,
                $this->get_offset(),
                array(
                    new OrderBy(new PropertyConditionVariable(User::class_name(), User::PROPERTY_LASTNAME)),
                    new OrderBy(new PropertyConditionVariable(User::class_name(), User::PROPERTY_FIRSTNAME)))));
    }

    /**
     *
     * @return integer
     */
    protected function get_offset()
    {
        $offset = Request::post(self::PARAM_OFFSET);
        if (! isset($offset) || is_null($offset))
        {
            $offset = 0;
        }

        return $offset;
    }

    /**
     *
     * @param \Chamilo\Core\Group\Storage\DataClass\Group $group
     * @return \Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\AdvancedElementFinderElement
     */
    abstract public function get_group_element($group);

    /**
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @return \Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\AdvancedElementFinderElement
     */
    abstract public function get_user_element($user);

    /**
     *
     * @return \Chamilo\Libraries\Storage\ResultSet\ResultSet
     */
    abstract public function retrieve_groups();

    /**
     *
     * @return integer[]
     */
    abstract public function get_user_ids();
}
