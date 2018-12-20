<?php

namespace Chamilo\Core\Group\Service;

use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Core\Group\Storage\Repository\GroupRepository;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Storage\DataClass\PropertyMapper;
use Chamilo\Libraries\Storage\Iterator\DataClassIterator;
use Chamilo\Libraries\Storage\Query\Condition\Condition;

/**
 * Service to manage the groups of Chamilo
 *
 * @package Chamilo\Core\Group\Service
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class GroupService
{
    /**
     * @var \Chamilo\Core\Group\Storage\Repository\GroupRepository
     */
    private $groupRepository;

    /**
     * @var \Chamilo\Core\Group\Service\GroupMembershipService
     */
    private $groupMembershipService;

    /**
     * @var \Chamilo\Core\Group\Storage\DataClass\Group[][]
     */
    private $userSubscribedGroups = array();

    /**
     * @var integer[][]
     */
    private $userSubscribedGroupIdentifiers = array();

    /**
     * @var \Chamilo\Libraries\Storage\DataClass\PropertyMapper
     */
    private $propertyMapper;

    /**
     * @var integer[][]
     */
    private $groupUserIdentifiers = array();

    /**
     * @var integer[][]
     */
    private $subGroupIdentifiers = array();

    /**
     * @var integer[]
     */
    private $subGroupsCount = array();

    /**
     * @var integer[]
     */
    private $groupUsersCount = array();

    /**
     * @var \Chamilo\Core\Group\Storage\DataClass\Group[][]
     */
    private $subGroups = array();

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
     *
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     *
     * @return integer
     */
    public function countGroups(Condition $condition = null)
    {
        return $this->getGroupRepository()->countGroups($condition);
    }

    /**
     * @param \Chamilo\Core\Group\Storage\DataClass\Group $group
     *
     * @return boolean
     * @throws \Exception
     */
    public function createGroup(Group $group)
    {
        return $this->getGroupRepository()->createGroup($group);
    }

    /**
     * @param \Chamilo\Core\Group\Storage\DataClass\Group $group
     *
     * @return boolean
     * @throws \Exception
     */
    public function deleteGroup(Group $group)
    {
        $deletedGroups = $this->getGroupRepository()->deleteGroup($group);

        if (!$deletedGroups instanceof DataClassIterator)
        {
            return false;
        }

        if (!$this->getGroupMembershipService()->unsubscribeUsersFromGroups($deletedGroups))
        {
            return false;
        }

        return true;
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
                    $this->getGroupRepository()->findGroupIdentifiersForDirectlySubscribedGroupNestingValues(
                        $directlySubscribedGroupNestingValues
                    );
            }
            else
            {
                $this->userSubscribedGroupIdentifiers[$userIdentifier] = array();
            }
        }

        return $this->userSubscribedGroupIdentifiers[$userIdentifier];
    }

    /**
     * @param integer $userIdentifier
     *
     * @return \Chamilo\Core\Group\Storage\DataClass\Group[]
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
                    $this->getGroupRepository()->findGroupsForDirectlySubscribedGroupNestingValues(
                        $directlySubscribedGroupNestingValues
                    );
            }
            else
            {
                $this->userSubscribedGroups[$userIdentifier] = new DataClassIterator(Group::class, []);
            }
        }

        return $this->userSubscribedGroups[$userIdentifier];
    }

    /**
     * @param integer $userIdentifier
     *
     * @return string[][]
     * @throws \Exception
     */
    public function findDirectlySubscribedGroupNestingValuesForUserIdentifier(int $userIdentifier)
    {
        return $this->getGroupRepository()->findDirectlySubscribedGroupNestingValuesForUserIdentifier($userIdentifier);
    }

    /**
     * @param string $groupCode
     *
     * @return \Chamilo\Core\Group\Storage\DataClass\Group|\Chamilo\Libraries\Storage\DataClass\DataClass
     */
    public function findGroupByCode($groupCode)
    {
        if (empty($groupCode))
        {
            throw new \InvalidArgumentException('The given groupcode can not be empty');
        }

        $group = $this->groupRepository->findGroupByCode($groupCode);

        if (!$group instanceof Group)
        {
            throw new \RuntimeException('Could not find the group with groupcode ' . $groupCode);
        }

        return $group;
    }

    /**
     * @param integer $groupIdentifier
     *
     * @return \Chamilo\Core\Group\Storage\DataClass\Group
     */
    public function findGroupByIdentifier($groupIdentifier)
    {
        $group = $this->groupRepository->findGroupByIdentifier($groupIdentifier);

        if (!$group instanceof Group)
        {
            throw new \RuntimeException('Could not find the group with identifier ' . $groupIdentifier);
        }

        return $group;
    }

    /**
     *
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     * @param integer $offset
     * @param integer $count
     * @param \Chamilo\Libraries\Storage\Query\OrderBy[] $orderProperty
     *
     * @return \Chamilo\Core\Group\Storage\DataClass\Group[]
     */
    public function findGroups($condition, $offset = 0, $count = - 1, $orderProperty = null)
    {
        return $this->getGroupRepository()->findGroups($condition, $count, $offset, $orderProperty);
    }

    /**
     *
     * @param integer[] $groupIdentifiers
     *
     * @return \Chamilo\Core\Group\Storage\DataClass\Group[]
     */
    public function findGroupsByIdentifiers($groupIdentifiers)
    {
        if (empty($groupIdentifiers))
        {
            return [];
        }

        return $this->groupRepository->findGroupsByIdentifiersOrderedByName($groupIdentifiers);
    }

    /**
     * @param string $searchQuery
     * @param integer $parentIdentifier
     *
     * @return \Chamilo\Core\Group\Storage\DataClass\Group[]
     */
    public function findGroupsForSearchQueryAndParentIdentifier(string $searchQuery = null, int $parentIdentifier = 0)
    {
        return $this->getGroupRepository()->findGroupsForSearchQueryAndParentIdentifier(
            $searchQuery, $parentIdentifier
        );
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
            $subGroupIdentifiers =
                $this->getGroupRepository()->findSubGroupIdentifiersForGroup($group, $recursiveSubgroups);

            if (!is_array($subGroupIdentifiers))
            {
                $subGroupIdentifiers = array();
            }

            $this->subGroupIdentifiers[$cacheKey] = $subGroupIdentifiers;
        }

        return $this->subGroupIdentifiers[$cacheKey];
    }

    /**
     * @param \Chamilo\Core\Group\Storage\DataClass\Group $group
     * @param boolean $recursiveSubgroups
     *
     * @return \Chamilo\Core\Group\Storage\DataClass\Group[]
     */
    public function findSubGroupsForGroup(Group $group, bool $recursiveSubgroups = false)
    {
        $cacheKey = md5(serialize([$group->getId(), $recursiveSubgroups]));

        if (!array_key_exists($cacheKey, $this->subGroups))
        {
            $subGroups = $this->getGroupRepository()->findSubGroupsForGroup($group, $recursiveSubgroups);

            $this->subGroups[$cacheKey] =
                $this->getPropertyMapper()->mapDataClassByProperty($subGroups, Group::PROPERTY_ID);
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
                $groupIdentifiers = array();
            }

            $groupIdentifiers[] = $group->getId();

            $this->groupUserIdentifiers[$cacheKey] =
                $this->getGroupMembershipService()->findSubscribedUserIdentifiersForGroupIdentifiers($groupIdentifiers);
        }

        return $this->groupUserIdentifiers[$cacheKey];
    }

    /**
     * @param integer $groupIdentifier
     *
     * @return \Chamilo\Core\Group\Storage\DataClass\Group
     * @deprecated Use GroupService::findGroupByIdentifier() now
     */
    public function getGroupByIdentifier($groupIdentifier)
    {
        return $this->findGroupByIdentifier($groupIdentifier);
    }

    /**
     * @return \Chamilo\Core\Group\Service\GroupMembershipService
     */
    public function getGroupMembershipService(): GroupMembershipService
    {
        return $this->groupMembershipService;
    }

    /**
     * @param \Chamilo\Core\Group\Service\GroupMembershipService $groupMembershipService
     */
    public function setGroupMembershipService(GroupMembershipService $groupMembershipService): void
    {
        $this->groupMembershipService = $groupMembershipService;
    }

    /**
     * @param \Chamilo\Core\Group\Storage\DataClass\Group $group
     *
     * @return string
     * @todo This should be rewritten when implementing the new NestedSetDataClassRepository for Group objects
     */
    public function getGroupPath(Group $group)
    {
        return $group->get_fully_qualified_name();
    }

    /**
     * @return \Chamilo\Core\Group\Storage\Repository\GroupRepository
     */
    public function getGroupRepository(): GroupRepository
    {
        return $this->groupRepository;
    }

    /**
     * @param \Chamilo\Core\Group\Storage\Repository\GroupRepository $groupRepository
     */
    public function setGroupRepository(GroupRepository $groupRepository): void
    {
        $this->groupRepository = $groupRepository;
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

        return $this->getGroupRepository()->getHighestGroupQuotumForUserGroupIdentifiers($userGroupIdentifiers);
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

        return $this->getGroupRepository()->getLowestGroupQuotumForUserGroupIdentifiers($userGroupIdentifiers);
    }

    /**
     * @return \Chamilo\Libraries\Storage\DataClass\PropertyMapper
     */
    public function getPropertyMapper(): PropertyMapper
    {
        return $this->propertyMapper;
    }

    /**
     * @param \Chamilo\Libraries\Storage\DataClass\PropertyMapper $propertyMapper
     */
    public function setPropertyMapper(PropertyMapper $propertyMapper): void
    {
        $this->propertyMapper = $propertyMapper;
    }

    /**
     * @param \Chamilo\Core\Group\Storage\DataClass\Group $group
     * @param integer $parentGroupIdentifier
     *
     * @return boolean
     * @throws \Exception
     */
    public function moveGroup(Group $group, int $parentGroupIdentifier)
    {
        return $this->getGroupRepository()->moveGroup($group, $parentGroupIdentifier);
    }

    /**
     * @param string $groupCode
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     */
    public function subscribeUserToGroupByCode($groupCode, User $user)
    {
        $group = $this->findGroupByCode($groupCode);
        $this->getGroupMembershipService()->subscribeUserToGroup($group, $user);
    }

    /**
     * @param \Chamilo\Core\Group\Storage\DataClass\Group $group
     *
     * @return boolean
     */
    public function updateGroup(Group $group)
    {
        return $this->getGroupRepository()->updateGroup($group);
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

                    $this->subGroupsCount[$cacheKey] =
                        $this->getGroupRepository()->countSubGroupsForGroup($group, false);
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
                $groupIdentifiers = array();
            }

            $groupIdentifiers[] = $group->getId();

            $this->groupUsersCount[$cacheKey] =
                $this->getGroupMembershipService()->countSubscribedUsersForGroupIdentifiers($groupIdentifiers);
        }

        return $this->groupUsersCount[$cacheKey];
    }
}