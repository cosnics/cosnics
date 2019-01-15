<?php

namespace Chamilo\Core\Group\Service;

use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Core\Group\Storage\DataClass\GroupRelUser;
use Chamilo\Core\Group\Storage\Repository\GroupMembershipRepository;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Storage\Iterator\DataClassIterator;

/**
 * @package Chamilo\Core\Group\Service
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class GroupMembershipService
{
    /**
     * @var \Chamilo\Core\Group\Storage\Repository\GroupMembershipRepository
     */
    protected $groupMembershipRepository;

    /**
     * @var \Chamilo\Core\Group\Service\GroupEventNotifier
     */
    protected $groupEventNotifier;

    /**
     * @param \Chamilo\Core\Group\Storage\Repository\GroupMembershipRepository $groupMembershipRepository
     * @param \Chamilo\Core\Group\Service\GroupEventNotifier $groupEventNotifier
     */
    public function __construct(
        GroupMembershipRepository $groupMembershipRepository, GroupEventNotifier $groupEventNotifier
    )
    {
        $this->groupMembershipRepository = $groupMembershipRepository;
        $this->groupEventNotifier = $groupEventNotifier;
    }

    /**
     * @param integer $groupIdentifier
     *
     * @return integer
     * @throws \Exception
     */
    public function countSubscribedUsersForGroupIdentifier(int $groupIdentifier)
    {
        return $this->getGroupMembershipRepository()->countSubscribedUsersForGroupIdentifier($groupIdentifier);
    }

    /**
     * @param integer[] $groupIdentifiers
     *
     * @return integer
     * @throws \Exception
     */
    public function countSubscribedUsersForGroupIdentifiers(array $groupIdentifiers)
    {
        return $this->getGroupMembershipRepository()->countSubscribedUsersForGroupIdentifiers(
            $groupIdentifiers
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
        return $this->getGroupMembershipRepository()->findSubscribedUserIdentifiersForGroupIdentifier($groupIdentifier);
    }

    /**
     * @param integer[] $groupIdentifiers
     *
     * @return integer[]
     * @throws \Exception
     */
    public function findSubscribedUserIdentifiersForGroupIdentifiers(array $groupIdentifiers)
    {
        return $this->getGroupMembershipRepository()->findSubscribedUserIdentifiersForGroupIdentifiers(
            $groupIdentifiers
        );
    }

    /**
     * @return \Chamilo\Core\Group\Storage\Repository\GroupMembershipRepository
     */
    public function getGroupMembershipRepository(): GroupMembershipRepository
    {
        return $this->groupMembershipRepository;
    }

    /**
     * @param \Chamilo\Core\Group\Storage\Repository\GroupMembershipRepository $groupMembershipRepository
     */
    public function setGroupMembershipRepository(GroupMembershipRepository $groupMembershipRepository): void
    {
        $this->groupMembershipRepository = $groupMembershipRepository;
    }

    /**
     * @param \Chamilo\Core\Group\Storage\DataClass\Group $group
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @return \Chamilo\Core\Group\Storage\DataClass\GroupRelUser|\Chamilo\Libraries\Storage\DataClass\CompositeDataClass|\Chamilo\Libraries\Storage\DataClass\DataClass
     */
    public function getGroupUserRelationByGroupAndUser(Group $group, User $user)
    {
        return $this->getGroupMembershipRepository()->findGroupRelUserByGroupAndUserId($group->getId(), $user->getId());
    }

    /**
     * @param string $groupCode
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @return \Chamilo\Core\Group\Storage\DataClass\GroupRelUser|\Chamilo\Libraries\Storage\DataClass\DataClass
     */
    public function getGroupUserRelationByGroupCodeAndUser(string $groupCode, User $user)
    {
        return $this->getGroupMembershipRepository()->findGroupRelUserByGroupCodeAndUserId($groupCode, $user->getId());
    }

    /**
     * @param \Chamilo\Core\Group\Storage\DataClass\Group $group
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @return \Chamilo\Core\Group\Storage\DataClass\GroupRelUser|\Chamilo\Libraries\Storage\DataClass\CompositeDataClass|\Chamilo\Libraries\Storage\DataClass\DataClass
     * @throws \Exception
     */
    public function subscribeUserToGroup(Group $group, User $user)
    {
        $groupRelation =
            $this->getGroupMembershipRepository()->findGroupRelUserByGroupAndUserId($group->getId(), $user->getId());

        if (!$groupRelation instanceof GroupRelUser)
        {
            $groupRelation = new GroupRelUser();
            $groupRelation->set_user_id($user->getId());
            $groupRelation->set_group_id($group->getId());

            if (!$this->getGroupMembershipRepository()->createGroupUserRelation($groupRelation))
            {
                throw new \RuntimeException(
                    sprintf('Could not subscribe the user %s to the group %s', $user->getId(), $group->getId())
                );
            }

            $this->groupEventNotifier->afterSubscribe($group, $user);
        }

        return $groupRelation;
    }

    /**
     * @param \Chamilo\Core\Group\Storage\DataClass\Group $group
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     */
    public function unsubscribeUserFromGroup(Group $group, User $user)
    {
        $groupRelation =
            $this->getGroupMembershipRepository()->findGroupRelUserByGroupAndUserId($group->getId(), $user->getId());

        if (!$groupRelation instanceof GroupRelUser)
        {
            throw new \RuntimeException(
                sprintf('Could not unsubscribe the user %s from the group %s because there is no active subscription', $user->getId(), $group->getId())
            );
        }

        if(!$this->getGroupMembershipRepository()->deleteGroupUserRelation($groupRelation))
        {
            throw new \RuntimeException(
                sprintf('Could not unsubscribe the user %s to the group %s', $user->getId(), $group->getId())
            );
        }

        $this->groupEventNotifier->afterUnsubscribe($group, $user);
    }

    /**
     * @param integer[] $groupIdentifiers
     *
     * @return boolean
     */
    public function unsubscribeUsersFromGroupIdentifiers(array $groupIdentifiers)
    {
        $success = $this->getGroupMembershipRepository()->unsubscribeUsersFromGroupIdentifiers($groupIdentifiers);
        if(!$success)
        {
            return false;
        }

        foreach($groupIdentifiers as $groupIdentifier)
        {
            $group = new Group();
            $group->setId($groupIdentifier);

            $this->groupEventNotifier->afterEmptyGroup($group);
        }

        return true;
    }

    /**
     * @param \Chamilo\Core\Group\Storage\DataClass\Group[] $groups
     *
     * @return boolean
     */
    public function unsubscribeUsersFromGroups(DataClassIterator $groups)
    {
        $groupsIdentifiers = array();

        foreach ($groups as $group)
        {
            $groupsIdentifiers[] = $group->getId();
        }

        return $this->unsubscribeUsersFromGroupIdentifiers($groupsIdentifiers);
    }
}