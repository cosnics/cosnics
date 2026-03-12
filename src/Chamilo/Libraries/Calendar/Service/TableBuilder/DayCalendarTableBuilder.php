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
class DayCalendarTableBuilder extends CalendarTableBuilder
{
    protected function addEvents(
        CalendarTableConfiguration $calendarTableConfiguration, int $displayTime, HTML_Table $table, array $cellMapping,
        array $events
    ): void
    {
        $start = 0;

        if ($calendarTableConfiguration->getHideNonWorkingHours()) {
            $start = $calendarTableConfiguration->getStartHour();
        }

        foreach ($events as $time => $items) {
            if ($time >= $this->getTableEndTime($calendarTableConfiguration, $displayTime)) {
                continue;
            }

            $row = (date('H', $time) - $start) / $calendarTableConfiguration->getHourStep();

            foreach ($items as $item) {
                try {
                    $cellContent = $table->getCellContents($row, 1);
                    $cellContent .= $item;
                    $table->setCellContents($row, 1, $cellContent);
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
        $header->setCellAttributes(0, 0, ['class' => 'table-calendar-day-hours']);

        $header->setHeaderContents(
            0, 1,
            $this->getTranslator()->trans(date('l', $displayTime) . 'Short', [], StringUtilities::LIBRARIES) . ' ' .
            date('d/m', $displayTime)
        );

        $startHour = 0;
        $endHour = 24;

        if ($calendarTableConfiguration->getHideNonWorkingHours()) {
            $startHour = $calendarTableConfiguration->getStartHour();
            $endHour = $calendarTableConfiguration->getEndHour();
        }

        for ($hour = $startHour; $hour < $endHour; $hour += $calendarTableConfiguration->getHourStep()) {
            $rowId = ($hour / $calendarTableConfiguration->getHourStep()) - $startHour;
            $cellContent = str_pad((string) $hour, 2, '0', STR_PAD_LEFT);
            $table->setCellContents($rowId, 0, $cellContent);

            $classes = [];

            $classes[] = 'table-calendar-day-hours';

            if ($hour % 2 == 0) {
                $classes[] = 'table-calendar-alternate';
            }

            $table->setCellAttributes($rowId, 0, ['class' => $classes]);
        }

        for ($hour = $startHour; $hour < $endHour; $hour += $calendarTableConfiguration->getHourStep()) {
            $rowId = ($hour / $calendarTableConfiguration->getHourStep()) - $startHour;

            $table->setCellContents($rowId, 1, '');

            $classes = $this->determineCellClasses($calendarTableConfiguration, $hour, $displayTime);

            if (count($classes) > 0) {
                $table->setCellAttributes($rowId, 1, ['class' => $classes]);
            }
        }

        return [];
    }

    /**
     * @return string[]
     */
    protected function determineCellClasses(
        CalendarTableConfiguration $calendarTableConfiguration, int $hour, int $displayTime
    ): array
    {
        $classes = [];

        // Highlight current hour
        if (date('Y-m-d') == date('Y-m-d', $displayTime)) {
            if (date('H') >= $hour && date('H') < $hour + $calendarTableConfiguration->getHourStep()) {
                $classes[] = 'table-calendar-highlight';
            }
        }

        // Is current table hour during working hours?
        if ($hour < $calendarTableConfiguration->getStartHour() || $hour >= $calendarTableConfiguration->getEndHour()) {
            $classes[] = 'table-calendar-disabled';
        }

        if ($hour % 2 == 0) {
            $classes[] = 'table-calendar-alternate';
        }

        return $classes;
    }

    public function getTableEndTime(CalendarTableConfiguration $calendarTableConfiguration, int $displayTime): int
    {
        if ($calendarTableConfiguration->getHideNonWorkingHours()) {
            return strtotime(date('Y-m-d ' . ($calendarTableConfiguration->getEndHour() - 1) . ':59:59', $displayTime));
        }

        return strtotime('+24 Hours', $this->getTableStartTime($calendarTableConfiguration, $displayTime));
    }

    public function getTableStartTime(CalendarTableConfiguration $calendarTableConfiguration, int $displayTime): int
    {
        if ($calendarTableConfiguration->getHideNonWorkingHours()) {
            return strtotime(date('Y-m-d ' . $calendarTableConfiguration->getStartHour() . ':00:00', $displayTime));
        }

        return strtotime(date('Y-m-d 00:00:00', $displayTime));
    }
}
