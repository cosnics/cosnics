<?php
namespace Chamilo\Core\User\Implementation\Admin;

use Chamilo\Configuration\Service\Consulter\ConfigurationConsulter;
use Chamilo\Core\Admin\Architecture\Interface\SettingsConnectorInterface;
use Chamilo\Core\User\Architecture\Domain\UserPictureProviderCollection;
use Chamilo\Core\User\Manager;
use Chamilo\Libraries\Utilities\DatetimeUtilities;
use IntlDateFormatter;
use Symfony\Component\Translation\Translator;

/**
 * Simple connector class to facilitate rendering settings forms by preprocessing data from the datamanagers to a simple
 * array format.
 *
 * @package Chamilo\Core\User\Service
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class SettingsConnector implements SettingsConnectorInterface
{
    protected ConfigurationConsulter $configurationConsulter;

    protected DatetimeUtilities $datetimeUtilities;

    protected Translator $translator;

    protected UserPictureProviderCollection $userPictureProviderFactory;

    public function __construct(
        UserPictureProviderCollection $userPictureProviderFactory, ConfigurationConsulter $configurationConsulter,
        DatetimeUtilities $datetimeUtilities, Translator $translator
    )
    {
        $this->userPictureProviderFactory = $userPictureProviderFactory;
        $this->configurationConsulter = $configurationConsulter;
        $this->datetimeUtilities = $datetimeUtilities;
        $this->translator = $translator;
    }

    public function getConfigurationConsulter(): ConfigurationConsulter
    {
        return $this->configurationConsulter;
    }

    public function getContext(): string
    {
        return Manager::CONTEXT;
    }

    public function getDateTermsAndConditionsUpdate(): array
    {
        $formattedDate = $this->getDatetimeUtilities()->formatLocaleDate(
            (int) $this->getConfigurationConsulter()->getSetting([Manager::CONTEXT, 'date_terms_and_conditions_update']
            ), IntlDateFormatter::SHORT, IntlDateFormatter::NONE
        );

        return [$formattedDate];
    }

    public function getDatetimeUtilities(): DatetimeUtilities
    {
        return $this->datetimeUtilities;
    }

    public function getTranslator(): Translator
    {
        return $this->translator;
    }

    public function getUserPictureProviderFactory(): UserPictureProviderCollection
    {
        return $this->userPictureProviderFactory;
    }

    /**
     * @return string[]
     */
    public function getUserPictureProviders(): array
    {
        return $this->getUserPictureProviderFactory()->getAvailablePictureProviderTypes();
    }
}
