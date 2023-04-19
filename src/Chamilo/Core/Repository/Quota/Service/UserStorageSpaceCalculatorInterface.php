<?php
namespace Chamilo\Core\Repository\Quota\Service;

use Chamilo\Core\User\Storage\DataClass\User;

/**
 * @package Chamilo\Core\Repository\Quota\Service
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
interface UserStorageSpaceCalculatorInterface
{
    public const POLICY_GROUP_HIGHEST = 1;
    public const POLICY_GROUP_LOWEST = 2;
    public const POLICY_HIGHEST = 3;
    public const POLICY_LOWEST = 4;
    public const POLICY_USER = 0;

    public function getAllowedStorageSpaceForUser(User $user): int;

    public function getAvailableStorageSpaceForUser(User $user): int;

    public function getUsedAggregatedUserStorageSpace(): int;

    public function getUsedStorageSpaceForUser(User $user): int;

    public function isQuotumDefinedForUser(User $user): bool;

}