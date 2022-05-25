<?php
namespace Chamilo\Core\Group\Ajax\Component;

use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Core\Group\Storage\DataClass\GroupRelUser;
use Chamilo\Core\Group\Storage\DataManager;
use Chamilo\Libraries\Ajax\Component\GroupsFeedComponent;
use Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\AdvancedElementFinderElement;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\ContainsCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\OrCondition;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\OrderProperty;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 * Feed to return the platform groups
 *
 * @author Sven Vanpoucke
 * @package application.weblcms
 */
class PlatformGroupsFeedComponent extends GroupsFeedComponent
{
    /**
     * The length for the filter prefix to remove
     */
    const FILTER_PREFIX_LENGTH = 2;
    const PARAM_GROUP = 'group';
    const PARAM_USER = 'user';

    public function getRequiredPostParameters()
    {
        return [];
    }

    /**
     * Returns the id of the selected filter
     */
    protected function get_filter()
    {
        $filter = Request::post(self::PARAM_FILTER);

        return substr($filter, static::FILTER_PREFIX_LENGTH);
    }

    /**
     * Returns the element for a specific group
     *
     * @param \core\group\Group $group
     *
     * @return AdvancedElementFinderElement
     */
    public function get_group_element($group)
    {
        $description = strip_tags($group->get_fully_qualified_name() . ' [' . $group->get_code() . ']');
        $glyph = new FontAwesomeGlyph('users', [], null, 'fas');

        return new AdvancedElementFinderElement(
            self::PARAM_GROUP . '_' . $group->get_id(), $glyph->getClassNamesString(), $group->get_name(), $description,
            AdvancedElementFinderElement::TYPE_SELECTABLE_AND_FILTER
        );
    }

    /**
     * Returns the element for a specific user
     *
     * @param \core\user\storage\data_class\User $user
     *
     * @return AdvancedElementFinderElement
     */
    public function get_user_element($user)
    {
        $glyph = new FontAwesomeGlyph('user', [], null, 'fas');

        return new AdvancedElementFinderElement(
            self::PARAM_USER . '_' . $user->get_id(), $glyph->getClassNamesString(), $user->get_fullname(),
            $user->get_official_code()
        );
    }

    /**
     * Retrieves all the users for the selected group
     */
    public function get_user_ids()
    {
        $filter_id = $this->get_filter();

        if (!$filter_id)
        {
            return;
        }

        $condition = new EqualityCondition(
            new PropertyConditionVariable(GroupRelUser::class, GroupRelUser::PROPERTY_GROUP_ID),
            new StaticConditionVariable($filter_id)
        );
        $relations = DataManager::retrieves(GroupRelUser::class, new DataClassRetrievesParameters($condition));

        $user_ids = [];

        foreach ($relations as $relation)
        {
            $user_ids[] = $relation->get_user_id();
        }

        return $user_ids;
    }

    /**
     * Returns all the groups for this feed
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function retrieve_groups()
    {
        // Set the conditions for the search query
        $search_query = Request::post(self::PARAM_SEARCH_QUERY);
        if ($search_query && $search_query != '')
        {
            $name_conditions[] = new ContainsCondition(
                new PropertyConditionVariable(Group::class, Group::PROPERTY_NAME), $search_query
            );
            $name_conditions[] = new ContainsCondition(
                new PropertyConditionVariable(Group::class, Group::PROPERTY_CODE), $search_query
            );
            $conditions[] = new OrCondition($name_conditions);
        }

        $filter_id = $this->get_filter();

        if ($filter_id)
        {
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(Group::class, Group::PROPERTY_PARENT_ID),
                new StaticConditionVariable($filter_id)
            );
        }
        else
        {
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(Group::class, Group::PROPERTY_PARENT_ID), new StaticConditionVariable(0)
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
            Group::class, new DataClassRetrievesParameters(
                $condition, null, null,
                new OrderBy(array(new OrderProperty(new PropertyConditionVariable(Group::class, Group::PROPERTY_NAME))))
            )
        );
    }
}
