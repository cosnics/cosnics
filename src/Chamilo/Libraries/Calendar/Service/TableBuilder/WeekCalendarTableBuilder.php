<?php
namespace Chamilo\Libraries\Calendar\Service\TableBuilder;

use Chamilo\Libraries\Calendar\Architecture\Domain\CalendarTableConfiguration;
use Chamilo\Libraries\Service\Utilities\StringUtilities;
use Exception;
use HTML_Table;

/**
 * @package Chamilo\Libraries\Calendar\Service\TableBuilder
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class WeekCalendarTableBuilder extends CalendarTableBuilder
{
    public const string TIME_PLACEHOLDER = '__TIME__';

    protected function addEvents(
        CalendarTableConfiguration $calendarTableConfiguration, int $displayTime, HTML_Table $table, array $cellMapping,
        array $events
    ): void
    {
        $workingStart = $calendarTableConfiguration->getStartHour();
        $workingEnd = $calendarTableConfiguration->getEndHour();
        $hide = $calendarTableConfiguration->getHideNonWorkingHours();
        $start = 0;
        $end = 24;

        if ($hide) {
            $start = $workingStart;
            $end = $workingEnd;
        }

        foreach ($events as $time => $items) {
            $row = (date('H', $time) / $calendarTableConfiguration->getHourStep()) - $start;

            if ($row > $end - $start - 1) {
                continue;
            }

            $column = date('w', $time);

            if ($column == 0) {
                $column = 7;
            }

            foreach ($items as $item) {
                try {
                    $cellContent = $table->getCellContents($row, $column);
                    $cellContent .= $item;
                    $table->setCellContents($row, $column, $cellContent);
                }
                catch (Exception) {
                }
            }
        }
    }

    /**
     * @throws \TableException
     */
    protected function buildTable(
        CalendarTableConfiguration $calendarTableConfiguration, HTML_Table $table, int $displayTime,
        ?string $dayUrlTemplate = null
    ): array
    {
        $header = $table->getHeader();
        $header->setRowType(0, 'th');
        $header->setHeaderContents(0, 0, '');
        $header->setCellAttributes(0, 0, ['class' => 'table-calendar-week-hours']);

        // Go 1 week back end them jump to the next monday to reach the first day of this week
        $firstDay = $this->getTableStartTime($calendarTableConfiguration, $displayTime);

        $workingStart = $calendarTableConfiguration->getStartHour();
        $workingEnd = $calendarTableConfiguration->getEndHour();
        $hide = $calendarTableConfiguration->getHideNonWorkingHours();
        $start = 0;
        $end = 24;

        if ($hide) {
            $start = $workingStart;
            $end = $workingEnd;
        }

        for ($hour = $start; $hour < $end; $hour += $calendarTableConfiguration->getHourStep()) {
            $rowId = ($hour / $calendarTableConfiguration->getHourStep()) - $start;
            $cellContent = str_pad((string) $hour, 2, '0', STR_PAD_LEFT);
            $table->setCellContents($rowId, 0, $cellContent);

            $classes = [];

            $classes[] = 'table-calendar-week-hours';

            if ($hour % 2 == 0) {
                $classes[] = 'table-calendar-alternate';
            }

            $table->setCellAttributes($rowId, 0, ['class' => $classes]);
        }

        $today = date('Y-m-d');

        for ($day = 0; $day < 7; $day ++) {
            $weekDayTime = strtotime('+' . $day . ' days', $firstDay);
            $header->setHeaderContents(0, $day + 1, $this->getHeaderContent($weekDayTime, $dayUrlTemplate));

            for ($hour = $start; $hour < $end; $hour += $calendarTableConfiguration->getHourStep()) {
                $row = ($hour / $calendarTableConfiguration->getHourStep()) - $start;

                $classes = $this->determineCellClasses(
                    $calendarTableConfiguration, $today, $weekDayTime, $hour, $workingStart, $workingEnd
                );

                if (count($classes) > 0) {
                    $table->setCellAttributes($row, $day + 1, ['class' => $classes]);
                }

                $table->setCellContents($row, $day + 1, '');
            }
        }

        return [];
    }

    /**
     * @return string[]
     */
    protected function determineCellClasses(
        CalendarTableConfiguration $calendarTableConfiguration, string $today, int $weekDay, int $hour,
        int $workingStart, int $workingEnd
    ): array
    {
        $classes = [];

        if ($today == date('Y-m-d', $weekDay)) {
            if (date('H') >= $hour && date('H') < $hour + $calendarTableConfiguration->getHourStep()) {
                $classes[] = 'table-calendar-highlight';
            }
        }

        // If day of week number is 0 (Sunday) or 6 (Saturday) -> it's a weekend
        if (date('w', $weekDay) % 6 == 0) {
            $classes[] = 'table-calendar-weekend';
        }
        elseif ($hour % 2 == 0) {
            $classes[] = 'table-calendar-alternate';
        }

        if ($hour < $workingStart || $hour >= $workingEnd) {
            $classes[] = 'table-calendar-disabled';
        }

        return $classes;
    }

    public function getDayUrl(int $time, string $dayUrlTemplate): string
    {
        return str_replace(self::TIME_PLACEHOLDER, (string) $time, $dayUrlTemplate);
    }

    protected function getHeaderContent(int $weekDayTime, ?string $dayUrlTemplate = null): string
    {
        $dayLabel =
            $this->getTranslator()->trans(date('l', $weekDayTime) . 'Short', [], StringUtilities::LIBRARIES) . ' ' .
            date('d/m', $weekDayTime);

        if (is_null($dayUrlTemplate)) {
            return $dayLabel;
        }
        else {
            return '<a href="' . $this->getDayUrl($weekDayTime, $dayUrlTemplate) . '">' . $dayLabel . '</a>';
        }
    }

    public function getTableEndTime(CalendarTableConfiguration $calendarTableConfiguration, int $displayTime): int
    {
        if ($calendarTableConfiguration->getFirstDayOfWeek() == 'sunday') {
            return strtotime('Next Saterday', strtotime('-1 Week', $displayTime));
        }

        return strtotime('Next Sunday', $this->getTableStartTime($calendarTableConfiguration, $displayTime));
    }

    public function getTableStartTime(CalendarTableConfiguration $calendarTableConfiguration, int $displayTime): int
    {
        if ($calendarTableConfiguration->getFirstDayOfWeek() == 'sunday') {
            return strtotime('Next Sunday', strtotime('-1 Week', $displayTime));
        }

        return strtotime('Next Monday', strtotime('-1 Week', $displayTime));
    }
}
