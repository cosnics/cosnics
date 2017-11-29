<?php
namespace Chamilo\Libraries\Calendar\Renderer\Type;

use Chamilo\Libraries\Calendar\Renderer\Event\EventRendererFactory;

/**
 *
 * @package Chamilo\Libraries\Calendar\Renderer\Type
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class DayRenderer extends HtmlTableRenderer
{

    /**
     *
     * @return \Chamilo\Libraries\Calendar\HtmlTable\DayCalendar
     */
    public function initializeCalendar()
    {
        return $this->getDayCalendarBuilder()->buildCalendar($this->getDisplayTime(), [], array('table-calendar-day'));
    }

    /**
     *
     * @return \Chamilo\Libraries\Calendar\Service\HtmlTable\DayCalendarBuilder
     */
    protected function getDayCalendarBuilder()
    {
        return $this->getService('chamilo.libraries.calendar.service.html_table.day_calendar_builder');
    }

    /**
     *
     * @see \Chamilo\Libraries\Calendar\Renderer\Renderer::render()
     */
    public function render()
    {
        $calendarConfiguration = $this->getCalendarConfiguration();
        $calendar = $this->getCalendar();

        $events = $this->getEvents($calendar->getStartTime(), $calendar->getEndTime());

        $startTime = $calendar->getStartTime();
        $endTime = $calendar->getEndTime();
        $tableDate = $startTime;

        while ($tableDate <= $endTime)
        {
            $nextTableDate = strtotime('+' . $calendarConfiguration->getHourStep() . ' Hours', $tableDate);

            foreach ($events as $index => $event)
            {
                $startDate = $event->getStartDate();
                $endDate = $event->getEndDate();

                if ($tableDate < $startDate && $startDate < $nextTableDate ||
                     $tableDate < $endDate && $endDate < $nextTableDate ||
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
