<?php
namespace Chamilo\Core\User\Ajax\Component;

use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Core\User\Storage\DataManager;
use Chamilo\Libraries\Architecture\JsonAjaxResult;
use Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\AdvancedElementFinderElement;
use Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\AdvancedElementFinderElements;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Utilities\Utilities;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;

/**
 * Feed to return users
 *
 * @package Chamilo\Core\User\Ajax
 * @author Sven Vanpoucke
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class UsersFeedComponent extends \Chamilo\Core\User\Ajax\Manager
{
    const PARAM_SEARCH_QUERY = 'query';
    const PARAM_OFFSET = 'offset';
    const PROPERTY_TOTAL_ELEMENTS = 'total_elements';
    const PROPERTY_ELEMENTS = 'elements';

    /**
     *
     * @var integer
     */
    private $userCount = 0;

    /**
     * Runs this ajax component
     */
    public function run()
    {
        $result = new JsonAjaxResult();

        $result->set_property(self::PROPERTY_ELEMENTS, $this->getElements()->as_array());
        $result->set_property(self::PROPERTY_TOTAL_ELEMENTS, $this->userCount);

        $result->display();
    }

    /**
     * Returns all the elements for this feed
     *
     * @return \Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\AdvancedElementFinderElements
     */
    private function getElements()
    {
        $elements = new AdvancedElementFinderElements();

        // Add user category
        $user_category = new AdvancedElementFinderElement(
            'users',
            'category',
            Translation::get('Users'),
            Translation::get('Users'));
        $elements->add_element($user_category);

        $users = $this->retrieveUsers();

        if ($users)
        {
            while ($user = $users->next_result())
            {
                $user_category->add_child($this->getElementForUser($user));
            }
        }

        return $elements;
    }

    /**
     * Retrieves the users
     *
     * @return \Chamilo\Libraries\Storage\ResultSet\ResultSet
     */
    public function retrieveUsers()
    {
        $condition = $this->getCondition();

        $this->userCount = DataManager::count(User::class_name(), new DataClassCountParameters($condition));

        $parameters = new DataClassRetrievesParameters(
            $condition,
            100,
            $this->getOffset(),
            array(
                new OrderBy(new PropertyConditionVariable(User::class_name(), User::PROPERTY_LASTNAME)),
                new OrderBy(new PropertyConditionVariable(User::class_name(), User::PROPERTY_FIRSTNAME))));

        return DataManager::retrieves(User::class_name(), $parameters);
    }

    /**
     *
     * @return \Chamilo\Libraries\Storage\Query\Condition\AndCondition
     */
    protected function getCondition()
    {
        $searchQuery = Request::post(self::PARAM_SEARCH_QUERY);

        $conditions = array();

        // Set the conditions for the search query
        if ($searchQuery && $searchQuery != '')
        {
            $conditions[] = Utilities::query_to_condition(
                $searchQuery,
                array(
                    new PropertyConditionVariable(User::class_name(), User::PROPERTY_USERNAME),
                    new PropertyConditionVariable(User::class_name(), User::PROPERTY_FIRSTNAME),
                    new PropertyConditionVariable(User::class_name(), User::PROPERTY_LASTNAME)));
        }

        // Only include active users
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(User::class_name(), User::PROPERTY_ACTIVE),
            new StaticConditionVariable(1));

        return new AndCondition($conditions);
    }

    /**
     * Returns the selected offset
     *
     * @return integer
     */
    protected function getOffset()
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
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @return \Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\AdvancedElementFinderElement
     */
    protected function getElementForUser(User $user)
    {
        return new AdvancedElementFinderElement(
            'user_' . $user->get_id(),
            'type type_user',
            $user->get_fullname(),
            $user->get_official_code());
    }

    public function set_user_count($userCount)
    {
        $this->userCount = $userCount;
    }
}
