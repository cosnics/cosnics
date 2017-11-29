<?php
namespace Chamilo\Libraries\Calendar\Renderer\Type\View;

use Chamilo\Libraries\Calendar\HtmlTable\WeekCalendar;
use Chamilo\Libraries\Calendar\Renderer\Event\EventRendererFactory;

/**
 *
 * @package Chamilo\Libraries\Calendar\Renderer\Type\View
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class WeekRenderer extends HtmlTableRenderer
{

    /**
     *
     * @return \Chamilo\Libraries\Calendar\HtmlTable\WeekCalendar
     */
    public function initializeCalendar()
    {
        $displayParameters = $this->getDataProvider()->getDisplayParameters();
        $displayParameters[self::PARAM_TIME] = WeekCalendar::TIME_PLACEHOLDER;
        $displayParameters[self::PARAM_TYPE] = self::TYPE_DAY;

        return $this->getWeekCalendarBuilder()->buildCalendar(
            $this->getDisplayTime(),
            $displayParameters,
            array('table-calendar-week'));
    }

    /**
     *
     * @return \Chamilo\Libraries\Calendar\Service\HtmlTable\WeekCalendarBuilder
     */
    protected function getWeekCalendarBuilder()
    {
        return $this->getService('chamilo.libraries.calendar.service.html_table.week_calendar_builder');
    }

    /**
     *
     * @return \Chamilo\Libraries\Calendar\CalendarConfiguration
     */
    protected function getCalendarConfiguration()
    {
        return $this->getService('chamilo.libraries.calendar.calendar_configuration');
    }

    public function render()
    {
        $calendarConfiguration = $this->getCalendarConfiguration();
        $calendar = $this->getCalendar();

        $fromDate = strtotime('Last Monday', strtotime('+1 Day', strtotime(date('Y-m-d', $this->getDisplayTime()))));
        $toDate = strtotime('-1 Second', strtotime('Next Week', $fromDate));

        $events = $this->getEvents($fromDate, $toDate);

        $startTime = $calendar->getStartTime();
        $endTime = $toDate;

        $tableDate = $startTime;

        while ($tableDate <= $endTime)
        {
            $nextTableDate = strtotime('+' . $calendarConfiguration->getHourStep() . ' Hours', $tableDate);

            foreach ($events as $index => $event)
            {
                $startDate = $event->getStartDate();
                $endDate = $event->getEndDate();

                if ($tableDate < $startDate && $startDate < $nextTableDate ||
                     $tableDate < $endDate && $endDate <= $nextTableDate ||
                     $startDate <= $tableDate && $nextTableDate <= $endDate)
                {
                    $configuration = new \Chamilo\Libraries\Calendar\Renderer\Event\Configuration();
                    $configuration->setStartDate($tableDate);
                    $configuration->setHourStep($calendarConfiguration->getHourStep());

                    $eventRendererFactory = new EventRendererFactory($this, $event, $configuration);

                    $calendar->addEvent($tableDate, $eventRendererFactory->render());
                }
            }
            $tableDate = $nextTableDate;
        }

        return $calendar->render();
    }
}
