<?php
namespace Chamilo\Libraries\Calendar\Service\View;

use Chamilo\Libraries\Calendar\Service\Event\Configuration;
use Chamilo\Libraries\Calendar\Service\Event\EventWeekRenderer;
use Chamilo\Libraries\Calendar\Service\View\Table\CalendarTable;
use Chamilo\Libraries\Calendar\Service\View\Table\WeekCalendarTable;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\DatetimeUtilities;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 * @package Chamilo\Libraries\Calendar\Service\View
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class WeekCalendarRenderer extends HourBasedTableCalendarRenderer
{

    public function getNextDisplayTime(): int
    {
        return strtotime('+1 Week', $this->getDisplayTime());
    }

    public function getPreviousDisplayTime(): int
    {
        return strtotime('-1 Week', $this->getDisplayTime());
    }

    public function initializeCalendar(): CalendarTable
    {
        $displayParameters = $this->getDataProvider()->getDisplayParameters();
        $displayParameters[self::PARAM_TIME] = WeekCalendarTable::TIME_PLACEHOLDER;
        $displayParameters[self::PARAM_TYPE] = self::TYPE_DAY;
        $dayUrlTemplate = new Redirect($displayParameters);

        return new WeekCalendarTable(
            $this->getDisplayTime(), $dayUrlTemplate->getUrl(), $this->getHourStep(), $this->getStartHour(),
            $this->getEndHour(), $this->getHideOtherHours(), ['table-calendar-week']
        );
    }

    /**
     * @throws \ReflectionException
     * @throws \Exception
     */
    public function renderFullCalendar(): string
    {
        $calendar = $this->getCalendar();
        $fromDate = strtotime('Last Monday', strtotime('+1 Day', strtotime(date('Y-m-d', $this->getDisplayTime()))));
        $toDate = strtotime('-1 Second', strtotime('Next Week', $fromDate));

        $events = $this->getEvents($fromDate, $toDate);

        $startTime = $calendar->getStartTime();
        $endTime = $toDate;

        $tableDate = $startTime;

        while ($tableDate <= $endTime)
        {
            $nextTableDate = strtotime('+' . $calendar->getHourStep() . ' Hours', $tableDate);

            foreach ($events as $event)
            {
                $startDate = $event->getStartDate();
                $endDate = $event->getEndDate();

                if ($tableDate < $startDate && $startDate < $nextTableDate ||
                    $tableDate < $endDate && $endDate <= $nextTableDate ||
                    $startDate <= $tableDate && $nextTableDate <= $endDate)
                {
                    $configuration = new Configuration();
                    $configuration->setStartDate($tableDate);
                    $configuration->setHourStep($calendar->getHourStep());

                    $eventRendererFactory = new EventWeekRenderer($this, $event, $configuration);

                    $calendar->addEvent($tableDate, $eventRendererFactory->render());
                }
            }

            $tableDate = $nextTableDate;
        }

        return $calendar->render();
    }

    /**
     * @throws \ReflectionException
     * @throws \Exception
     */
    public function renderTitle(): string
    {
        $weekNumber = date('W', $this->getDisplayTime());

        return Translation::get('Week', null, StringUtilities::LIBRARIES) . ' ' . $weekNumber . ' : ' .
            DatetimeUtilities::getInstance()->formatLocaleDate('%A %d %B %Y', $this->getCalendar()->getStartTime()) .
            ' - ' . DatetimeUtilities::getInstance()->formatLocaleDate(
                '%A %d %B %Y', strtotime('+6 Days', $this->getCalendar()->getStartTime())
            );
    }
}
