<?php
namespace Chamilo\Core\Admin;

use Chamilo\Configuration\Service\Consulter\ConfigurationConsulter;
use Chamilo\Configuration\Service\Consulter\LanguageConsulter;
use Chamilo\Configuration\Service\Consulter\RegistrationConsulter;
use Chamilo\Configuration\Storage\DataClass\Registration;
use Chamilo\Libraries\DependencyInjection\DependencyInjectionContainerBuilder;
use Chamilo\Libraries\Format\Theme\ThemePathBuilder;
use Chamilo\Libraries\Mail\Mailer\MailerFactory;
use Symfony\Component\Translation\Translator;

/**
 * Simple connector class to facilitate rendering settings forms by preprocessing data from the datamanagers to a simple
 * array format.
 *
 * @author Hans De Bisschop
 */
class SettingsConnector
{

    public static function getConfigurationConsulter(): ConfigurationConsulter
    {
        return DependencyInjectionContainerBuilder::getInstance()->createContainer()->get(
            ConfigurationConsulter::class
        );
    }

    public static function getLanguageConsulter(): LanguageConsulter
    {
        return DependencyInjectionContainerBuilder::getInstance()->createContainer()->get(
            LanguageConsulter::class
        );
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\ConnectionException
     * @throws \Symfony\Component\Cache\Exception\CacheException
     */
    public static function getMailers()
    {
        $mailerFactory = DependencyInjectionContainerBuilder::getInstance()->createContainer()->get(
            MailerFactory::class
        );

        return $mailerFactory->getAvailableMailers();
    }

    public static function getRegistrationConsulter(): RegistrationConsulter
    {
        return DependencyInjectionContainerBuilder::getInstance()->createContainer()->get(
            RegistrationConsulter::class
        );
    }

    public static function getThemeSystemPathBuilder(): ThemePathBuilder
    {
        return DependencyInjectionContainerBuilder::getInstance()->createContainer()->get(
            'Chamilo\Libraries\Format\Theme\ThemeSystemPathBuilder'
        );
    }

    public static function getTranslator(): Translator
    {
        return DependencyInjectionContainerBuilder::getInstance()->createContainer()->get(
            Translator::class
        );
    }

    public static function get_active_applications(): array
    {
        $registrations = self::getRegistrationConsulter()->getRegistrationsByType(Registration::TYPE_APPLICATION);
        $translator = self::getTranslator();

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
    public static function get_languages(): array
    {
        return self::getLanguageConsulter()->getLanguages();
    }

    /**
     * @return string[]
     */
    public static function get_themes(): array
    {
        return self::getThemeSystemPathBuilder()->getAvailableThemes();
    }

    /**
     * @return int[]
     */
    public static function get_working_hours(): array
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

    public static function is_allowed_quick_change_platform_language(): bool
    {
        return self::is_allowed_to_change_platform_language() && self::getConfigurationConsulter()->getSetting(
                [\Chamilo\Core\User\Manager::CONTEXT, 'allow_user_quick_change_platform_language']
            ) == 1;
    }

    public static function is_allowed_to_change_platform_language(): bool
    {
        return self::getConfigurationConsulter()->getSetting(
                [\Chamilo\Core\User\Manager::CONTEXT, 'allow_user_change_platform_language']
            ) == 1;
    }

    public static function is_allowed_to_change_platform_timezone(): bool
    {
        return self::getConfigurationConsulter()->getSetting(
                [\Chamilo\Core\User\Manager::CONTEXT, 'allow_user_change_platform_timezone']
            ) == 1;
    }

    public static function is_allowed_to_change_theme(): bool
    {
        return self::getConfigurationConsulter()->getSetting(
                [\Chamilo\Core\User\Manager::CONTEXT, 'allow_user_theme_selection']
            ) == 1;
    }
}
