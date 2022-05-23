<?php
namespace Chamilo\Libraries\Ajax\Component;

use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Ajax\Manager;
use Chamilo\Libraries\Architecture\JsonAjaxResult;
use Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\AdvancedElementFinderElement;
use Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\AdvancedElementFinderElements;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\OrderProperty;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Service\SearchQueryConditionGenerator;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * Feed to return the course groups of this course
 *
 * @author Sven Vanpoucke
 * @package Chamilo\Libraries\Ajax\Component
 */
abstract class GroupsFeedComponent extends Manager
{
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
     * @throws \ReflectionException
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
     * @return \Chamilo\Libraries\Storage\Service\SearchQueryConditionGenerator
     */
    protected function getSearchQueryConditionGenerator()
    {
        return $this->getService(SearchQueryConditionGenerator::class);
    }

    /**
     * Returns all the elements for this feed
     *
     * @return \Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\AdvancedElementFinderElements
     * @throws \ReflectionException
     */
    private function get_elements()
    {
        $elements = new AdvancedElementFinderElements();
        $glyph = new FontAwesomeGlyph('folder', [], null, 'fas');

        // Add groups
        $groups = $this->retrieve_groups();
        if ($groups && $groups->count() > 0)
        {
            $translator = $this->getTranslator();
            // Add group category
            $group_category = new AdvancedElementFinderElement(
                'groups', $glyph->getClassNamesString(), $translator->trans('Groups', [], Utilities::COMMON_LIBRARIES),
                $translator->trans('Groups', [], Utilities::COMMON_LIBRARIES)
            );
            $elements->add_element($group_category);

            foreach ($groups as $group)
            {
                $group_category->add_child($this->get_group_element($group));
            }
        }

        // Add users
        /**
         * @var \Chamilo\Libraries\Storage\Iterator\DataClassIterator $users
         */
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
     *
     * @param \Chamilo\Core\Group\Storage\DataClass\Group $group
     *
     * @return \Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\AdvancedElementFinderElement
     */
    abstract public function get_group_element($group);

    /**
     *
     * @return integer
     */
    protected function get_offset()
    {
        $offset = $this->getRequest()->request->get(self::PARAM_OFFSET);
        if (!isset($offset) || is_null($offset))
        {
            $offset = 0;
        }

        return $offset;
    }

    /**
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @return \Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\AdvancedElementFinderElement
     */
    abstract public function get_user_element($user);

    /**
     *
     * @return integer[]
     */
    abstract public function get_user_ids();

    /**
     *
     * @return \Chamilo\Libraries\Storage\Iterator\DataClassIterator<\Chamilo\Core\Group\Storage\DataClass\Group>
     */
    abstract public function retrieve_groups();

    /**
     * @return array|\Chamilo\Core\User\Storage\DataClass\User[]
     */
    private function retrieve_users()
    {
        $conditions = [];

        $user_ids = $this->get_user_ids();

        if (count($user_ids) == 0)
        {
            return [];
        }

        $conditions[] = new InCondition(new PropertyConditionVariable(User::class, User::PROPERTY_ID), $user_ids);

        $search_query = $this->getRequest()->request->get(self::PARAM_SEARCH_QUERY);

        // Set the conditions for the search query
        if ($search_query && $search_query != '')
        {
            $conditions[] = $this->getSearchQueryConditionGenerator()->getSearchConditions(
                $search_query, array(
                    new PropertyConditionVariable(User::class, User::PROPERTY_USERNAME),
                    new PropertyConditionVariable(User::class, User::PROPERTY_FIRSTNAME),
                    new PropertyConditionVariable(User::class, User::PROPERTY_LASTNAME)
                )
            );
        }

        $condition = new AndCondition($conditions);

        $this->user_count = $this->getUserService()->countUsers($condition);

        return $this->getUserService()->findUsers(
            $condition, $this->get_offset(), 100, new OrderBy(array(
                new OrderProperty(new PropertyConditionVariable(User::class, User::PROPERTY_LASTNAME)),
                new OrderProperty(new PropertyConditionVariable(User::class, User::PROPERTY_FIRSTNAME))
            ))
        );
    }
}
