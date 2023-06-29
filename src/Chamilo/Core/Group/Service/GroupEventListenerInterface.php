<?php
namespace Chamilo\Core\Group\Service;

use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Core\User\Storage\DataClass\User;

/**
 * @package Chamilo\Core\Group\Service
 * @author  - Sven Vanpoucke - Hogeschool Gent
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
interface GroupEventListenerInterface
{
    public function afterCreate(Group $group): bool;

    /**
     * @param int[] $subGroupIds
     * @param int[] $impactedUserIds
     */
    public function afterDelete(Group $group, array $subGroupIds = [], array $impactedUserIds = []): bool;

    /**
     * @param int[] $impactedUserIds
     */
    public function afterEmptyGroup(Group $group, array $impactedUserIds = []): bool;

    public function afterMove(Group $group, Group $oldParentGroup, Group $newParentGroup): bool;

    public function afterSubscribe(Group $group, User $user): bool;

    public function afterUnsubscribe(Group $group, User $user): bool;

    public function afterUpdate(Group $group): bool;
}