<?php
namespace Chamilo\Core\Repository\Quota;

use Chamilo\Core\Group\Service\GroupsTreeTraverser;
use Chamilo\Core\Repository\Quota\Rights\Service\RightsService;
use Chamilo\Core\Repository\Quota\Service\StorageSpaceCalculator;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\DependencyInjection\DependencyInjectionContainerBuilder;
use Chamilo\Libraries\Format\Form\FormValidator;

/**
 * @package    Chamilo\Core\Repository\Quota
 * @author     Bart Mollet
 * @author     Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author     Dieter De Neef
 * @author     Magali Gillard <magali.gillard@ehb.be>
 * @author     Eduard Vossen <eduard.vossen@ehb.be>
 * @deprecated Use StorageSpaceCalculator service now
 */
class Calculator
{
    private User $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * @deprecated Use StorageSpaceCalculator::addUploadWarningToForm() now
     */
    public function addUploadWarningToForm(FormValidator $form): void
    {
        $this->getStorageSpaceCalculator()->addUploadWarningToFormForUser($form, $this->getUser());
    }

    /**
     * @deprecated Use StorageSpaceCalculator::doesUserHaveRequestedStorageSpace() now
     */
    public function canUpload(int $requestedStorageSize): bool
    {
        return $this->getStorageSpaceCalculator()->doesUserHaveRequestedStorageSpace(
            $this->getUser(), $requestedStorageSize
        );
    }

    /**
     * @deprecated Use StorageSpaceCalculator::getAggregatedUserStorageSpacePercentage() now
     */
    public function getAggregatedUserDiskQuotaPercentage(): int
    {
        return $this->getStorageSpaceCalculator()->getAggregatedUserStorageSpacePercentage();
    }

    /**
     * @deprecated Use StorageSpaceCalculator::getAllocatedStorageSpacePercentage() now
     */
    public function getAllocatedDiskSpacePercentage(): int
    {
        return $this->getStorageSpaceCalculator()->getAllocatedStorageSpacePercentage();
    }

    /**
     * @deprecated Use StorageSpaceCalculator::getAvailableAggregatedUserStorageSpace() now
     */
    public function getAvailableAggregatedUserDiskQuota(): int
    {
        return $this->getStorageSpaceCalculator()->getAvailableAggregatedUserStorageSpace();
    }

    /**
     * @deprecated Use StorageSpaceCalculator::getAvailableAllocatedStorageSpace()
     */
    public function getAvailableAllocatedDiskSpace(): int
    {
        return $this->getStorageSpaceCalculator()->getAvailableAllocatedStorageSpace();
    }

    /**
     * @deprecated Use StorageSpaceCalculator::getAvailableReservedStorageSpace() now
     */
    public function getAvailableReservedDiskSpace(): int
    {
        return $this->getStorageSpaceCalculator()->getAvailableReservedStorageSpace();
    }

    /**
     * @deprecated Use StorageSpaceCalculator::getAvailableStorageSpaceForUser() now
     */
    public function getAvailableUserDiskQuota(): int
    {
        return $this->getStorageSpaceCalculator()->getAvailableStorageSpaceForUser($this->getUser());
    }

    /**
     * Build a bar-view of the used quota.
     *
     * @param int $percent   The percentage of the bar that is in use
     * @param string $status A status message which will be displayed below the bar.
     */
    public static function getBar(int $percent, string $status): string
    {
        $html = [];

        if ($percent >= 100)
        {
            $percent = 100;
        }

        if ($percent >= 90)
        {
            $class = 'progress-bar-danger';
        }
        elseif ($percent >= 80)
        {
            $class = 'progress-bar-warning';
        }
        else
        {
            $class = 'progress-bar-success';
        }

        $displayPercent = round($percent);

        $html[] = '<div class="progress">';
        $html[] = '<div class="progress-bar progress-bar-striped ' . $class . '" role="progressbar" aria-valuenow="' .
            $displayPercent . '" aria-valuemin="0" aria-valuemax="100" style="width: ' . $displayPercent .
            '%; min-width: 2em;">';
        $html[] = $status . ' &ndash; ' . $displayPercent . '%';
        $html[] = '</div>';
        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }

    /**
     * @deprecated Use StorageSpaceCalculator::getHighestGroupQuotumForUser() now
     */
    public function getGroupHighest(): int
    {
        return $this->getGroupsTreeTraverser()->getHighestGroupQuotumForUser($this->getUser());
    }

    /**
     * @deprecated Use StorageSpaceCalculator::getLowestGroupQuotumForUser() now
     */
    public function getGroupLowest(): int
    {
        return $this->getGroupsTreeTraverser()->getLowestGroupQuotumForUser($this->getUser());
    }

    protected function getGroupsTreeTraverser(): GroupsTreeTraverser
    {
        return $this->getService(GroupsTreeTraverser::class);
    }

    /**
     * @deprecated Use StorageSpaceCalculator::getMaximumAggregatedUserStorageSpace() now
     */
    public function getMaximumAggregatedUserDiskQuota(): int
    {
        return $this->getStorageSpaceCalculator()->getMaximumAggregatedUserStorageSpace();
    }

    /**
     * @deprecated Use StorageSpaceCalculator::getMaximumAllocatedStorageSpace() now
     */
    public function getMaximumAllocatedDiskSpace(): int
    {
        return $this->getStorageSpaceCalculator()->getMaximumAllocatedStorageSpace();
    }

    /**
     * @deprecated Use StorageSpaceCalculator::getMaximumAllocatedStorageSpace() now
     */
    public function getMaximumReservedDiskSpace(): int
    {
        return $this->getStorageSpaceCalculator()->getMaximumAllocatedStorageSpace();
    }

    /**
     * @deprecated USe StorageSpaceCalculator::getMaximumUploadSizeForUser() now
     */
    public function getMaximumUploadSize(): int
    {
        return $this->getStorageSpaceCalculator()->getMaximumUploadSizeForUser($this->getUser());
    }

    /**
     * @deprecated Use StorageSpaceCalculator::getAllowedStorageSpaceForUser() now
     */
    public function getMaximumUserDiskQuota(): int
    {
        return $this->getStorageSpaceCalculator()->getAllowedStorageSpaceForUser($this->getUser());
    }

    /**
     * @deprecated Use StorageSpaceCalculator::getReservedStorageSpacePercentage() now
     */
    public function getReservedDiskSpacePercentage(): int
    {
        return $this->getStorageSpaceCalculator()->getReservedStorageSpacePercentage();
    }

    public function getRightsService(): RightsService
    {
        return $this->getService(RightsService::class);
    }

    protected function getService(string $serviceName)
    {
        return DependencyInjectionContainerBuilder::getInstance()->createContainer()->get(
            $serviceName
        );
    }

    protected function getStorageSpaceCalculator(): StorageSpaceCalculator
    {
        return $this->getService(StorageSpaceCalculator::class);
    }

    /**
     * @deprecated Use StorageSpaceCalculator::getMaximumAggregatedUserStorageSpace() now
     */
    public function getTotalUserDiskQuota(): int
    {
        return $this->getStorageSpaceCalculator()->getMaximumAggregatedUserStorageSpace();
    }

    /**
     * @deprecated Use StorageSpaceCalculator::getUsedAggregatedUserStorageSpace() now
     */
    public function getUsedAggregatedUserDiskQuota(): int
    {
        return $this->getStorageSpaceCalculator()->getUsedAggregatedUserStorageSpace();
    }

    /**
     * @deprecated Use StorageSpaceCalculator::getUsedAggregatedUserStorageSpace() now
     */
    public function getUsedAllocatedDiskSpace(): int
    {
        return $this->getStorageSpaceCalculator()->getUsedAggregatedUserStorageSpace();
    }

    /**
     * @deprecated Use StorageSpaceCalculator::getMaximumAggregatedUserStorageSpace() now
     */
    public function getUsedReservedDiskSpace(): int
    {
        return $this->getStorageSpaceCalculator()->getMaximumAggregatedUserStorageSpace();
    }

    /**
     * @deprecated Use StorageSpaceCalculator::getUsedStorageSpaceForUser() now
     */
    public function getUsedUserDiskQuota(): int
    {
        return $this->getStorageSpaceCalculator()->getUsedStorageSpaceForUser($this->getUser());
    }

    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @deprecated UseStorageSpaceCalculator::geStorageSpacePercentageForUser() now
     */
    public function getUserDiskQuotaPercentage(): int
    {
        return $this->getStorageSpaceCalculator()->getStorageSpacePercentageForUser($this->getUser());
    }

    /**
     * @deprecated Use StorageSpaceCalculator::isStorageQuotumEnabled() now
     */
    public function isEnabled(): bool
    {
        return $this->getStorageSpaceCalculator()->isStorageQuotumEnabled();
    }

    /**
     * @throws \Chamilo\Libraries\Rights\Exception\RightsLocationNotFoundException
     * @deprecated Use RightsService::canUserRequestAdditionalStorageSpace() now
     */
    public function requestAllowed(): bool
    {
        return $this->getRightsService()->canUserRequestAdditionalStorageSpace($this->getUser());
    }

    /**
     * @throws \Chamilo\Libraries\Rights\Exception\RightsLocationNotFoundException
     * @deprecated Use RightsService::canUserUpgradeStorageSpace() now
     */
    public function upgradeAllowed(): bool
    {
        return $this->getRightsService()->canUserUpgradeStorageSpace($this->getUser());
    }

    /**
     * @deprecated Use StorageSpaceCalculator::isQuotumDefinedForUser() now
     */
    public function usesUserDiskQuota(): bool
    {
        return $this->getStorageSpaceCalculator()->isQuotumDefinedForUser($this->getUser());
    }
}
