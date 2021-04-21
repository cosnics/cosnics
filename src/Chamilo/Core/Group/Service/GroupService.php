<?php

namespace Chamilo\Core\Group\Service;

use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Core\Group\Storage\DataClass\GroupRelUser;
use Chamilo\Core\Group\Storage\Repository\GroupRepository;
use Chamilo\Core\User\Storage\DataClass\User;

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

    /**
     * GroupService constructor.
     *
     * @param \Chamilo\Core\Group\Storage\Repository\GroupRepository $groupRepository
     */
    public function __construct(\Chamilo\Core\Group\Storage\Repository\GroupRepository $groupRepository)
    {
        $this->groupRepository = $groupRepository;
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
     * @param int $groupIdentifier
     *
     * @return Group
     */
    public function getGroupByIdentifier($groupIdentifier)
    {
        $group = $this->groupRepository->findGroupByIdentifier($groupIdentifier);

        if (!$group instanceof Group)
        {
            throw new \RuntimeException('Could not find the group with identifier ' . $groupIdentifier);
        }

        return $group;
    }

    /**
     * @param array $groupIds
     *
     * @return Group[]
     */
    public function findGroupsByIds($groupIds = [])
    {
        return $this->groupRepository->findGroupsByIds($groupIds);
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
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     */
    public function subscribeUserToGroup(Group $group, User $user)
    {
        $groupRelation = $this->groupRepository->findGroupRelUserByGroupAndUserId($group->getId(), $user->getId());

        if (!$groupRelation instanceof GroupRelUser)
        {
            $this->subscribeUserToGroupBatchOperation($group->getId(), $user->getId());
        }
    }

    /**
     * Subscribes a user to a group without a subscription check. Only use this in batch operations where
     * you know for sure that the subscription doesn't need to be checked
     *
     * @param int $groupId
     * @param int $userId
     */
    public function subscribeUserToGroupBatchOperation(int $groupId, int $userId)
    {
        $groupRelation = new GroupRelUser();
        $groupRelation->set_user_id($userId);
        $groupRelation->set_group_id($groupId);

        if (!$this->groupRepository->create($groupRelation))
        {
            throw new \RuntimeException(
                sprintf('Could not subscribe the user %s to the group %s', $userId, $groupId)
            );
        }
    }

    /**
     * @param \Chamilo\Core\Group\Storage\DataClass\Group $group
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     */
    public function removeUserFromGroup(Group $group, User $user)
    {
        $groupRelation = $this->groupRepository->findGroupRelUserByGroupAndUserId($group->getId(), $user->getId());

        if ($groupRelation instanceof GroupRelUser)
        {
            $this->removeUserFromGroupBatchOperation($group->getId(), $user->getId());
        }
    }

    /**
     * @param int $groupId
     * @param int $userId
     */
    public function removeUserFromGroupBatchOperation(int $groupId, int $userId)
    {
        if(!$this->groupRepository->removeUserFromGroup($groupId, $userId))
        {
            throw new \RuntimeException(
                sprintf('Could not remove the user %s to the group %s', $userId, $groupId)
            );
        }
    }

    /**
     * @param \Chamilo\Core\Group\Storage\DataClass\Group $group
     *
     * @return Group[]
     */
    public function findDirectChildrenFromGroup(Group $group)
    {
        return $this->groupRepository->findDirectChildrenFromGroup($group);
    }

    /**
     * @param string $name
     * @param string $code
     * @param int $parentGroupId
     *
     * @return Group
     */
    public function createGroup(string $name, string $code, int $parentGroupId = 0)
    {
        $group = new Group();
        $group->set_name($name);
        $group->set_code($code);
        $group->set_parent_id($parentGroupId);

        if(!$this->groupRepository->create($group))
        {
            throw new \RuntimeException(
                sprintf('Could not create a group with name %s and code %s', $name, $code)
            );
        }

        return $group;
    }
}
