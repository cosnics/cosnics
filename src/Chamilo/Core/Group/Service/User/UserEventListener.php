<?php
namespace Chamilo\Core\Group\Service\User;

use Chamilo\Core\Group\Service\GroupMembershipService;
use Chamilo\Core\User\Service\UserEventListenerInterface;
use Chamilo\Core\User\Storage\DataClass\User;

/**
 * @package Chamilo\Core\Group\Service\User
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class UserEventListener implements UserEventListenerInterface
{

    protected GroupMembershipService $groupMembershipService;

    public function __construct(GroupMembershipService $groupMembershipService)
    {
        $this->groupMembershipService = $groupMembershipService;
    }

    public function afterCreate(User $user): bool
    {
        return true;
    }

    public function afterDelete(User $user): bool
    {
        return true;
    }

    public function afterEnterPage(User $user, string $pageUri): bool
    {
        return true;
    }

    public function afterExport(User $actionUser, User $exportedUser): bool
    {
        return true;
    }

    public function afterImport(User $actionUser, User $importedUser): bool
    {
        return true;
    }

    public function afterLogin(User $user, ?string $clientIp): bool
    {
        return true;
    }

    public function afterPasswordReset(User $user): bool
    {
        return true;
    }

    public function afterQuota(User $user): bool
    {
        return true;
    }

    public function afterRegistration(User $user): bool
    {
        return true;
    }

    public function afterUpdate(User $user): bool
    {
        return true;
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function beforeDelete(User $user): bool
    {
        return $this->getGroupMembershipService()->unsubscribeUserFromAllGroups($user);
    }

    public function beforeLeavePage(User $user, string $userVisitIdentifier): bool
    {
        return true;
    }

    public function beforeLogout(User $user, ?string $clientIp): bool
    {
        return true;
    }

    public function getGroupMembershipService(): GroupMembershipService
    {
        return $this->groupMembershipService;
    }
}