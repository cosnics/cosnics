<?php

namespace Chamilo\Core\Group\Service;

use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Core\Group\Storage\DataClass\GroupRelUser;
use Chamilo\Core\User\Storage\DataClass\User;

/**
 * Class GroupSubscriptionService
 * @package Chamilo\Core\Group\Service
 */
class GroupSubscriptionService
{
    /**
     * @var \Chamilo\Core\Group\Storage\Repository\GroupSubscriptionRepository
     */
    protected $groupSubscriptionRepository;

    /**
     * @var \Chamilo\Core\Group\Service\GroupService
     */
    protected $groupService;

    /**
     * GroupSubscriptionService constructor.
     *
     * @param \Chamilo\Core\Group\Storage\Repository\GroupSubscriptionRepository $groupSubscriptionRepository
     * @param \Chamilo\Core\Group\Service\GroupService $groupService
     */
    public function __construct(
        \Chamilo\Core\Group\Storage\Repository\GroupSubscriptionRepository $groupSubscriptionRepository,
        GroupService $groupService
    )
    {
        $this->groupSubscriptionRepository = $groupSubscriptionRepository;
        $this->groupService = $groupService;
    }

    /**
     * @param \Chamilo\Core\Group\Storage\DataClass\Group $group
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @return \Chamilo\Core\Group\Storage\DataClass\GroupRelUser|\Chamilo\Libraries\Storage\DataClass\CompositeDataClass|\Chamilo\Libraries\Storage\DataClass\DataClass
     */
    public function findGroupUserRelation(Group $group, User $user)
    {
        return $this->groupSubscriptionRepository->findGroupUserRelation($group, $user);
    }

    /**
     * @param string $groupCode
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     */
    public function subscribeUserToGroupByCode($groupCode, User $user)
    {
        $group = $this->groupService->findGroupByCode($groupCode);
        $this->subscribeUserToGroup($group, $user);
    }

    /**
     * @param \Chamilo\Core\Group\Storage\DataClass\Group $group
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     */
    public function subscribeUserToGroup(Group $group, User $user)
    {
        $groupUserRelation = $this->findGroupUserRelation($group, $user);

        if (!$groupUserRelation instanceof GroupRelUser)
        {
            $groupUserRelation = new GroupRelUser();
            $groupUserRelation->set_user_id($user->getId());
            $groupUserRelation->set_group_id($group->getId());

            if (!$this->groupSubscriptionRepository->createGroupUserRelation($groupUserRelation))
            {
                throw new \RuntimeException(
                    sprintf('Could not subscribe the user %s to group %s', $user->getId(), $group->getId())
                );
            }
        }
    }

    /**
     * @param string $groupCode
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     */
    public function removeUserFromGroupByCode($groupCode, User $user)
    {
        $group = $this->groupService->findGroupByCode($groupCode);
        $this->removeUserFromGroup($group, $user);
    }

    /**
     * @param \Chamilo\Core\Group\Storage\DataClass\Group $group
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     */
    public function removeUserFromGroup(Group $group, User $user)
    {
        $groupUserRelation = $this->findGroupUserRelation($group, $user);
        if(!$groupUserRelation instanceof GroupRelUser)
        {
            return;
        }

        if(!$this->groupSubscriptionRepository->deleteGroupUserRelation($groupUserRelation))
        {
            throw new \RuntimeException(
                sprintf('Could not remove the user %s from group %s', $user->getId(), $group->getId())
            );
        }
    }

    /**
     * @param \Chamilo\Core\Group\Storage\DataClass\Group $group
     *
     * @return \Chamilo\Libraries\Storage\Iterator\DataClassIterator|User[]
     */
    public function findUsersDirectlySubscribedToGroup(Group $group)
    {
        return $this->groupSubscriptionRepository->findUsersDirectlySubscribedToGroup($group);
    }

    /**
     * @param \Chamilo\Core\Group\Storage\DataClass\Group $group
     *
     * @return int[]
     */
    public function findUserIdsDirectlySubscribedToGroup(Group $group)
    {
        return $this->groupSubscriptionRepository->findUserIdsDirectlySubscribedToGroup($group);
    }

    /**
     * @param \Chamilo\Core\Group\Storage\DataClass\Group $group
     *
     * @return \Chamilo\Libraries\Storage\Iterator\DataClassIterator|User[]
     */
    public function findUsersInGroupAndSubgroups(Group $group)
    {
        return $this->groupSubscriptionRepository->findUsersInGroupAndSubgroups($group);
    }

    /**
     * @param \Chamilo\Core\Group\Storage\DataClass\Group $group
     *
     * @return int[]
     */
    public function findUserIdsInGroupAndSubgroups(Group $group)
    {
        return $this->groupSubscriptionRepository->findUserIdsInGroupAndSubgroups($group);
    }

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @return \Chamilo\Libraries\Storage\Iterator\DataClassIterator|Group[]
     */
    public function findGroupsWhereUserIsDirectlySubscribed(User $user)
    {
        return $this->groupSubscriptionRepository->findGroupsWhereUserIsDirectlySubscribed($user);
    }

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @return int[]
     */
    public function findGroupIdsWhereUserIsDirectlySubscribed(User $user)
    {
        return $this->groupSubscriptionRepository->findGroupIdsWhereUserIsDirectlySubscribed($user);
    }

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @return \Chamilo\Libraries\Storage\Iterator\DataClassIterator|Group[]
     */
    public function findAllGroupsForUser(User $user)
    {
        return $this->groupSubscriptionRepository->findAllGroupsForUser($user);
    }

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @return int[]
     */
    public function findAllGroupIdsForUser(User $user)
    {
        return $this->groupSubscriptionRepository->findAllGroupIdsForUser($user);
    }

    /**
     * @param \Chamilo\Core\Group\Storage\DataClass\Group $group
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @return bool
     */
    public function isUserDirectlySubscribedToGroup(Group $group, User $user)
    {
        return $this->findGroupUserRelation($group, $user) instanceof GroupRelUser;
    }

    /**
     * @param \Chamilo\Core\Group\Storage\DataClass\Group $group
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @return bool
     */
    public function isUserSubscribedToGroupOrSubgroups(Group $group, User $user)
    {
        return $this->groupSubscriptionRepository->countUserSubscriptionsToGroupAndSubgroups($group, $user) > 0;
    }

}