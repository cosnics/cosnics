<?php

namespace Chamilo\Core\Group\Service;

use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Core\Group\Storage\Repository\GroupRepository;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Storage\DataClass\PropertyMapper;
use Chamilo\Libraries\Storage\Iterator\DataClassCollection;

/**
 * @package Chamilo\Core\Group\Service
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class GroupsTreeTraverser
{
    /**
     * @var \Chamilo\Core\Group\Storage\Repository\GroupRepository
     */
    protected $groupRepository;

    /**
     * @var \Chamilo\Core\Group\Service\GroupMembershipService
     */
    protected $groupMembershipService;

    /**
     * @var \Chamilo\Libraries\Storage\DataClass\PropertyMapper
     */
    protected $propertyMapper;

    /**
     * @var \Chamilo\Core\Group\Storage\DataClass\Group[][]
     */
    protected $userSubscribedGroups = [];

    /**
     * @var integer[][]
     */
    protected $userSubscribedGroupIdentifiers = [];

    /**
     * @var integer[]
     */
    protected $subGroupsCount = [];

    /**
     * @var integer[][]
     */
    protected $groupUserIdentifiers = [];

    /**
     * @var integer[][]
     */
    protected $subGroupIdentifiers = [];

    /**
     * @var integer[][]
     */
    protected $parentGroupIdentifiers = [];

    /**
     * @var integer[]
     */
    protected $groupUsersCount = [];

    /**
     * @var \Chamilo\Core\Group\Storage\DataClass\Group[][]
     */
    protected $subGroups = [];

    /**
     * @param \Chamilo\Core\Group\Storage\Repository\GroupRepository $groupRepository
     * @param \Chamilo\Core\Group\Service\GroupMembershipService $groupMembershipService
     * @param \Chamilo\Libraries\Storage\DataClass\PropertyMapper $propertyMapper
     */
    public function __construct(
        GroupRepository $groupRepository, GroupMembershipService $groupMembershipService, PropertyMapper $propertyMapper
    )
    {
        $this->groupRepository = $groupRepository;
        $this->groupMembershipService = $groupMembershipService;
        $this->propertyMapper = $propertyMapper;
    }

    /**
     * @param \Chamilo\Core\Group\Storage\DataClass\Group $group
     * @param boolean $recursiveSubgroups
     *
     * @return integer
     */
    public function countSubGroupsForGroup(Group $group, bool $recursiveSubgroups = false)
    {
        $cacheKey = md5(serialize([$group->getId(), $recursiveSubgroups]));

        if (!array_key_exists($cacheKey, $this->subGroupsCount))
        {
            if ($group->getRightValue() == $group->getLeftValue() + 1)
            {
                $this->subGroupsCount[$cacheKey] = 0;
            }
            elseif ($group->getRightValue() == $group->getLeftValue() + 3)
            {
                $this->subGroupsCount[$cacheKey] = 1;
            }
            else
            {
                if ($recursiveSubgroups)
                {
                    $this->subGroupsCount[$cacheKey] = ($group->getRightValue() - $group->getLeftValue() - 1) / 2;
                }
                else
                {

                    $this->subGroupsCount[$cacheKey] = $this->groupRepository->countSubGroupsForGroup($group, false);
                }
            }
        }

        return $this->subGroupsCount[$cacheKey];
    }

    /**
     * @param \Chamilo\Core\Group\Storage\DataClass\Group $group
     * @param boolean $includeSubGroups
     * @param boolean $recursiveSubgroups
     *
     * @return integer
     * @throws \Exception
     */
    public function countUsersForGroup(Group $group, bool $includeSubGroups = false, bool $recursiveSubgroups = false)
    {
        $cacheKey = md5(serialize([$group->getId(), $includeSubGroups, $recursiveSubgroups]));

        if (!array_key_exists($cacheKey, $this->groupUsersCount))
        {
            if ($includeSubGroups)
            {
                $groupIdentifiers = $this->findSubGroupIdentifiersForGroup($group, $recursiveSubgroups);
            }
            else
            {
                $groupIdentifiers = [];
            }

            $groupIdentifiers[] = $group->getId();

            $this->groupUsersCount[$cacheKey] =
                $this->groupMembershipService->countSubscribedUsersForGroupIdentifiers($groupIdentifiers);
        }

        return $this->groupUsersCount[$cacheKey];
    }

    /**
     * @param integer $userIdentifier
     *
     * @return integer[]
     * @throws \Exception
     */
    public function findAllSubscribedGroupIdentifiersForUserIdentifier(int $userIdentifier)
    {
        if (!array_key_exists($userIdentifier, $this->userSubscribedGroupIdentifiers))
        {
            $directlySubscribedGroupNestingValues =
                $this->findDirectlySubscribedGroupNestingValuesForUserIdentifier($userIdentifier);

            if (count($directlySubscribedGroupNestingValues) > 0)
            {
                $this->userSubscribedGroupIdentifiers[$userIdentifier] =
                    $this->groupRepository->findGroupIdentifiersForDirectlySubscribedGroupNestingValues(
                        $directlySubscribedGroupNestingValues
                    );
            }
            else
            {
                $this->userSubscribedGroupIdentifiers[$userIdentifier] = [];
            }
        }

        return $this->userSubscribedGroupIdentifiers[$userIdentifier];
    }

    /**
     * @param integer $userIdentifier
     *
     * @return \Chamilo\Core\Group\Storage\DataClass\Group[]|DataClassCollection
     *
     * @throws \Exception
     */
    public function findAllSubscribedGroupsForUserIdentifier(int $userIdentifier)
    {
        if (!array_key_exists($userIdentifier, $this->userSubscribedGroups))
        {
            $directlySubscribedGroupNestingValues =
                $this->findDirectlySubscribedGroupNestingValuesForUserIdentifier($userIdentifier);

            if (count($directlySubscribedGroupNestingValues) > 0)
            {
                $this->userSubscribedGroups[$userIdentifier] =
                    $this->groupRepository->findGroupsForDirectlySubscribedGroupNestingValues(
                        $directlySubscribedGroupNestingValues
                    );
            }
            else
            {
                $this->userSubscribedGroups[$userIdentifier] = new DataClassCollection([]);
            }
        }

        return $this->userSubscribedGroups[$userIdentifier];
    }

    /**
     * @param integer $userIdentifier
     *
     * @return string[][]|DataClassCollection
     * @throws \Exception
     */
    public function findDirectlySubscribedGroupNestingValuesForUserIdentifier(int $userIdentifier)
    {
        return $this->groupRepository->findDirectlySubscribedGroupNestingValuesForUserIdentifier($userIdentifier);
    }

    /**
     * @param \Chamilo\Core\Group\Storage\DataClass\Group $group
     * @param bool $includeSelf
     *
     * @return integer[]
     * @throws \Exception
     */
    public function findParentGroupIdentifiersForGroup(Group $group, bool $includeSelf = true)
    {
        $cacheKey = md5(serialize([$group->getId(), $includeSelf]));
        if (!array_key_exists($cacheKey, $this->parentGroupIdentifiers))
        {
            $parentGroupIdentifiers = $this->groupRepository->findParentGroupIdentifiersForGroup($group, $includeSelf);

            if (!is_array($parentGroupIdentifiers))
            {
                $parentGroupIdentifiers = [];
            }

            $this->parentGroupIdentifiers[$cacheKey] = $parentGroupIdentifiers;
        }

        return $this->parentGroupIdentifiers[$cacheKey];
    }

    /**
     * @param \Chamilo\Core\Group\Storage\DataClass\Group $group
     * @param boolean $includeSelf
     *
     * @return \Chamilo\Core\Group\Storage\DataClass\Group[]|DataClassCollection
     */
    public function findParentGroupsForGroup(Group $group, bool $includeSelf = true)
    {
        return $this->groupRepository->findParentGroupsForGroup($group, $includeSelf);
    }

    /**
     * @param \Chamilo\Core\Group\Storage\DataClass\Group $group
     * @param boolean $recursiveSubgroups
     *
     * @return integer[]
     * @throws \Exception
     */
    public function findSubGroupIdentifiersForGroup(Group $group, bool $recursiveSubgroups = false)
    {
        $cacheKey = md5(serialize([$group->getId(), $recursiveSubgroups]));

        if (!array_key_exists($cacheKey, $this->subGroupIdentifiers))
        {
            $subGroupIdentifiers = $this->groupRepository->findSubGroupIdentifiersForGroup($group, $recursiveSubgroups);

            if (!is_array($subGroupIdentifiers))
            {
                $subGroupIdentifiers = [];
            }

            $this->subGroupIdentifiers[$cacheKey] = $subGroupIdentifiers;
        }

        return $this->subGroupIdentifiers[$cacheKey];
    }

    /**
     * @param \Chamilo\Core\Group\Storage\DataClass\Group $group
     * @param boolean $recursiveSubgroups
     *
     * @return \Chamilo\Core\Group\Storage\DataClass\Group[]|DataClassCollection
     */
    public function findSubGroupsForGroup(Group $group, bool $recursiveSubgroups = false)
    {
        $cacheKey = md5(serialize([$group->getId(), $recursiveSubgroups]));

        if (!array_key_exists($cacheKey, $this->subGroups))
        {
            $subGroups = $this->groupRepository->findSubGroupsForGroup($group, $recursiveSubgroups);

            $this->subGroups[$cacheKey] = $this->propertyMapper->mapDataClassByProperty($subGroups, Group::PROPERTY_ID);
        }

        return $this->subGroups[$cacheKey];
    }

    /**
     * @param \Chamilo\Core\Group\Storage\DataClass\Group $group
     * @param boolean $includeSubGroups
     * @param boolean $recursiveSubgroups
     *
     * @return integer[]
     * @throws \Exception
     */
    public function findUserIdentifiersForGroup(
        Group $group, bool $includeSubGroups = false, bool $recursiveSubgroups = false
    )
    {
        $cacheKey = md5(serialize([$group->getId(), $includeSubGroups, $recursiveSubgroups]));

        if (!array_key_exists($cacheKey, $this->groupUserIdentifiers))
        {
            if ($includeSubGroups)
            {
                $groupIdentifiers = $this->findSubGroupIdentifiersForGroup($group, $recursiveSubgroups);
            }
            else
            {
                $groupIdentifiers = [];
            }

            $groupIdentifiers[] = $group->getId();

            $this->groupUserIdentifiers[$cacheKey] =
                $this->groupMembershipService->findSubscribedUserIdentifiersForGroupIdentifiers($groupIdentifiers);
        }

        return $this->groupUserIdentifiers[$cacheKey];
    }

    /**
     * @param \Chamilo\Core\Group\Storage\DataClass\Group $group
     * @param boolean $includeSelf
     *
     * @return string
     */
    public function getFullyQualifiedNameForGroup(Group $group, bool $includeSelf = true)
    {
        $parentGroups = $this->findParentGroupsForGroup($group, $includeSelf);

        $names = [];

        foreach ($parentGroups as $parentGroup)
        {
            $names[] = $parentGroup->get_name();
        }

        return implode(' <span class="text-primary">></span> ', array_reverse($names));
    }

    /**
     * @param \Chamilo\Core\Group\Storage\DataClass\Group $group
     *
     * @return string
     * @todo This should be rewritten when implementing the new NestedSetDataClassRepository for Group objects
     *
     */
    public function getGroupPath(Group $group)
    {
        return $group->get_fully_qualified_name();
    }

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @return integer
     * @throws \Exception
     */
    public function getHighestGroupQuotumForUser(User $user)
    {
        $userGroupIdentifiers = $this->findAllSubscribedGroupIdentifiersForUserIdentifier($user->getId());

        if (count($userGroupIdentifiers) == 0)
        {
            return 0;
        }

        return $this->groupRepository->getHighestGroupQuotumForUserGroupIdentifiers($userGroupIdentifiers);
    }

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @return integer
     * @throws \Exception
     */
    public function getLowestGroupQuotumForUser(User $user)
    {
        $userGroupIdentifiers = $this->findAllSubscribedGroupIdentifiersForUserIdentifier($user->getId());

        if (count($userGroupIdentifiers) == 0)
        {
            return 0;
        }

        return $this->groupRepository->getLowestGroupQuotumForUserGroupIdentifiers($userGroupIdentifiers);
    }
}