<?php

namespace Chamilo\Core\Group\Service;

use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Core\User\Storage\DataClass\User;

/**
 * @package Chamilo\Core\Group\Service
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class GroupEventNotifier implements GroupEventListenerInterface
{
    /**
     * @var \Chamilo\Core\Group\Service\GroupEventListenerInterface[]
     */
    protected $groupEventListeners;

    /**
     * GroupEventNotifier constructor.
     */
    public function __construct()
    {
        $this->groupEventListeners = [];
    }

    /**
     * @param \Chamilo\Core\Group\Service\GroupEventListenerInterface $groupEventListener
     */
    public function addGroupEventListener(GroupEventListenerInterface $groupEventListener)
    {
        $this->groupEventListeners[] = $groupEventListener;
    }

    /**
     * @param \Chamilo\Core\Group\Storage\DataClass\Group $group
     */
    public function afterCreate(Group $group)
    {
        foreach($this->groupEventListeners as $groupEventListener)
        {
            $groupEventListener->afterCreate($group);
        }
    }

    /**
     * @param \Chamilo\Core\Group\Storage\DataClass\Group $group
     */
    public function beforeDelete(Group $group)
    {
        foreach($this->groupEventListeners as $groupEventListener)
        {
            $groupEventListener->beforeDelete($group);
        }
    }

    /**
     * @param \Chamilo\Core\Group\Storage\DataClass\Group $group
     * @param int[] $subGroupIds
     * @param int[] $impactedUserIds
     */
    public function afterDelete(Group $group, array $subGroupIds = [], array $impactedUserIds = [])
    {
        foreach($this->groupEventListeners as $groupEventListener)
        {
            $groupEventListener->afterDelete($group, $subGroupIds, $impactedUserIds);
        }
    }

    /**
     * @param \Chamilo\Core\Group\Storage\DataClass\Group $group
     * @param \Chamilo\Core\Group\Storage\DataClass\Group $oldParentGroup
     * @param \Chamilo\Core\Group\Storage\DataClass\Group $newParentGroup
     */
    public function afterMove(Group $group, Group $oldParentGroup, Group $newParentGroup)
    {
        foreach($this->groupEventListeners as $groupEventListener)
        {
            $groupEventListener->afterMove($group, $oldParentGroup, $newParentGroup);
        }
    }

    /**
     * @param \Chamilo\Core\Group\Storage\DataClass\Group $group
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     */
    public function afterSubscribe(Group $group, User $user)
    {
        foreach($this->groupEventListeners as $groupEventListener)
        {
            $groupEventListener->afterSubscribe($group, $user);
        }
    }

    /**
     * @param \Chamilo\Core\Group\Storage\DataClass\Group $group
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     */
    public function afterUnsubscribe(Group $group, User $user)
    {
        foreach($this->groupEventListeners as $groupEventListener)
        {
            $groupEventListener->afterUnsubscribe($group, $user);
        }
    }

    /**
     * @param \Chamilo\Core\Group\Storage\DataClass\Group $group
     * @param int[] $impactedUserIds
     */
    public function afterEmptyGroup(Group $group, array $impactedUserIds = [])
    {
        foreach($this->groupEventListeners as $groupEventListener)
        {
            $groupEventListener->afterEmptyGroup($group, $impactedUserIds);
        }
    }
}