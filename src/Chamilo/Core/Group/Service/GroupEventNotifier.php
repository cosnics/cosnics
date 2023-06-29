<?php
namespace Chamilo\Core\Group\Service;

use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Core\User\Storage\DataClass\User;

/**
 * @package Chamilo\Core\Group\Service
 * @author  Sven Vanpoucke - Hogeschool Gent
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class GroupEventNotifier implements GroupEventListenerInterface
{
    /**
     * @var \Chamilo\Core\Group\Service\GroupEventListenerInterface[]
     */
    protected array $groupEventListeners;

    public function __construct()
    {
        $this->groupEventListeners = [];
    }

    public function addGroupEventListener(GroupEventListenerInterface $groupEventListener): void
    {
        $this->groupEventListeners[] = $groupEventListener;
    }

    public function afterCreate(Group $group): bool
    {
        foreach ($this->groupEventListeners as $groupEventListener)
        {
            $groupEventListener->afterCreate($group);
        }

        return true;
    }

    /**
     * @param int[] $subGroupIds
     * @param int[] $impactedUserIds
     */
    public function afterDelete(Group $group, array $subGroupIds = [], array $impactedUserIds = []): bool
    {
        foreach ($this->groupEventListeners as $groupEventListener)
        {
            $groupEventListener->afterDelete($group, $subGroupIds, $impactedUserIds);
        }

        return true;
    }

    /**
     * @param int[] $impactedUserIds
     */
    public function afterEmptyGroup(Group $group, array $impactedUserIds = []): bool
    {
        foreach ($this->groupEventListeners as $groupEventListener)
        {
            $groupEventListener->afterEmptyGroup($group, $impactedUserIds);
        }

        return true;
    }

    public function afterMove(Group $group, Group $oldParentGroup, Group $newParentGroup): bool
    {
        foreach ($this->groupEventListeners as $groupEventListener)
        {
            $groupEventListener->afterMove($group, $oldParentGroup, $newParentGroup);
        }

        return true;
    }

    public function afterSubscribe(Group $group, User $user): bool
    {
        foreach ($this->groupEventListeners as $groupEventListener)
        {
            $groupEventListener->afterSubscribe($group, $user);
        }

        return true;
    }

    public function afterUnsubscribe(Group $group, User $user): bool
    {
        foreach ($this->groupEventListeners as $groupEventListener)
        {
            $groupEventListener->afterUnsubscribe($group, $user);
        }

        return true;
    }

    public function afterUpdate(Group $group): bool
    {
        foreach ($this->groupEventListeners as $groupEventListener)
        {
            $groupEventListener->afterUpdate($group);
        }

        return true;
    }
}