<?php
namespace Chamilo\Core\Repository\Quota\Service;

use Chamilo\Configuration\Service\Consulter\ConfigurationConsulter;
use Chamilo\Core\Repository\Filter\FilterData;
use Chamilo\Core\Repository\Quota\Manager;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Application\Routing\UrlGenerator;
use Chamilo\Libraries\File\ConfigurablePathBuilder;
use Chamilo\Libraries\File\Filesystem;
use Chamilo\Libraries\Format\Form\FormValidator;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Core\Repository\Quota\Service
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class StorageSpaceCalculator implements UserStorageSpaceCalculatorInterface
{
    protected AggregatedUserStorageSpaceCalculatorInterface $aggregatedUserStorageSpaceCalculator;

    protected ConfigurablePathBuilder $configurablePathBuilder;

    protected ConfigurationConsulter $configurationConsulter;

    protected Translator $translator;

    protected UrlGenerator $urlGenerator;

    protected UserStorageSpaceCalculatorInterface $userStorageSpaceCalculator;

    public function __construct(
        ConfigurationConsulter $configurationConsulter, Translator $translator,
        ConfigurablePathBuilder $configurablePathBuilder, UrlGenerator $urlGenerator,
        UserStorageSpaceCalculatorInterface $allowedUserStorageCalculator,
        CachedAggregatedUserStorageSpaceCalculator $maximumAggregatedUserStorageSpaceCacheDataLoader
    )
    {
        $this->configurationConsulter = $configurationConsulter;
        $this->translator = $translator;
        $this->configurablePathBuilder = $configurablePathBuilder;
        $this->urlGenerator = $urlGenerator;
        $this->userStorageSpaceCalculator = $allowedUserStorageCalculator;
        $this->aggregatedUserStorageSpaceCalculator = $maximumAggregatedUserStorageSpaceCacheDataLoader;
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

    public function doesUserHaveRequestedStorageSpace(User $user, int $requestedStorageSpace): bool
    {
        if (!$this->isStorageQuotumEnabled())
        {
            return true;
        }

        return $this->getAvailableStorageSpaceForUser($user) > $requestedStorageSpace;
    }

    public function getAggregatedUserStorageSpaceCalculator(): AggregatedUserStorageSpaceCalculatorInterface
    {
        return $this->aggregatedUserStorageSpaceCalculator;
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
        return $this->getUserStorageSpaceCalculator()->getAllowedStorageSpaceForUser($user);
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
        return $this->getUserStorageSpaceCalculator()->getAvailableStorageSpaceForUser($user);
    }

    public function getConfigurablePathBuilder(): ConfigurablePathBuilder
    {
        return $this->configurablePathBuilder;
    }

    public function getConfigurationConsulter(): ConfigurationConsulter
    {
        return $this->configurationConsulter;
    }

    public function getMaximumAggregatedUserStorageSpace(): int
    {
        return $this->getAggregatedUserStorageSpaceCalculator()->getMaximumAggregatedUserStorageSpace();
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
        return $this->getUserStorageSpaceCalculator()->getUsedAggregatedUserStorageSpace();
    }

    public function getUsedStorageSpaceForUser(User $user): int
    {
        return $this->getUserStorageSpaceCalculator()->getUsedStorageSpaceForUser($user);
    }

    public function getUserStorageSpaceCalculator(): UserStorageSpaceCalculatorInterface
    {
        return $this->userStorageSpaceCalculator;
    }

    public function isQuotumDefinedForUser(User $user): bool
    {
        return $this->getUserStorageSpaceCalculator()->isQuotumDefinedForUser($user);
    }

    public function isStorageQuotumEnabled(): bool
    {
        return (bool) $this->getConfigurationConsulter()->getSetting(['Chamilo\Core\Repository', 'enable_quota']);
    }
}