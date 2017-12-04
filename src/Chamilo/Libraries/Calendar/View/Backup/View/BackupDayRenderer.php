<?php
namespace Chamilo\Libraries\Calendar\Renderer\Type\Backup\View;

use Chamilo\Libraries\Calendar\Renderer\Event\EventRendererFactory;
use Chamilo\Libraries\Utilities\DatetimeUtilities;

/**
 *
 * @package Chamilo\Libraries\Calendar\Renderer\Type\View
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class BackupDayRenderer extends BackupFullTableRenderer
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
        return $this->getService('chamilo.libraries.calendar.service.table.day_calendar_builder');
    }

    /**
     *
     * @return \Chamilo\Libraries\Calendar\Table\CalendarConfiguration
     */
    protected function getCalendarConfiguration()
    {
        return $this->getService('chamilo.libraries.calendar.table.calendar_configuration');
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
            $nextTableDate = strtotime('+1 hour', $tableDate);

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

                    $eventRendererFactory = new EventRendererFactory($this, $event, $configuration);

                    $calendar->addEvent($tableDate, $eventRendererFactory->render());
                }
            }

            $tableDate = $nextTableDate;
        }

        return $calendar->render();
    }

    /**
     *
     * @see \Chamilo\Libraries\Calendar\Renderer\Type\View\FullRenderer::renderTitle()
     */
    public function renderTitle()
    {
        return DatetimeUtilities::format_locale_date('%A %d %B %Y', $this->getDisplayTime());
    }

    /**
     *
     * @see \Chamilo\Libraries\Calendar\Renderer\Type\View\FullTableRenderer::getPreviousDisplayTime()
     */
    public function getPreviousDisplayTime()
    {
        return strtotime('-1 Day', $this->getDisplayTime());
    }

    /**
     *
     * @see \Chamilo\Libraries\Calendar\Renderer\Type\View\FullTableRenderer::getNextDisplayTime()
     */
    public function getNextDisplayTime()
    {
        return strtotime('+1 Day', $this->getDisplayTime());
    }
}
