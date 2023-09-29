<?php
namespace Chamilo\Core\Group\Service;

use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Core\Group\Storage\Repository\GroupRepository;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\DataClass\PropertyMapper;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @package Chamilo\Core\Group\Service
 * @author  Sven Vanpoucke - Hogeschool Gent
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class GroupsTreeTraverser
{
    protected GroupMembershipService $groupMembershipService;

    protected GroupRepository $groupRepository;

    /**
     * @var int[][]
     */
    protected array $groupUserIdentifiers = [];

    /**
     * @var int[]
     */
    protected array $groupUsersCount = [];

    /**
     * @var int[][]
     */
    protected array $parentGroupIdentifiers = [];

    protected PropertyMapper $propertyMapper;

    /**
     * @var int[][]
     */
    protected array $subGroupIdentifiers = [];

    /**
     * @var \Chamilo\Core\Group\Storage\DataClass\Group[][]
     */
    protected array $subGroups = [];

    /**
     * @var int[]
     */
    protected array $subGroupsCount = [];

    /**
     * @var int[][]
     */
    protected array $userSubscribedGroupIdentifiers = [];

    /**
     * @var \Chamilo\Core\Group\Storage\DataClass\Group[][]
     */
    protected array $userSubscribedGroups = [];

    public function __construct(
        GroupRepository $groupRepository, GroupMembershipService $groupMembershipService, PropertyMapper $propertyMapper
    )
    {
        $this->groupRepository = $groupRepository;
        $this->groupMembershipService = $groupMembershipService;
        $this->propertyMapper = $propertyMapper;
    }

    public function countSubGroupsForGroup(Group $group, bool $recursiveSubgroups = false): int
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
            elseif ($recursiveSubgroups)
            {
                $this->subGroupsCount[$cacheKey] = ($group->getRightValue() - $group->getLeftValue() - 1) / 2;
            }
            else
            {
                $this->subGroupsCount[$cacheKey] = $this->groupRepository->countSubGroupsForGroup($group);
            }
        }

        return $this->subGroupsCount[$cacheKey];
    }

    public function countUsersForGroup(Group $group, bool $includeSubGroups = false, bool $recursiveSubgroups = false
    ): int
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
     * @return string[]
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function findAllSubscribedGroupIdentifiersForUserIdentifier(string $userIdentifier): array
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
     * @param string $userIdentifier
     *
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Group\Storage\DataClass\Group>
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function findAllSubscribedGroupsForUserIdentifier(string $userIdentifier): ArrayCollection
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
                $this->userSubscribedGroups[$userIdentifier] = new ArrayCollection([]);
            }
        }

        return $this->userSubscribedGroups[$userIdentifier];
    }

    /**
     * @param string $userIdentifier
     *
     * @return ArrayCollection<string[]>
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function findDirectlySubscribedGroupNestingValuesForUserIdentifier(string $userIdentifier): ArrayCollection
    {
        return $this->groupRepository->findDirectlySubscribedGroupNestingValuesForUserIdentifier($userIdentifier);
    }

    /**
     * @param string $userIdentifier
     *
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Group\Storage\DataClass\Group>
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function findDirectlySubscribedGroupsForUserIdentifier(string $userIdentifier): ArrayCollection
    {
        return $this->groupRepository->findDirectlySubscribedGroupsForUserIdentifier($userIdentifier);
    }

    /**
     * @return int[]
     */
    public function findParentGroupIdentifiersForGroup(Group $group, bool $includeSelf = true): array
    {
        $cacheKey = md5(serialize([$group->getId(), $includeSelf]));

        if (!array_key_exists($cacheKey, $this->parentGroupIdentifiers))
        {
            $this->parentGroupIdentifiers[$cacheKey] =
                $this->groupRepository->findParentGroupIdentifiersForGroup($group, $includeSelf);
        }

        return $this->parentGroupIdentifiers[$cacheKey];
    }

    /**
     * @param \Chamilo\Core\Group\Storage\DataClass\Group $group
     * @param bool $includeSelf
     *
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Group\Storage\DataClass\Group>
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function findParentGroupsForGroup(Group $group, bool $includeSelf = true): ArrayCollection
    {
        return $this->groupRepository->findParentGroupsForGroup($group, $includeSelf);
    }

    /**
     * @return string[]
     */
    public function findSubGroupIdentifiersForGroup(Group $group, bool $recursiveSubgroups = false): array
    {
        $cacheKey = md5(serialize([$group->getId(), $recursiveSubgroups]));

        if (!array_key_exists($cacheKey, $this->subGroupIdentifiers))
        {
            $this->subGroupIdentifiers[$cacheKey] =
                $this->groupRepository->findSubGroupIdentifiersForGroup($group, $recursiveSubgroups);
        }

        return $this->subGroupIdentifiers[$cacheKey];
    }

    /**
     * @return \Chamilo\Core\Group\Storage\DataClass\Group[]
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function findSubGroupsForGroup(Group $group, bool $recursiveSubgroups = false): array
    {
        $cacheKey = md5(serialize([$group->getId(), $recursiveSubgroups]));

        if (!array_key_exists($cacheKey, $this->subGroups))
        {
            $subGroups = $this->groupRepository->findSubGroupsForGroup($group, $recursiveSubgroups);

            $this->subGroups[$cacheKey] =
                $this->propertyMapper->mapDataClassByProperty($subGroups, DataClass::PROPERTY_ID);
        }

        return $this->subGroups[$cacheKey];
    }

    /**
     * @return string[]
     */
    public function findUserIdentifiersForGroup(
        Group $group, bool $includeSubGroups = false, bool $recursiveSubgroups = false
    ): array
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
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function getFullyQualifiedNameForGroup(Group $group, bool $includeSelf = true): string
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
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function getHighestGroupQuotumForUser(User $user): int
    {
        $userGroupIdentifiers = $this->findAllSubscribedGroupIdentifiersForUserIdentifier($user->getId());

        if (count($userGroupIdentifiers) == 0)
        {
            return 0;
        }

        return $this->groupRepository->getHighestGroupQuotumForUserGroupIdentifiers($userGroupIdentifiers);
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function getLowestGroupQuotumForUser(User $user): int
    {
        $userGroupIdentifiers = $this->findAllSubscribedGroupIdentifiersForUserIdentifier($user->getId());

        if (count($userGroupIdentifiers) == 0)
        {
            return 0;
        }

        return $this->groupRepository->getLowestGroupQuotumForUserGroupIdentifiers($userGroupIdentifiers);
    }
}