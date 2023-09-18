<?php
namespace Chamilo\Core\User\Service;

use Chamilo\Configuration\Service\Consulter\ConfigurationConsulter;
use Chamilo\Core\Admin\Service\SettingsConnectorInterface;
use Chamilo\Core\User\Manager;
use Chamilo\Core\User\Picture\UserPictureProviderFactory;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Utilities\DatetimeUtilities;
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

    protected UserPictureProviderFactory $userPictureProviderFactory;

    public function __construct(
        UserPictureProviderFactory $userPictureProviderFactory, ConfigurationConsulter $configurationConsulter,
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
        $dateFormat = '%e-%m-%Y';

        $formattedDate = $this->getDatetimeUtilities()->formatLocaleDate(
            $dateFormat,
            (int) $this->getConfigurationConsulter()->getSetting([Manager::CONTEXT, 'date_terms_and_conditions_update'])
        );

        return [$formattedDate];
    }

    public function getDatetimeUtilities(): DatetimeUtilities
    {
        return $this->datetimeUtilities;
    }

    /**
     * @return string[]
     */
    public function getFullnameFormats(): array
    {
        $translator = $this->getTranslator();
        $options = [];

        $options[User::NAME_FORMAT_FIRST] = $translator->trans('FirstName', [], Manager::CONTEXT) . ' ' .
            $translator->trans('LastName', [], Manager::CONTEXT);
        $options[User::NAME_FORMAT_LAST] = $translator->trans('LastName', [], Manager::CONTEXT) . ' ' .
            $translator->trans('FirstName', [], Manager::CONTEXT);

        return $options;
    }

    public function getTranslator(): Translator
    {
        return $this->translator;
    }

    public function getUserPictureProviderFactory(): UserPictureProviderFactory
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
