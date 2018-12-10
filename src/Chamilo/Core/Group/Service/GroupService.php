<?php

namespace Chamilo\Core\Group\Service;

use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Core\Group\Storage\DataClass\GroupRelUser;
use Chamilo\Core\Group\Storage\Repository\GroupRepository;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Storage\Iterator\DataClassIterator;
use Chamilo\Libraries\Storage\Query\Condition\Condition;

/**
 * Service to manage the groups of Chamilo
 *
 * @package Chamilo\Core\Group\Service
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class GroupService
{
    /**
     * @var GroupRepository
     */
    protected $groupRepository;

    private $userSubscribedGroups = array();

    private $userSubscribedGroupIdentifiers = array();

    /**
     * GroupService constructor.
     *
     * @param \Chamilo\Core\Group\Storage\Repository\GroupRepository $groupRepository
     */
    public function __construct(GroupRepository $groupRepository)
    {
        $this->groupRepository = $groupRepository;
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
     * @param integer $groupIdentifier
     *
     * @return integer[]
     * @throws \Exception
     */
    public function findSubscribedUserIdentifiersForGroupIdentifier(int $groupIdentifier)
    {
        return $this->getGroupRepository()->findSubscribedUserIdentifiersForGroupIdentifier($groupIdentifier);
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
     * @param \Chamilo\Core\Group\Storage\DataClass\Group $group
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     */
    public function subscribeUserToGroup(Group $group, User $user)
    {
        $groupRelation = $this->groupRepository->findGroupRelUserByGroupAndUserId($group->getId(), $user->getId());

        if (!$groupRelation instanceof GroupRelUser)
        {
            $groupRelation = new GroupRelUser();
            $groupRelation->set_user_id($user->getId());
            $groupRelation->set_group_id($group->getId());

            if (!$this->groupRepository->create($groupRelation))
            {
                throw new \RuntimeException(
                    sprintf('Could not subscribe the user %s to the group %s', $user->getId(), $group->getId())
                );
            }
        }
    }

    /**
     * @param string $groupCode
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     */
    public function subscribeUserToGroupByCode($groupCode, User $user)
    {
        $group = $this->findGroupByCode($groupCode);
        $this->subscribeUserToGroup($group, $user);
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
}