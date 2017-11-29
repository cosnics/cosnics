<?php
namespace Chamilo\Libraries\Calendar\Renderer\Type\View;

use Chamilo\Libraries\Calendar\Renderer\Event\EventRendererFactory;

/**
 *
 * @package Chamilo\Libraries\Calendar\Renderer\Type\View
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class DayRenderer extends HtmlTableRenderer
{

    /**
     *
     * @return \Chamilo\Libraries\Calendar\Table\Type\DayCalendar
     */
    public function initializeCalendar()
    {
        return $this->getDayCalendarBuilder()->buildCalendar($this->getDisplayTime(), [], array('table-calendar-day'));
    }

    /**
     *
     * @return \Chamilo\Libraries\Calendar\Service\Table\DayCalendarBuilder
     */
    protected function getDayCalendarBuilder()
    {
        return $this->getService('chamilo.libraries.calendar.service.html_table.day_calendar_builder');
    }

    /**
     *
     * @return \Chamilo\Libraries\Calendar\Table\CalendarConfiguration
     */
    protected function getCalendarConfiguration()
    {
        return $this->getService('chamilo.libraries.calendar.calendar_configuration');
    }

    /**
     *
     * @see \Chamilo\Libraries\Calendar\Renderer\Type\View\FullRenderer::renderFullCalendar()
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
