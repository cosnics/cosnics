<?php

namespace Chamilo\Core\Group\Service;

use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Core\User\Storage\DataClass\User;

/**
 * Interface GroupEventListenerInterface
 *
 * @package Chamilo\Core\Group\Service
 * @author - Sven Vanpoucke - Hogeschool Gent
 */
interface GroupEventListenerInterface
{
    /**
     * @param \Chamilo\Core\Group\Storage\DataClass\Group $group
     */
    public function afterCreate(Group $group);

    /**
     * @param \Chamilo\Core\Group\Storage\DataClass\Group $group
     */
    public function afterDelete(Group $group);

    /**
     * @param \Chamilo\Core\Group\Storage\DataClass\Group $group
     * @param \Chamilo\Core\Group\Storage\DataClass\Group $oldParentGroup
     * @param \Chamilo\Core\Group\Storage\DataClass\Group $newParentGroup
     */
    public function afterMove(Group $group, Group $oldParentGroup, Group $newParentGroup);

    /**
     * @param \Chamilo\Core\Group\Storage\DataClass\Group $group
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     */
    public function afterSubscribe(Group $group, User $user);

    /**
     * @param \Chamilo\Core\Group\Storage\DataClass\Group $group
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     */
    public function afterUnsubscribe(Group $group, User $user);

    /**
     * @param \Chamilo\Core\Group\Storage\DataClass\Group $group
     */
    public function afterEmptyGroup(Group $group);
}