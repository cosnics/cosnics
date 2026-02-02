<?php
namespace Chamilo\Core\Admin\Implementation\Admin;

use Chamilo\Core\Admin\Architecture\Interface\SettingsConnectorInterface;
use Chamilo\Core\Admin\Manager;
use Chamilo\Core\Admin\Service\Consulter\ConfigurationConsulter;
use Chamilo\Core\Admin\Service\Consulter\LanguageConsulter;
use Chamilo\Libraries\Protocol\Mail\Factory\MailerFactory;
use Chamilo\Libraries\UserInterface\Theme\Service\ThemePathBuilder;
use DateTimeZone;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Core\Admin\Implementation\Admin
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class SettingsConnector implements SettingsConnectorInterface
{
    protected ConfigurationConsulter $configurationConsulter;

    protected LanguageConsulter $languageConsulter;

    protected MailerFactory $mailerFactory;

    protected ThemePathBuilder $themeSystemPathBuilder;

    protected Translator $translator;

    public function __construct(
        ConfigurationConsulter $configurationConsulter, LanguageConsulter $languageConsulter,
        MailerFactory $mailerFactory, ThemePathBuilder $themeSystemPathBuilder, Translator $translator
    )
    {
        $this->configurationConsulter = $configurationConsulter;
        $this->languageConsulter = $languageConsulter;
        $this->mailerFactory = $mailerFactory;
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

    /**
     * @return string[]
     */
    public function getLanguages(): array
    {
        return $this->getLanguageConsulter()->getLanguages();
    }

    public function getMailerFactory(): MailerFactory
    {
        return $this->mailerFactory;
    }

    /**
     * @return string[]
     */
    public function getMailers(): array
    {
        return $this->getMailerFactory()->getMailers();
    }

    public function getThemeSystemPathBuilder(): ThemePathBuilder
    {
        return $this->themeSystemPathBuilder;
    }

    /**
     * @return string[]
     */
    public function getThemes(): array
    {
        return $this->getThemeSystemPathBuilder()->getAvailableThemes();
    }

    /**
     * @return string[]
     */
    public static function getTimeZones(): array
    {
        $timezones = [];
        $timezoneIdentifiers = DateTimeZone::listIdentifiers();

        foreach ($timezoneIdentifiers as $timezoneIdentifier) {
            $timezones[$timezoneIdentifier] = $timezoneIdentifier;
        }

        return $timezones;
    }

    public function getTranslator(): Translator
    {
        return $this->translator;
    }

    /**
     * @return int[]
     */
    public function getWorkingHours(): array
    {
        $start = 0;
        $end = 24;
        $workingHours = [];

        for ($i = $start; $i <= $end; $i ++) {
            $workingHours[$i] = $i;
        }

        return $workingHours;
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
