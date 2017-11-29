<?php
namespace Chamilo\Libraries\Calendar\Format\Renderer\Type;

use Chamilo\Libraries\Calendar\Event\Service\ViewRendererFactory;
use Chamilo\Libraries\Calendar\Format\HtmlTable\WeekCalendar;
use Chamilo\Libraries\Calendar\Format\Renderer\HtmlTableRenderer;

/**
 *
 * @package Chamilo\Libraries\Calendar\Format\Renderer\Type
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
     * @see \Chamilo\Libraries\Calendar\Renderer\Renderer::render()
     */
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
            $nextTableDate = strtotime('+1 hour', $tableDate);

            foreach ($events as $index => $event)
            {
                $startDate = $event->getStartDate();
                $endDate = $event->getEndDate();

                if ($tableDate < $startDate && $startDate < $nextTableDate ||
                     $tableDate < $endDate && $endDate <= $nextTableDate ||
                     $startDate <= $tableDate && $nextTableDate <= $endDate)
                {
                    $eventRendererFactory = new ViewRendererFactory($this, $event, $tableDate);

                    $calendar->addEvent($tableDate, $eventRendererFactory->render());
                }
            }
            $tableDate = $nextTableDate;
        }

        return $calendar->render();
    }
}
