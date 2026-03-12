<?php
namespace Chamilo\Core\Group\Component;

use Chamilo\Core\Group\Architecture\Enum\ActionEnum;
use Chamilo\Core\Group\Service\GroupMembershipService;
use Chamilo\Core\Group\Service\GroupService;
use Chamilo\Core\Group\Service\GroupsTreeTraverser;
use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Core\User\Service\UserService;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Domain\ChamiloRequest;
use Chamilo\Libraries\Component\GroupsFeedComponent;
use Chamilo\Libraries\Service\Routing\UrlGenerator;
use Chamilo\Libraries\Storage\Architecture\Domain\NestedSet;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\Condition\ContainsCondition;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\Condition\OrCondition;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\ConditionVariable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\ConditionVariable\StaticConditionVariable;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\OrderBy;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\OrderProperty;
use Chamilo\Libraries\Storage\Service\SearchQueryConditionGenerator;
use Chamilo\Libraries\UserInterface\Form\Architecture\Domain\Element\AdvancedElementFinder\AdvancedElementFinderElement;
use Chamilo\Libraries\UserInterface\Glyph\Architecture\Domain\FontAwesomeGlyph;
use Chamilo\Libraries\UserInterface\Layout\Service\ApplicationHeaderRenderer;
use Chamilo\Libraries\UserInterface\Layout\Service\DefaultFooterRenderer;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Core\Group\Component
 * @author  Sven Vanpoucke
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class GroupFeedComponent extends GroupsFeedComponent
{
    public const int FILTER_PREFIX_LENGTH = 2;
    public const string PARAM_GROUP = 'group';
    public const string PARAM_USER = 'user';

    protected GroupMembershipService $groupMembershipService;

    protected GroupService $groupService;

    protected GroupsTreeTraverser $groupsTreeTraverser;

    public function __construct(
        ChamiloRequest $request, ApplicationHeaderRenderer $applicationHeaderRenderer,
        DefaultFooterRenderer $defaultFooterRenderer, Translator $translator, UserService $userService,
        UrlGenerator $urlGenerator, SearchQueryConditionGenerator $searchQueryConditionGenerator,
        GroupService $groupService, GroupsTreeTraverser $groupsTreeTraverser,
        GroupMembershipService $groupMembershipService
    )
    {
        parent::__construct(
            $request, $applicationHeaderRenderer, $defaultFooterRenderer, $translator, $userService, $urlGenerator,
            $searchQueryConditionGenerator
        );

        $this->groupService = $groupService;
        $this->groupsTreeTraverser = $groupsTreeTraverser;
        $this->groupMembershipService = $groupMembershipService;
    }

    public function getApplicationAction(): string
    {
        return ActionEnum::GROUP_FEED->value;
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function getGroupElement(Group $group): AdvancedElementFinderElement
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

    public function getGroupMembershipService(): GroupMembershipService
    {
        return $this->groupMembershipService;
    }

    public function getGroupService(): GroupService
    {
        return $this->groupService;
    }

    public function getGroupsTreeTraverser(): GroupsTreeTraverser
    {
        return $this->groupsTreeTraverser;
    }

    public function getUserElement(User $user): AdvancedElementFinderElement
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
    public function getUserIdentifiers(): array
    {
        $filterIdentifier = $this->get_filter();

        if (!$filterIdentifier) {
            return [];
        }

        return $this->getGroupMembershipService()->findSubscribedUserIdentifiersForGroupIdentifier($filterIdentifier);
    }

    protected function get_filter(): string
    {
        $filter = $this->getRequest()->request->get(self::PARAM_FILTER);

        return substr($filter, static::FILTER_PREFIX_LENGTH);
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Group\Storage\DataClass\Group>
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function retrieveGroups(): ArrayCollection
    {
        // Set the conditions for the search query
        $searchQuery = $this->getRequest()->request->get(self::PARAM_SEARCH_QUERY);
        if ($searchQuery && $searchQuery != '') {
            $nameConditions[] = new ContainsCondition(
                new PropertyConditionVariable(Group::class, Group::PROPERTY_NAME), $searchQuery
            );
            $nameConditions[] = new ContainsCondition(
                new PropertyConditionVariable(Group::class, Group::PROPERTY_CODE), $searchQuery
            );
            $conditions[] = new OrCondition($nameConditions);
        }

        $filterIdentifier = $this->get_filter();

        if ($filterIdentifier) {
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(Group::class, NestedSet::PROPERTY_PARENT_ID),
                new StaticConditionVariable($filterIdentifier)
            );
        }
        else {
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
