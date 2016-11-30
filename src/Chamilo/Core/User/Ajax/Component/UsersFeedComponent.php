<?php
namespace Chamilo\Core\User\Ajax\Component;

use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Core\User\Storage\DataManager;
use Chamilo\Libraries\Architecture\JsonAjaxResult;
use Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\AdvancedElementFinderElement;
use Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\AdvancedElementFinderElements;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * Feed to return users
 * 
 * @author Sven Vanpoucke
 * @package application.weblcms
 */
class UsersFeedComponent extends \Chamilo\Core\User\Ajax\Manager
{
    const PARAM_SEARCH_QUERY = 'query';
    const PARAM_OFFSET = 'offset';
    const PROPERTY_TOTAL_ELEMENTS = 'total_elements';
    const PROPERTY_ELEMENTS = 'elements';

    private $user_count = 0;

    /**
     * Runs this ajax component
     */
    public function run()
    {
        $result = new JsonAjaxResult();
        
        $search_query = Request::post(self::PARAM_SEARCH_QUERY);
        
        $elements = $this->get_elements();
        
        $elements = $elements->as_array();
        
        $result->set_property(self::PROPERTY_ELEMENTS, $elements);
        $result->set_property(self::PROPERTY_TOTAL_ELEMENTS, $this->user_count);
        
        $result->display();
    }

    /**
     * Returns all the elements for this feed
     * 
     * @return AdvancedElementFinderElements
     */
    private function get_elements()
    {
        $elements = new AdvancedElementFinderElements();
        
        // Add user category
        $user_category = new AdvancedElementFinderElement(
            'users', 
            'category', 
            Translation::get('Users'), 
            Translation::get('Users'));
        $elements->add_element($user_category);
        
        $users = $this->retrieve_users();
        if ($users)
        {
            while ($user = $users->next_result())
            {
                $user_category->add_child($this->get_element_for_user($user));
            }
        }
        
        return $elements;
    }

    /**
     * Retrieves the users from the course (direct subscribed and group subscribed)
     * 
     * @return ResultSet
     */
    public function retrieve_users()
    {
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
        
        // Only include active users
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(User::class_name(), User::PROPERTY_ACTIVE), 
            new StaticConditionVariable(1));
        
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
        
        $this->user_count = DataManager::count(User::class_name(), $condition);
        $parameters = new DataClassRetrievesParameters(
            $condition, 
            100, 
            $this->get_offset(), 
            array(
                new OrderBy(new PropertyConditionVariable(User::class_name(), User::PROPERTY_LASTNAME)), 
                new OrderBy(new PropertyConditionVariable(User::class_name(), User::PROPERTY_FIRSTNAME))));
        
        return DataManager::retrieves(User::class_name(), $parameters);
    }

    /**
     * Returns the selected offset
     * 
     * @return int
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
     * Returns the advanced element finder element for the given user
     * 
     * @param $user User
     *
     * @return AdvancedElementFinderElement
     */
    protected function get_element_for_user($user)
    {
        return new AdvancedElementFinderElement(
            'user_' . $user->get_id(), 
            'type type_user', 
            $user->get_fullname(), 
            $user->get_official_code());
    }

    public function set_user_count($user_count)
    {
        $this->user_count = $user_count;
    }
}
