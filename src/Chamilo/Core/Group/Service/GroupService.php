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
     * This is temporarily used for BC issues
     *
     * @var \Chamilo\Core\Group\Service\GroupSubscriptionService
     */
    protected $groupSubscriptionService;

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
     * @param \Chamilo\Core\Group\Service\GroupSubscriptionService $groupSubscriptionService
     *
     * @return \Chamilo\Core\Group\Service\GroupService
     */
    public function setGroupSubscriptionService(
        \Chamilo\Core\Group\Service\GroupSubscriptionService $groupSubscriptionService
    ): \Chamilo\Core\Group\Service\GroupService
    {
        $this->groupSubscriptionService = $groupSubscriptionService;

        return $this;
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
     * @param string $groupCode
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     */
    public function subscribeUserToGroupByCode($groupCode, User $user)
    {
        $this->groupSubscriptionService->subscribeUserToGroupByCode($groupCode, $user);
    }

    /**
     * @param \Chamilo\Core\Group\Storage\DataClass\Group $group
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     */
    public function subscribeUserToGroup(Group $group, User $user)
    {
        $this->groupSubscriptionService->subscribeUserToGroup($group, $user);
    }

    /**
     * @param \Chamilo\Core\Group\Storage\DataClass\Group $group
     *
     * @return Group[]
     */
    public function findDirectChildrenFromGroup(Group $group)
    {
        return $this->groupRepository->getDirectChildrenOfGroup($group);
    }
}