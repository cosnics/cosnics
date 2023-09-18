<?php
namespace Chamilo\Core\Admin\Service;

use Chamilo\Configuration\Service\Consulter\ConfigurationConsulter;
use Chamilo\Configuration\Service\Consulter\LanguageConsulter;
use Chamilo\Configuration\Service\Consulter\RegistrationConsulter;
use Chamilo\Configuration\Storage\DataClass\Registration;
use Chamilo\Core\Admin\Manager;
use Chamilo\Libraries\Format\Theme\ThemePathBuilder;
use Chamilo\Libraries\Mail\Mailer\MailerFactory;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Core\Admin\Service
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class SettingsConnector implements SettingsConnectorInterface
{

    protected ConfigurationConsulter $configurationConsulter;

    protected LanguageConsulter $languageConsulter;

    protected MailerFactory $mailerFactory;

    protected RegistrationConsulter $registrationConsulter;

    protected ThemePathBuilder $themeSystemPathBuilder;

    protected Translator $translator;

    public function __construct(
        ConfigurationConsulter $configurationConsulter, LanguageConsulter $languageConsulter,
        MailerFactory $mailerFactory, RegistrationConsulter $registrationConsulter,
        ThemePathBuilder $themeSystemPathBuilder, Translator $translator
    )
    {
        $this->configurationConsulter = $configurationConsulter;
        $this->languageConsulter = $languageConsulter;
        $this->mailerFactory = $mailerFactory;
        $this->registrationConsulter = $registrationConsulter;
        $this->themeSystemPathBuilder = $themeSystemPathBuilder;
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

    public function getLanguageConsulter(): LanguageConsulter
    {
        return $this->languageConsulter;
    }

    public function getMailerFactory(): MailerFactory
    {
        return $this->mailerFactory;
    }

    /**
     * @return string[]
     * @throws \Symfony\Component\Cache\Exception\CacheException
     */
    public function getMailers(): array
    {
        return $this->getMailerFactory()->getAvailableMailers();
    }

    public function getRegistrationConsulter(): RegistrationConsulter
    {
        return $this->registrationConsulter;
    }

    public function getThemeSystemPathBuilder(): ThemePathBuilder
    {
        return $this->themeSystemPathBuilder;
    }

    public function getTranslator(): Translator
    {
        return $this->translator;
    }

    /**
     * @throws \Symfony\Component\Cache\Exception\CacheException
     */
    public function getActiveApplications(): array
    {
        $registrations = $this->getRegistrationConsulter()->getRegistrationsByType(Registration::TYPE_APPLICATION);
        $translator = $this->getTranslator();

        $options = [];
        $options['Chamilo\Core\Home'] = $translator->trans('Homepage', [], 'Chamilo\Core\Home');

        foreach ($registrations as $registration)
        {
            if ($registration[Registration::PROPERTY_STATUS])
            {
                $options[$registration[Registration::PROPERTY_CONTEXT]] = $translator->trans(
                    'TypeName', [], $registration[Registration::PROPERTY_CONTEXT]
                );
            }
        }

        asort($options);

        return $options;
    }

    /**
     * @return string[]
     */
    public function getLanguages(): array
    {
        return $this->getLanguageConsulter()->getLanguages();
    }

    /**
     * @return string[]
     */
    public function getThemes(): array
    {
        return $this->getThemeSystemPathBuilder()->getAvailableThemes();
    }

    /**
     * @return int[]
     */
    public function getWorkingHours(): array
    {
        $start = 0;
        $end = 24;
        $working_hours = [];

        for ($i = $start; $i <= $end; $i ++)
        {
            $working_hours[$i] = $i;
        }

        return $working_hours;
    }

    public function isAllowedQuickChangePlatformLanguage(): bool
    {
        return $this->isAllowedToChangePlatformLanguage() && $this->getConfigurationConsulter()->getSetting(
                [\Chamilo\Core\User\Manager::CONTEXT, 'allow_user_quick_change_platform_language']
            ) == 1;
    }

    public function isAllowedToChangePlatformLanguage(): bool
    {
        return $this->getConfigurationConsulter()->getSetting(
                [\Chamilo\Core\User\Manager::CONTEXT, 'allow_user_change_platform_language']
            ) == 1;
    }

    public function isAllowedToChangePlatformTimezone(): bool
    {
        return $this->getConfigurationConsulter()->getSetting(
                [\Chamilo\Core\User\Manager::CONTEXT, 'allow_user_change_platform_timezone']
            ) == 1;
    }

    public function isAllowedToChangeTheme(): bool
    {
        return $this->getConfigurationConsulter()->getSetting(
                [\Chamilo\Core\User\Manager::CONTEXT, 'allow_user_theme_selection']
            ) == 1;
    }
}
