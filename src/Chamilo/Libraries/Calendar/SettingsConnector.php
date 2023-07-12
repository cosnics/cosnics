<?php
namespace Chamilo\Libraries\Calendar;

use Chamilo\Configuration\Service\Consulter\ConfigurationConsulter;
use Chamilo\Core\User\Manager;
use Chamilo\Libraries\DependencyInjection\DependencyInjectionContainerBuilder;
use DateTimeZone;

/**
 * @package Chamilo\Libraries\Calendar
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class SettingsConnector
{

    /**
     * @return string[]
     */
    public static function get_time_zones(): array
    {
        $timezones = [];
        $timezoneIdentifiers = DateTimeZone::listIdentifiers();

        foreach ($timezoneIdentifiers as $timezoneIdentifier)
        {
            $timezones[$timezoneIdentifier] = $timezoneIdentifier;
        }

        return $timezones;
    }

    /**
     * @throws \ReflectionException
     */
    public static function is_allowed_to_change_platform_timezone(): bool
    {
        /**
         * @var \Chamilo\Configuration\Service\Consulter\ConfigurationConsulter $configurationConsulter
         */
        $configurationConsulter =
            DependencyInjectionContainerBuilder::getInstance()->createContainer()->get(ConfigurationConsulter::class);

        return $configurationConsulter->getSetting(
                [Manager::CONTEXT, 'allow_user_change_platform_timezone']
            ) == 1;
    }
}
