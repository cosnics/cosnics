<?php
namespace Chamilo\Libraries\Calendar\Renderer\Type\View;

use Chamilo\Libraries\Calendar\HtmlTable\MonthCalendar;
use Chamilo\Libraries\Calendar\Renderer\Event\EventRendererFactory;

/**
 *
 * @package Chamilo\Libraries\Calendar\Renderer\Type\View
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class MonthRenderer extends HtmlTableRenderer
{

    /**
     *
     * @return \Chamilo\Libraries\Calendar\HtmlTable\MonthCalendar
     */
    public function initializeCalendar()
    {
        $displayParameters = $this->getDataProvider()->getDisplayParameters();
        $displayParameters[self::PARAM_TIME] = MonthCalendar::TIME_PLACEHOLDER;
        $displayParameters[self::PARAM_TYPE] = self::TYPE_DAY;

        return $this->getMonthCalendarBuilder()->buildCalendar(
            $this->getDisplayTime(),
            $displayParameters,
            array('table-calendar-month'));
    }

    /**
     *
     * @return \Chamilo\Libraries\Calendar\Service\HtmlTable\MonthCalendarBuilder
     */
    protected function getMonthCalendarBuilder()
    {
        return $this->getService('chamilo.libraries.calendar.service.html_table.month_calendar_builder');
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
        $calendar = $this->getCalendar();

        $startTime = $calendar->getStartTime();
        $endTime = $calendar->getEndTime();

        $events = $this->getEvents($startTime, $endTime);
        $tableDate = $startTime;

        while ($tableDate <= $endTime)
        {
            $nextTableDate = strtotime('+1 Day', $tableDate);

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

                    $eventRendererFactory = new EventRendererFactory($this, $event, $configuration);

                    $calendar->addEvent($tableDate, $eventRendererFactory->render());
                }
            }

            $tableDate = $nextTableDate;
        }

        return '<div class="month-calendar">' . $calendar->render() . '</div>';
    }
}
