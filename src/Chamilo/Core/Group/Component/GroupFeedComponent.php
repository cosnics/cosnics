<?php
namespace Chamilo\Core\Group\Component;

use Chamilo\Core\Group\Service\GroupMembershipService;
use Chamilo\Core\Group\Service\GroupsTreeTraverser;
use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Ajax\Component\GroupsFeedComponent;
use Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\AdvancedElementFinderElement;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Storage\DataClass\NestedSet;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\ContainsCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\OrCondition;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\OrderProperty;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @package Chamilo\Core\Group\Component
 * @author  Sven Vanpoucke
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class GroupFeedComponent extends GroupsFeedComponent
{
    public const FILTER_PREFIX_LENGTH = 2;

    public const PARAM_GROUP = 'group';
    public const PARAM_USER = 'user';

    public function getGroupMembershipService(): GroupMembershipService
    {
        return $this->getService(GroupMembershipService::class);
    }

    public function getGroupsTreeTraverser(): GroupsTreeTraverser
    {
        return $this->getService(GroupsTreeTraverser::class);
    }

    public function getRequiredPostParameters(array $postParameters = []): array
    {
        return [];
    }

    protected function get_filter(): string
    {
        $filter = $this->getRequest()->request->get(self::PARAM_FILTER);

        return substr($filter, static::FILTER_PREFIX_LENGTH);
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function get_group_element(Group $group): AdvancedElementFinderElement
    {
        $description = strip_tags(
            $this->getGroupsTreeTraverser()->getFullyQualifiedNameForGroup($group) . ' [' . $group->getCode() . ']'
        );
        $glyph = new FontAwesomeGlyph('users', [], null, 'fas');

        return new AdvancedElementFinderElement(
            self::PARAM_GROUP . '_' . $group->getId(), $glyph->getClassNamesString(), $group->getName(), $description,
            AdvancedElementFinderElement::TYPE_SELECTABLE_AND_FILTER
        );
    }

    public function get_user_element(User $user): AdvancedElementFinderElement
    {
        $glyph = new FontAwesomeGlyph('user', [], null, 'fas');

        return new AdvancedElementFinderElement(
            self::PARAM_USER . '_' . $user->getId(), $glyph->getClassNamesString(), $user->getFullName(),
            $user->getOfficialCode()
        );
    }

    /**
     * @return string[]
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function get_user_ids(): array
    {
        $filter_id = $this->get_filter();

        if (!$filter_id)
        {
            return [];
        }

        return $this->getGroupMembershipService()->findSubscribedUserIdentifiersForGroupIdentifier($filter_id);
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Group\Storage\DataClass\Group>
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function retrieve_groups(): ArrayCollection
    {
        // Set the conditions for the search query
        $search_query = $this->getRequest()->request->get(self::PARAM_SEARCH_QUERY);
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
                new PropertyConditionVariable(Group::class, NestedSet::PROPERTY_PARENT_ID),
                new StaticConditionVariable($filter_id)
            );
        }
        else
        {
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(Group::class, NestedSet::PROPERTY_PARENT_ID),
                new StaticConditionVariable(0)
            );
        }

        $condition = new AndCondition($conditions);

        return $this->getGroupService()->findGroups(
            condition: $condition, orderBy: new OrderBy(
            [new OrderProperty(new PropertyConditionVariable(Group::class, Group::PROPERTY_NAME))]
        )
        );
    }
}
