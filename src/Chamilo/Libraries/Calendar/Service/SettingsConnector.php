<?php
namespace Chamilo\Libraries\Calendar\Service;

use Chamilo\Configuration\Service\Consulter\ConfigurationConsulter;
use Chamilo\Core\Admin\Service\SettingsConnectorInterface;
use Chamilo\Core\User\Manager;
use DateTimeZone;

/**
 * @package Chamilo\Libraries\Calendar
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class SettingsConnector implements SettingsConnectorInterface
{

    protected ConfigurationConsulter $configurationConsulter;

    public function __construct(ConfigurationConsulter $configurationConsulter)
    {
        $this->configurationConsulter = $configurationConsulter;
    }

    public function getConfigurationConsulter(): ConfigurationConsulter
    {
        return $this->configurationConsulter;
    }

    public function getContext(): string
    {
        return 'Chamilo\Libraries\Calendar';
    }

    /**
     * @return string[]
     */
    public static function getTimeZones(): array
    {
        $timezones = [];
        $timezoneIdentifiers = DateTimeZone::listIdentifiers();

        foreach ($timezoneIdentifiers as $timezoneIdentifier)
        {
            $timezones[$timezoneIdentifier] = $timezoneIdentifier;
        }

        return $timezones;
    }

    public function isAllowedToChangePlatformTimezone(): bool
    {
        return $this->getConfigurationConsulter()->getSetting(
                [Manager::CONTEXT, 'allow_user_change_platform_timezone']
            ) == 1;
    }
}
