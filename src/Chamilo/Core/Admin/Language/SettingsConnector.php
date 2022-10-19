<?php
namespace Chamilo\Core\Admin\Language;

use Chamilo\Configuration\Configuration;
use Chamilo\Configuration\Storage\DataClass\Registration;
use Chamilo\Libraries\DependencyInjection\DependencyInjectionContainerBuilder;
use Chamilo\Libraries\Format\Theme\ThemePathBuilder;
use Chamilo\Libraries\Translation\Translation;

/**
 * Simple connector class to facilitate rendering settings forms by preprocessing data from the datamanagers to a simple
 * array format.
 *
 * @author Hans De Bisschop
 * @package admin.settings
 */
class SettingsConnector
{

    /**
     * @return \Chamilo\Libraries\Format\Theme\ThemePathBuilder
     */
    public static function getThemePathBuilder()
    {
        return DependencyInjectionContainerBuilder::getInstance()->createContainer()->get(ThemePathBuilder::class);
    }

    public static function get_active_applications()
    {
        $registrations = Configuration::registrations_by_type(Registration::TYPE_APPLICATION);

        $options = [];
        $options['home'] = Translation::get('Homepage', [], 'home');

        foreach ($registrations as $registration)
        {
            if ($registration[Registration::PROPERTY_STATUS])
            {
                $options[$registration[Registration::PROPERTY_NAME]] = Translation::get(
                    'TypeName', null, $registration[Registration::PROPERTY_CONTEXT]
                );
            }
        }

        asort($options);

        return $options;
    }

    public static function get_languages()
    {
        return Configuration::getInstance()->getLanguages();
    }

    public static function get_themes()
    {
        return self::getThemePathBuilder()->getAvailableThemes();
    }

    public static function get_working_hours()
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

    public static function is_allowed_quick_change_platform_language()
    {
        return self::is_allowed_to_change_platform_language() && Configuration::getInstance()->get_setting(
                array(\Chamilo\Core\User\Manager::context(), 'allow_user_quick_change_platform_language')
            ) == 1;
    }

    // support for quick language change

    public static function is_allowed_to_change_platform_language()
    {
        return Configuration::getInstance()->get_setting(
                array(\Chamilo\Core\User\Manager::context(), 'allow_user_change_platform_language')
            ) == 1;
    }

    public static function is_allowed_to_change_platform_timezone()
    {
        return Configuration::getInstance()->get_setting(
                array(\Chamilo\Core\User\Manager::context(), 'allow_user_change_platform_timezone')
            ) == 1;
    }

    public static function is_allowed_to_change_theme()
    {
        return Configuration::getInstance()->get_setting(
                array(\Chamilo\Core\User\Manager::context(), 'allow_user_theme_selection')
            ) == 1;
    }
}