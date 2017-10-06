<?php
namespace Chamilo\Core\Admin;

use Chamilo\Configuration\Configuration;
use Chamilo\Configuration\Storage\DataClass\Registration;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Mail\Mailer\MailerFactory;
use Chamilo\Libraries\Platform\Translation;

/**
 * Simple connector class to facilitate rendering settings forms by preprocessing data from the datamanagers to a simple
 * array format.
 *
 * @author Hans De Bisschop
 */
class SettingsConnector
{

    public static function get_languages()
    {
        return \Chamilo\Configuration\Configuration::getInstance()->getLanguages();
    }

    public static function get_themes()
    {
        $options = Theme::getInstance()->getAvailableThemes();

        return $options;
    }

    public static function getMailers()
    {
        $mailerFactory = new MailerFactory(Configuration::getInstance());
        return $mailerFactory->getAvailableMailers();
    }

    public static function get_time_zones()
    {
        $content = file_get_contents(__DIR__ . '/timezones.txt');
        $content = explode("\n", $content);

        $timezones = array();

        foreach ($content as $timezone)
        {
            $timezone = trim($timezone);
            $timezones[$timezone] = $timezone;
        }

        return $timezones;
    }

    public static function get_active_applications()
    {
        $registrations = \Chamilo\Configuration\Configuration::registrations_by_type(Registration::TYPE_APPLICATION);

        $options = array();
        $options['Chamilo\Core\Home'] = Translation::get('Homepage', array(), 'Chamilo\Core\Home');

        foreach ($registrations as $registration)
        {
            if ($registration[Registration::PROPERTY_STATUS])
            {
                $options[$registration[Registration::PROPERTY_CONTEXT]] = Translation::get(
                    'TypeName',
                    null,
                    $registration[Registration::PROPERTY_CONTEXT]);
            }
        }

        asort($options);

        return $options;
    }

    public static function get_working_hours()
    {
        $start = 0;
        $end = 24;
        $working_hours = array();

        for ($i = $start; $i <= $end; $i ++)
        {
            $working_hours[$i] = $i;
        }

        return $working_hours;
    }

    public static function is_allowed_to_change_platform_language()
    {
        return Configuration::getInstance()->get_setting(
            array(\Chamilo\Core\User\Manager::context(), 'allow_user_change_platform_language')) == 1;
    }

    // support for quick language change
    public static function is_allowed_quick_change_platform_language()
    {
        return self::is_allowed_to_change_platform_language() && Configuration::getInstance()->get_setting(
            array(\Chamilo\Core\User\Manager::context(), 'allow_user_quick_change_platform_language')) == 1;
    }

    public static function is_allowed_to_change_platform_timezone()
    {
        return Configuration::getInstance()->get_setting(
            array(\Chamilo\Core\User\Manager::context(), 'allow_user_change_platform_timezone')) == 1;
    }

    public static function is_allowed_to_change_theme()
    {
        return Configuration::getInstance()->get_setting(
            array(\Chamilo\Core\User\Manager::context(), 'allow_user_theme_selection')) == 1;
    }
}
