<?php
namespace Chamilo\Core\Repository\Quota\Service;

use Chamilo\Configuration\Service\Consulter\ConfigurationConsulter;
use Chamilo\Core\Group\Service\GroupsTreeTraverser;
use Chamilo\Core\Repository\Filter\FilterData;
use Chamilo\Core\Repository\Quota\Manager;
use Chamilo\Core\Repository\Service\ContentObjectService;
use Chamilo\Core\User\Service\UserService;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Application\Routing\UrlGenerator;
use Chamilo\Libraries\Cache\CacheDataSaverTrait;
use Chamilo\Libraries\File\ConfigurablePathBuilder;
use Chamilo\Libraries\File\Filesystem;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Storage\Exception\DataClassNoResultException;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Core\Repository\Quota\Service
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class StorageSpaceCalculator
{
    use CacheDataSaverTrait;

    public const CACHE_KEY_MAXIMUM_AGGREGATED_USER_STORAGE_SPACE = 'd07b197bfa9677710b95fc5202198f83';

    public const POLICY_GROUP_HIGHEST = 1;
    public const POLICY_GROUP_LOWEST = 2;
    public const POLICY_HIGHEST = 3;
    public const POLICY_LOWEST = 4;
    public const POLICY_USER = 0;

    protected AdapterInterface $cacheAdapter;

    protected GroupsTreeTraverser $groupsTreeTraverser;

    protected UrlGenerator $urlGenerator;

    /**
     * @var int[]
     */
    private array $allowedStorageSpaceForUserCache = [];

    private ConfigurablePathBuilder $configurablePathBuilder;

    private ConfigurationConsulter $configurationConsulter;

    private ContentObjectService $contentObjectService;

    private Translator $translator;

    private ?int $usedAggregatedUserStorageSpace = null;

    /**
     * @var int[]
     */
    private array $usedStorageSpaceForUserCache = [];

    private UserService $userService;

    public function __construct(
        ConfigurationConsulter $configurationConsulter, Translator $translator,
        GroupsTreeTraverser $groupsTreeTraverser, UserService $userService,
        ConfigurablePathBuilder $configurablePathBuilder, ContentObjectService $contentObjectService,
        UrlGenerator $urlGenerator, AdapterInterface $cacheAdapter
    )
    {
        $this->configurationConsulter = $configurationConsulter;
        $this->translator = $translator;
        $this->groupsTreeTraverser = $groupsTreeTraverser;
        $this->userService = $userService;
        $this->configurablePathBuilder = $configurablePathBuilder;
        $this->contentObjectService = $contentObjectService;
        $this->urlGenerator = $urlGenerator;
        $this->cacheAdapter = $cacheAdapter;
    }

    public function addUploadWarningToFormForUser(FormValidator $form, User $user)
    {
        $configurationConsulter = $this->getConfigurationConsulter();
        $translator = $this->getTranslator();

        $postMaxSize = Filesystem::interpret_file_size(ini_get('post_max_size'));
        $uploadMaxFilesize = Filesystem::interpret_file_size(ini_get('upload_max_filesize'));

        $maximumServerSize = max($postMaxSize, $uploadMaxFilesize);
        $availableStorageSpaceForUser = $this->getAvailableStorageSpaceForUser($user);

        if ($this->isStorageQuotumEnabled() && $availableStorageSpaceForUser < $maximumServerSize)
        {
            $url = $this->getUrlGenerator()->fromParameters(
                [
                    Application::PARAM_CONTEXT => \Chamilo\Core\Repository\Manager::CONTEXT,
                    Application::PARAM_ACTION => \Chamilo\Core\Repository\Manager::ACTION_QUOTA,
                    FilterData::FILTER_CATEGORY => null,
                    Manager::PARAM_ACTION => null
                ]
            );

            $allowUpgrade = $configurationConsulter->getSetting(['Chamilo\Core\Repository', 'allow_upgrade']);
            $allowRequest = $configurationConsulter->getSetting(['Chamilo\Core\Repository', 'allow_request']);

            $translation = ($allowUpgrade || $allowRequest) ? 'MaximumFileSizeUser' : 'MaximumFileSizeUserNoUpgrade';

            $message = $translator->trans(
                $translation, [
                'SERVER' => Filesystem::format_file_size($maximumServerSize),
                'USER' => Filesystem::format_file_size($availableStorageSpaceForUser),
                'URL' => $url
            ], 'Chamilo\Core\Repository\Quota'
            );

            if ($availableStorageSpaceForUser < 5242880)
            {
                $form->add_error_message('max_size', null, $message);
            }
            else
            {
                $form->add_warning_message('max_size', null, $message);
            }
        }
        else
        {
            $maximumSize = $maximumServerSize;
            $message = $translator->trans(
                'MaximumFileSizeServer',
                ['FILESIZE' => Filesystem::format_file_size($maximumSize), 'Chamilo\Core\Repository\Quota']
            );
            $form->add_warning_message('max_size', null, $message);
        }
    }

    public function doGetMaximumAggregatedUserStorageSpace(): int
    {
        try
        {
            $totalQuota = 0;
            $users = $this->getUserService()->findUsers();

            foreach ($users as $user)
            {
                $totalQuota += $this->getAllowedStorageSpaceForUser($user);
            }

            return $totalQuota;
        }
        catch (DataClassNoResultException $exception)
        {
            return 0;
        }
    }

    public function doesUserHaveRequestedStorageSpace(User $user, int $requestedStorageSpace): bool
    {
        if (!$this->isStorageQuotumEnabled())
        {
            return true;
        }

        return $this->getAvailableStorageSpaceForUser($user) > $requestedStorageSpace;
    }

    public function getAggregatedUserStorageSpacePercentage(): int
    {
        return 100 * $this->getUsedAggregatedUserStorageSpace() / $this->getMaximumAggregatedUserStorageSpace();
    }

    public function getAllocatedStorageSpacePercentage(): int
    {
        return 100 * $this->getUsedAggregatedUserStorageSpace() / $this->getMaximumAllocatedStorageSpace();
    }

    public function getAllowedStorageSpaceForUser(User $user): int
    {
        if (!isset($this->allowedStorageSpaceForUserCache[$user->getId()]))
        {
            $quotumPolicy = $this->getConfigurationConsulter()->getSetting(['Chamilo\Core\Repository', 'quota_policy']);
            $useQuotumFallback = (bool) $this->getConfigurationConsulter()->getSetting(
                ['Chamilo\Core\Repository', 'quota_fallback']
            );
            $isQuotumFallbackUser = (bool) $this->getConfigurationConsulter()->getSetting(
                ['Chamilo\Core\Repository', 'quota_fallback_user']
            );

            switch ($quotumPolicy)
            {
                case self::POLICY_USER :
                    if ($user->get_disk_quota() || !$useQuotumFallback)
                    {
                        $this->allowedStorageSpaceForUserCache[$user->getId()] = $user->get_disk_quota();
                    }
                    elseif ($isQuotumFallbackUser == 0)
                    {
                        $this->allowedStorageSpaceForUserCache[$user->getId()] =
                            $this->getHighestGroupQuotumForUser($user);
                    }
                    else
                    {
                        $this->allowedStorageSpaceForUserCache[$user->getId()] =
                            $this->getLowestGroupQuotumForUser($user);
                    }

                    break;
                case self::POLICY_GROUP_HIGHEST :
                    $groupQuotum = $this->getHighestGroupQuotumForUser($user);

                    if ($groupQuotum || !$useQuotumFallback)
                    {
                        $this->allowedStorageSpaceForUserCache[$user->getId()] = $groupQuotum;
                    }
                    else
                    {
                        $this->allowedStorageSpaceForUserCache[$user->getId()] = $user->get_disk_quota();
                    }
                    break;
                case self::POLICY_GROUP_LOWEST :
                    $groupQuotum = $this->getLowestGroupQuotumForUser($user);

                    if ($groupQuotum || !$useQuotumFallback)
                    {
                        $this->allowedStorageSpaceForUserCache[$user->getId()] = $groupQuotum;
                    }
                    else
                    {
                        $this->allowedStorageSpaceForUserCache[$user->getId()] = $user->get_disk_quota();
                    }
                    break;
                case self::POLICY_HIGHEST :
                    $groupQuotum = $this->getHighestGroupQuotumForUser($user);

                    if ($groupQuotum > $user->get_disk_quota())
                    {
                        $this->allowedStorageSpaceForUserCache[$user->getId()] = $groupQuotum;
                    }
                    else
                    {
                        $this->allowedStorageSpaceForUserCache[$user->getId()] = $user->get_disk_quota();
                    }
                    break;
                case self::POLICY_LOWEST :
                    $groupQuotum = $this->getLowestGroupQuotumForUser($user);

                    if ($groupQuotum > $user->get_disk_quota() || !$groupQuotum)
                    {
                        $this->allowedStorageSpaceForUserCache[$user->getId()] = $user->get_disk_quota();
                    }
                    else
                    {
                        $this->allowedStorageSpaceForUserCache[$user->getId()] = $groupQuotum;
                    }
                    break;
                default :
                    $this->allowedStorageSpaceForUserCache[$user->getId()] = $user->get_disk_quota();
                    break;
            }
        }

        return $this->allowedStorageSpaceForUserCache[$user->getId()];
    }

    public function getAvailableAggregatedUserStorageSpace(): int
    {
        $availableStorageSpace =
            $this->getMaximumAggregatedUserStorageSpace() - $this->getUsedAggregatedUserStorageSpace();

        return max($availableStorageSpace, 0);
    }

    public function getAvailableAllocatedStorageSpace(): int
    {
        return $this->getMaximumAllocatedStorageSpace() - $this->getUsedAggregatedUserStorageSpace();
    }

    public function getAvailableReservedStorageSpace(): int
    {
        $availableStorageSpace =
            $this->getMaximumAllocatedStorageSpace() - $this->getMaximumAggregatedUserStorageSpace();

        return max($availableStorageSpace, 0);
    }

    public function getAvailableStorageSpaceForUser(User $user): int
    {
        $availableStorageSpace = $this->getAllowedStorageSpaceForUser($user) - $this->getUsedStorageSpaceForUser($user);

        return max($availableStorageSpace, 0);
    }

    public function getCacheAdapter(): AdapterInterface
    {
        return $this->cacheAdapter;
    }

    public function getConfigurablePathBuilder(): ConfigurablePathBuilder
    {
        return $this->configurablePathBuilder;
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

    public function getHighestGroupQuotumForUser(User $user): int
    {
        return $this->getGroupsTreeTraverser()->getHighestGroupQuotumForUser($user);
    }

    public function getLowestGroupQuotumForUser(User $user): int
    {
        return $this->getGroupsTreeTraverser()->getLowestGroupQuotumForUser($user);
    }

    public function getMaximumAggregatedUserStorageSpace(): int
    {
        try
        {
            if (!$this->hasCacheData(self::CACHE_KEY_MAXIMUM_AGGREGATED_USER_STORAGE_SPACE))
            {
                $this->saveCacheData(
                    self::CACHE_KEY_MAXIMUM_AGGREGATED_USER_STORAGE_SPACE,
                    $this->doGetMaximumAggregatedUserStorageSpace()
                );
            }

            return $this->loadData(self::CACHE_KEY_MAXIMUM_AGGREGATED_USER_STORAGE_SPACE);
        }
        catch (InvalidArgumentException $e)
        {
            return 0;
        }
    }

    public function getMaximumAllocatedStorageSpace(): int
    {
        return (int) disk_total_space($this->getConfigurablePathBuilder()->getRepositoryPath());
    }

    public function getMaximumUploadSizeForUser(User $user): int
    {
        $postMaxSize = Filesystem::interpret_file_size(ini_get('post_max_size'));
        $uploadMaxFilesize = Filesystem::interpret_file_size(ini_get('upload_max_filesize'));

        $maximumServerSize = max($postMaxSize, $uploadMaxFilesize);
        $availableStorageSpaceForUser = $this->getAvailableStorageSpaceForUser($user);

        if ($this->isStorageQuotumEnabled() && $availableStorageSpaceForUser < $maximumServerSize)
        {
            $maximumUploadSize = min($availableStorageSpaceForUser, $maximumServerSize);
        }
        else
        {
            $maximumUploadSize = $maximumServerSize;
        }

        return (int) floor($maximumUploadSize / 1024 / 1024);
    }

    public function getReservedStorageSpacePercentage(): int
    {
        return 100 * $this->getMaximumAggregatedUserStorageSpace() / $this->getMaximumAllocatedStorageSpace();
    }

    public function getStorageSpacePercentageForUser(User $user): int
    {
        return 100 * $this->getUsedStorageSpaceForUser($user) / $this->getAllowedStorageSpaceForUser($user);
    }

    public function getTranslator(): Translator
    {
        return $this->translator;
    }

    public function getUrlGenerator(): UrlGenerator
    {
        return $this->urlGenerator;
    }

    public function getUsedAggregatedUserStorageSpace(): int
    {
        if (!isset($this->usedAggregatedUserStorageSpace))
        {
            $this->usedAggregatedUserStorageSpace = $this->getContentObjectService()->getUsedStorageSpace();
        }

        return $this->usedAggregatedUserStorageSpace;
    }

    public function getUsedStorageSpaceForUser(User $user): int
    {
        if (!isset($this->usedStorageSpaceForUserCache[$user->getId()]))
        {
            $this->usedStorageSpaceForUserCache[$user->getId()] =
                $this->getContentObjectService()->getUsedStorageSpaceForUser($user);
        }

        return $this->usedStorageSpaceForUserCache[$user->getId()];
    }

    public function getUserService(): UserService
    {
        return $this->userService;
    }

    public function isQuotumDefinedForUser(User $user): bool
    {
        $quotaPolicy = $this->getConfigurationConsulter()->getSetting(['Chamilo\Core\Repository', 'quota_policy']);
        $useQuotaFallback =
            (bool) $this->getConfigurationConsulter()->getSetting(['Chamilo\Core\Repository', 'quota_fallback']);
        $isQuotaFallbackUser = (bool) $this->getConfigurationConsulter()->getSetting(
            ['Chamilo\Core\Repository', 'quota_fallback_user']
        );

        switch ($quotaPolicy)
        {
            case self::POLICY_USER :
                if ($user->get_disk_quota() || !$useQuotaFallback)
                {
                    return true;
                }
                elseif ($isQuotaFallbackUser == 0)
                {
                    $groupQuota = $this->getHighestGroupQuotumForUser($user);

                    return !($groupQuota > $user->get_disk_quota());
                }
                else
                {
                    $groupQuota = $this->getLowestGroupQuotumForUser($user);

                    return !$groupQuota;
                }
            case self::POLICY_GROUP_HIGHEST :
                $groupQuota = $this->getHighestGroupQuotumForUser($user);

                return !($groupQuota || !$useQuotaFallback);
            case self::POLICY_GROUP_LOWEST :
                $groupQuota = $this->getLowestGroupQuotumForUser($user);

                return !($groupQuota || !$useQuotaFallback);
            case self::POLICY_HIGHEST :
                $groupQuota = $this->getHighestGroupQuotumForUser($user);

                return !($groupQuota > $user->get_disk_quota());
            case self::POLICY_LOWEST :
                $groupQuota = $this->getLowestGroupQuotumForUser($user);

                return $groupQuota > $user->get_disk_quota() || !$groupQuota;
            default :
                return true;
        }
    }

    public function isStorageQuotumEnabled(): bool
    {
        return (bool) $this->getConfigurationConsulter()->getSetting(['Chamilo\Core\Repository', 'enable_quota']);
    }
}