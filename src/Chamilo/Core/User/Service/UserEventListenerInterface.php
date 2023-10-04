<?php
namespace Chamilo\Core\User\Service;

use Chamilo\Core\User\Storage\DataClass\User;

/**
 * @package Chamilo\Core\User\Service
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
interface UserEventListenerInterface
{
    public function afterCreate(User $user): bool;

    public function afterDelete(User $user): bool;

    public function afterEnterPage(User $user, string $pageUri): bool;

    public function afterExport(User $actionUser, User $exportedUser): bool;

    public function afterImport(User $actionUser, User $importedUser): bool;

    public function afterLogin(User $user, ?string $clientIp): bool;

    public function afterPasswordReset(User $user): bool;

    public function afterQuota(User $user): bool;

    public function afterRegistration(User $user): bool;

    public function afterUpdate(User $user): bool;

    public function beforeDelete(User $user): bool;

    public function beforeLeavePage(User $user, string $userVisitIdentifier): bool;

    public function beforeLogout(User $user, ?string $clientIp): bool;
}