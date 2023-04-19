<?php
namespace Chamilo\Core\Repository\Quota\Service;

use Chamilo\Configuration\Service\Consulter\ConfigurationConsulter;
use Chamilo\Core\Group\Service\GroupsTreeTraverser;
use Chamilo\Core\Repository\Service\ContentObjectService;
use Chamilo\Core\User\Storage\DataClass\User;

/**
 * @package Chamilo\Core\Repository\Quota\Service
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class UserStorageSpaceCalculator implements UserStorageSpaceCalculatorInterface
{

    protected ConfigurationConsulter $configurationConsulter;

    protected ContentObjectService $contentObjectService;

    protected GroupsTreeTraverser $groupsTreeTraverser;

    public function getAllowedStorageSpaceForUser(User $user): int
    {
        $quotumPolicy = $this->getConfigurationConsulter()->getSetting(['Chamilo\Core\Repository', 'quota_policy']);
        $useQuotumFallback = (bool) $this->getConfigurationConsulter()->getSetting(
            ['Chamilo\Core\Repository', 'quota_fallback']
        );
        $isQuotumFallbackUser = (bool) $this->getConfigurationConsulter()->getSetting(
            ['Chamilo\Core\Repository', 'quota_fallback_user']
        );
        $groupsTreeTraverser = $this->getGroupsTreeTraverser();

        switch ($quotumPolicy)
        {
            case self::POLICY_USER :
                if ($user->get_disk_quota() || !$useQuotumFallback)
                {
                    return $user->get_disk_quota();
                }
                elseif ($isQuotumFallbackUser == 0)
                {
                    return $groupsTreeTraverser->getHighestGroupQuotumForUser($user);
                }
                else
                {
                    return $groupsTreeTraverser->getLowestGroupQuotumForUser($user);
                }
            case self::POLICY_GROUP_HIGHEST :
                $groupQuotum = $groupsTreeTraverser->getHighestGroupQuotumForUser($user);

                if ($groupQuotum || !$useQuotumFallback)
                {
                    return $groupQuotum;
                }
                else
                {
                    return $user->get_disk_quota();
                }
            case self::POLICY_GROUP_LOWEST :
                $groupQuotum = $groupsTreeTraverser->getLowestGroupQuotumForUser($user);

                if ($groupQuotum || !$useQuotumFallback)
                {
                    return $groupQuotum;
                }
                else
                {
                    return $user->get_disk_quota();
                }
            case self::POLICY_HIGHEST :
                $groupQuotum = $groupsTreeTraverser->getHighestGroupQuotumForUser($user);

                if ($groupQuotum > $user->get_disk_quota())
                {
                    return $groupQuotum;
                }
                else
                {
                    return $user->get_disk_quota();
                }
            case self::POLICY_LOWEST :
                $groupQuotum = $groupsTreeTraverser->getLowestGroupQuotumForUser($user);

                if ($groupQuotum > $user->get_disk_quota() || !$groupQuotum)
                {
                    return $user->get_disk_quota();
                }
                else
                {
                    return $groupQuotum;
                }
            default :
                return $user->get_disk_quota();
        }
    }

    public function getAvailableStorageSpaceForUser(User $user): int
    {
        $availableStorageSpace = $this->getAllowedStorageSpaceForUser($user) - $this->getUsedStorageSpaceForUser($user);

        return max($availableStorageSpace, 0);
    }

    public function getConfigurationConsulter(): ConfigurationConsulter
    {
        return $this->configurationConsulter;
    }

    public function getContentObjectService(): ContentObjectService
    {
        return $this->contentObjectService;
    }

    public function getGroupsTreeTraverser(): GroupsTreeTraverser
    {
        return $this->groupsTreeTraverser;
    }

    public function getUsedAggregatedUserStorageSpace(): int
    {
        return $this->getContentObjectService()->getUsedStorageSpace();
    }

    public function getUsedStorageSpaceForUser(User $user): int
    {
        return $this->getContentObjectService()->getUsedStorageSpaceForUser($user);
    }

    public function isQuotumDefinedForUser(User $user): bool
    {
        $quotaPolicy = $this->getConfigurationConsulter()->getSetting(['Chamilo\Core\Repository', 'quota_policy']);
        $useQuotaFallback =
            (bool) $this->getConfigurationConsulter()->getSetting(['Chamilo\Core\Repository', 'quota_fallback']);
        $isQuotaFallbackUser = (bool) $this->getConfigurationConsulter()->getSetting(
            ['Chamilo\Core\Repository', 'quota_fallback_user']
        );
        $groupsTreeTraverser = $this->getGroupsTreeTraverser();

        switch ($quotaPolicy)
        {
            case self::POLICY_USER :
                if ($user->get_disk_quota() || !$useQuotaFallback)
                {
                    return true;
                }
                elseif ($isQuotaFallbackUser == 0)
                {
                    $groupQuota = $groupsTreeTraverser->getHighestGroupQuotumForUser($user);

                    return !($groupQuota > $user->get_disk_quota());
                }
                else
                {
                    $groupQuota = $groupsTreeTraverser->getLowestGroupQuotumForUser($user);

                    return !$groupQuota;
                }
            case self::POLICY_GROUP_HIGHEST :
                $groupQuota = $groupsTreeTraverser->getHighestGroupQuotumForUser($user);

                return !($groupQuota || !$useQuotaFallback);
            case self::POLICY_GROUP_LOWEST :
                $groupQuota = $groupsTreeTraverser->getLowestGroupQuotumForUser($user);

                return !($groupQuota || !$useQuotaFallback);
            case self::POLICY_HIGHEST :
                $groupQuota = $groupsTreeTraverser->getHighestGroupQuotumForUser($user);

                return !($groupQuota > $user->get_disk_quota());
            case self::POLICY_LOWEST :
                $groupQuota = $groupsTreeTraverser->getLowestGroupQuotumForUser($user);

                return $groupQuota > $user->get_disk_quota() || !$groupQuota;
            default :
                return true;
        }
    }

}