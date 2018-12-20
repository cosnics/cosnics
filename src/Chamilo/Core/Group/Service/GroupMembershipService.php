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
    private $groupMembershipRepository;

    /**
     * @param \Chamilo\Core\Group\Storage\Repository\GroupMembershipRepository $groupMembershipRepository
     */
    public function __construct(GroupMembershipRepository $groupMembershipRepository)
    {
        $this->groupMembershipRepository = $groupMembershipRepository;
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

            if (!$this->getGroupMembershipRepository()->create($groupRelation))
            {
                throw new \RuntimeException(
                    sprintf('Could not subscribe the user %s to the group %s', $user->getId(), $group->getId())
                );
            }
        }
    }

    /**
     * @param integer[] $groupsIdentifiers
     *
     * @return boolean
     */
    public function unsubscribeUsersFromGroupIdentifiers(array $groupsIdentifiers)
    {
        return $this->getGroupMembershipRepository()->unsubscribeUsersFromGroupIdentifiers($groupsIdentifiers);
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
}