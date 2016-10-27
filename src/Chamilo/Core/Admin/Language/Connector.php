<?php
namespace Chamilo\Core\Admin\Language;

use Chamilo\Configuration\Storage\DataClass\Registration;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Configuration\PlatformSetting;
use Chamilo\Libraries\Platform\Translation;

/**
 * Simple connector class to facilitate rendering settings forms by preprocessing data from the datamanagers to a simple
 * array format.
 *
 * @author Hans De Bisschop
 * @package admin.settings $Id: settings_admin_connector.class.php 168 2009-11-12 11:53:23Z vanpouckesven $
 */
class SettingsConnector
{

    public static function get_languages()
    {
        return \Chamilo\Configuration\Configuration :: get_instance()->getLanguages();
    }

    public static function get_themes()
    {
        $options = Theme :: getInstance()->getAvailableThemes();

        return $options;
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
        $registrations = \Chamilo\Configuration\Configuration :: registrations_by_type(Registration :: TYPE_APPLICATION);

        $options = array();
        $options['home'] = Translation :: get('Homepage', array(), 'home');

        foreach ($registrations as $registration)
        {
            if ($registration[Registration :: PROPERTY_STATUS])
            {
                $options[$registration[Registration :: PROPERTY_NAME]] = Translation :: get(
                    'TypeName',
                    null,
                    $registration[Registration :: PROPERTY_CONTEXT]);
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
        return PlatformSetting :: get('allow_user_change_platform_language', \Chamilo\Core\User\Manager :: context()) ==
             1;
    }

    // support for quick language change
    public static function is_allowed_quick_change_platform_language()
    {
        return self :: is_allowed_to_change_platform_language() && PlatformSetting :: get(
            'allow_user_quick_change_platform_language',
            \Chamilo\Core\User\Manager :: context()) == 1;
    }

    public static function is_allowed_to_change_platform_timezone()
    {
        return PlatformSetting :: get('allow_user_change_platform_timezone', \Chamilo\Core\User\Manager :: context()) ==
             1;
    }

    public static function is_allowed_to_change_theme()
    {
        return PlatformSetting :: get('allow_user_theme_selection', \Chamilo\Core\User\Manager :: context()) == 1;
    }
}
