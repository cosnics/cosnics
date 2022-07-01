<?php
namespace Chamilo\Libraries\Calendar\Service\View;

use Chamilo\Libraries\Calendar\Service\Event\Configuration;
use Chamilo\Libraries\Calendar\Service\Event\EventDayRenderer;
use Chamilo\Libraries\Calendar\Service\View\Table\CalendarTable;
use Chamilo\Libraries\Calendar\Service\View\Table\DayCalendarTable;
use Chamilo\Libraries\Utilities\DatetimeUtilities;

/**
 *
 * @package Chamilo\Libraries\Calendar\Renderer\Type\View
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class DayCalendarRenderer extends HourBasedTableCalendarRenderer
{

    public function getNextDisplayTime(): int
    {
        return strtotime('+1 Day', $this->getDisplayTime());
    }

    public function getPreviousDisplayTime(): int
    {
        return strtotime('-1 Day', $this->getDisplayTime());
    }

    public function initializeCalendar(): CalendarTable
    {
        return new DayCalendarTable(
            $this->getDisplayTime(), $this->getHourStep(), $this->getStartHour(), $this->getEndHour(),
            $this->getHideOtherHours(), ['table-calendar-day']
        );
    }

    /**
     * @throws \Exception
     */
    public function renderFullCalendar(): string
    {
        $calendar = $this->getCalendar();

        $fromDate = $calendar->getStartTime();
        $toDate = $calendar->getEndTime();

        $events = $this->getEvents($fromDate, $toDate);

        $startTime = $calendar->getStartTime();
        $endTime = $calendar->getEndTime();
        $tableDate = $startTime;

        while ($tableDate <= $endTime)
        {
            $nextTableDate = strtotime('+' . $this->getHourStep() . ' Hours', $tableDate);

            foreach ($events as $event)
            {
                $startDate = $event->getStartDate();
                $endDate = $event->getEndDate();

                if ($tableDate < $startDate && $startDate < $nextTableDate ||
                    $tableDate < $endDate && $endDate < $nextTableDate ||
                    $startDate <= $tableDate && $nextTableDate <= $endDate)
                {
                    $configuration = new Configuration();
                    $configuration->setStartDate($tableDate);
                    $configuration->setHourStep($this->getHourStep());

                    $eventRendererFactory = new EventDayRenderer($this, $event, $configuration);

                    $calendar->addEvent($tableDate, $eventRendererFactory->render());
                }
            }

            $tableDate = $nextTableDate;
        }

        return $calendar->render();
    }

    /**
     * @throws \Exception
     */
    public function renderTitle(): string
    {
        return DatetimeUtilities::getInstance()->formatLocaleDate('%A %d %B %Y', $this->getDisplayTime());
    }
}
