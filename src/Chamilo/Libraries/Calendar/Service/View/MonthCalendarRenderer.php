<?php
namespace Chamilo\Libraries\Calendar\Service\View;

use Chamilo\Libraries\Calendar\Service\Event\Configuration;
use Chamilo\Libraries\Calendar\Service\Event\EventMonthRenderer;
use Chamilo\Libraries\Calendar\Service\View\Table\CalendarTable;
use Chamilo\Libraries\Calendar\Service\View\Table\MonthCalendarTable;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 *
 * @package Chamilo\Libraries\Calendar\Renderer\Type\View
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class MonthCalendarRenderer extends TableCalendarRenderer
{

    public function getNextDisplayTime(int $displayTime): int
    {
        return strtotime('first day of next month', $displayTime);
    }

    public function getPreviousDisplayTime(int $displayTime): int
    {
        return strtotime('first day of previous month', $displayTime);
    }

    /**
     * @throws \ReflectionException
     */
    public function initializeCalendar(): CalendarTable
    {
        $displayParameters = $this->getDataProvider()->getDisplayParameters();
        $displayParameters[self::PARAM_TIME] = MonthCalendarTable::TIME_PLACEHOLDER;
        $displayParameters[self::PARAM_TYPE] = self::TYPE_DAY;
        $dayUrlTemplate = new Redirect($displayParameters);

        return new MonthCalendarTable($this->getDisplayTime(), $dayUrlTemplate->getUrl(), ['table-calendar-month']);
    }

    /**
     * @throws \ReflectionException
     * @throws \Exception
     */
    public function renderFullCalendar(): string
    {
        $calendar = $this->getCalendar();

        $startTime = $calendar->getStartTime();
        $endTime = $calendar->getEndTime();

        $events = $this->getEvents($startTime, $endTime);
        $tableDate = $startTime;

        while ($tableDate <= $endTime)
        {
            $nextTableDate = strtotime('+1 Day', $tableDate);

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

                    $eventRendererFactory = new EventMonthRenderer($this, $event, $configuration);

                    $calendar->addEvent($tableDate, $eventRendererFactory->render());
                }
            }

            $tableDate = $nextTableDate;
        }

        return '<div class="month-calendar">' . $calendar->render() . '</div>';
    }

    public function renderTitle(): string
    {
        return Translation::get(date('F', $this->getDisplayTime()) . 'Long', null, StringUtilities::LIBRARIES) . ' ' .
            date('Y', $this->getDisplayTime());
    }
}
