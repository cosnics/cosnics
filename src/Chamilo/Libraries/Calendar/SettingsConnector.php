<?php
namespace Chamilo\Libraries\Calendar;

use Chamilo\Configuration\Configuration;

/**
 *
 * @package Chamilo\Libraries\Calendar
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class SettingsConnector
{

    /**
     *
     * @return string[]
     */
    public static function get_time_zones()
    {
        $timezones = array();
        $timezoneIdentifiers = \DateTimeZone::listIdentifiers();
        
        foreach ($timezoneIdentifiers as $timezoneIdentifier)
        {
            $timezones[$timezoneIdentifier] = $timezoneIdentifier;
        }
        
        return $timezones;
    }

    /**
     *
     * @return boolean
     */
    public static function is_allowed_to_change_platform_timezone()
    {
        return Configuration::getInstance()->get_setting(
            array(\Chamilo\Core\User\Manager::context(), 'allow_user_change_platform_timezone')) == 1;
    }
}
