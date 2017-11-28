<?php
namespace Chamilo\Libraries\Calendar\Service;

use Chamilo\Libraries\Platform\Configuration\LocalSetting;
use Chamilo\Libraries\Calendar\Table\CalendarConfiguration;

class CalendarConfigurationFactory
{

    /**
     *
     * @var \Chamilo\Libraries\Platform\Configuration\LocalSetting
     */
    private $localSettingConsulter;

    /**
     *
     * @param \Chamilo\Libraries\Platform\Configuration\LocalSetting $localSettingConsulter
     */
    public function __construct(LocalSetting $localSettingConsulter)
    {
        $this->localSettingConsulter = $localSettingConsulter;
    }

    /**
     *
     * @return \Chamilo\Libraries\Platform\Configuration\LocalSetting
     */
    protected function getLocalSettingConsulter()
    {
        return $this->localSettingConsulter;
    }

    /**
     *
     * @return \Chamilo\Libraries\Calendar\Table\CalendarConfiguration
     */
    public function getCalendarConfiguration()
    {
        $localSettingConsulter = $this->getLocalSettingConsulter();

        return new CalendarConfiguration(
            $localSettingConsulter->get('working_hours_start', 'Chamilo\Libraries\Calendar'),
            $localSettingConsulter->get('working_hours_end', 'Chamilo\Libraries\Calendar'),
            $localSettingConsulter->get('hide_non_working_hours', 'Chamilo\Libraries\Calendar'),
            $localSettingConsulter->get('hour_step', 'Chamilo\Libraries\Calendar'),
            $localSettingConsulter->get('first_day_of_week', 'Chamilo\Libraries\Calendar'));
    }
}

