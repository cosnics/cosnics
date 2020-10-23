<?php
namespace Chamilo\Core\User\Ajax\Component;

use Chamilo\Core\User\Ajax\Manager;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Core\User\Storage\DataManager;
use Chamilo\Libraries\Architecture\JsonAjaxResult;
use Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\AdvancedElementFinderElement;
use Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\AdvancedElementFinderElements;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Storage\Service\SearchQueryConditionGenerator;
use Chamilo\Libraries\Translation\Translation;

/**
 * Feed to return users
 *
 * @package Chamilo\Core\User\Ajax
 * @author Sven Vanpoucke
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class UsersFeedComponent extends Manager
{
    const PARAM_OFFSET = 'offset';

    const PARAM_SEARCH_QUERY = 'query';

    const PROPERTY_ELEMENTS = 'elements';

    const PROPERTY_TOTAL_ELEMENTS = 'total_elements';

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
            $conditions[] = $this->getSearchQueryConditionGenerator()->getSearchConditions(
                $searchQuery, array(
                    new PropertyConditionVariable(User::class, User::PROPERTY_USERNAME),
                    new PropertyConditionVariable(User::class, User::PROPERTY_FIRSTNAME),
                    new PropertyConditionVariable(User::class, User::PROPERTY_LASTNAME),
                    new PropertyConditionVariable(User::class, User::PROPERTY_OFFICIAL_CODE)
                )
            );
        }

        // Only include active users
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(User::class, User::PROPERTY_ACTIVE), new StaticConditionVariable(1)
        );

        return new AndCondition($conditions);
    }

    /**
     * Returns the advanced element finder element for the given user
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @return \Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\AdvancedElementFinderElement
     */
    protected function getElementForUser(User $user)
    {
        $glyph = new FontAwesomeGlyph('user', array(), null, 'fas');

        return new AdvancedElementFinderElement(
            'user_' . $user->get_id(), $glyph->getClassNamesString(), $user->get_fullname(), $user->get_official_code()
        );
    }

    /**
     * Returns all the elements for this feed
     *
     * @return \Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\AdvancedElementFinderElements
     */
    protected function getElements()
    {
        $elements = new AdvancedElementFinderElements();

        $glyph = new FontAwesomeGlyph('folder', array(), null, 'fas');

        // Add user category
        $user_category = new AdvancedElementFinderElement(
            'users', $glyph->getClassNamesString(), Translation::get('Users'), Translation::get('Users')
        );
        $elements->add_element($user_category);

        $users = $this->retrieveUsers();

        if ($users)
        {
            foreach($users as $user)
            {
                $user_category->add_child($this->getElementForUser($user));
            }
        }

        return $elements;
    }

    /**
     * Returns the selected offset
     *
     * @return integer
     */
    protected function getOffset()
    {
        $offset = Request::post(self::PARAM_OFFSET);

        if (!isset($offset) || is_null($offset))
        {
            $offset = 0;
        }

        return $offset;
    }

    /**
     * @return \Chamilo\Libraries\Storage\Service\SearchQueryConditionGenerator
     */
    protected function getSearchQueryConditionGenerator()
    {
        return $this->getService(SearchQueryConditionGenerator::class);
    }

    /**
     * Retrieves the users
     *
     * @return \Chamilo\Libraries\Storage\ResultSet\ResultSet
     */
    public function retrieveUsers()
    {
        $condition = $this->getCondition();

        $this->userCount = DataManager::count(User::class, new DataClassCountParameters($condition));

        $parameters = new DataClassRetrievesParameters(
            $condition, 100, $this->getOffset(), array(
                new OrderBy(new PropertyConditionVariable(User::class, User::PROPERTY_LASTNAME)),
                new OrderBy(new PropertyConditionVariable(User::class, User::PROPERTY_FIRSTNAME)),
            )
        );

        return DataManager::retrieves(User::class, $parameters);
    }

    public function set_user_count($userCount)
    {
        $this->userCount = $userCount;
    }
}
