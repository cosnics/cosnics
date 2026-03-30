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
class MonthCalendarTableBuilder extends CalendarTableBuilder
{
    public const string TIME_PLACEHOLDER = '__TIME__';

    protected function addEventItems(HTML_Table $table, $time, $row, $column, $items): void
    {
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

    protected function addEvents(
        CalendarTableConfiguration $calendarTableConfiguration, int $displayTime, HTML_Table $table, array $cellMapping,
        array $events
    ): void
    {
        foreach ($events as $time => $items) {
            $cellMappingKey = date('Ymd', $time);

            $row = $cellMapping[$cellMappingKey][0];
            $column = $cellMapping[$cellMappingKey][1];

            if (is_null($row) || is_null($column)) {
                continue;
            }

            $this->addEventItems($table, $time, $row, $column, $items);
        }
    }

    /**
     * @throws \Exception
     */
    protected function buildTable(
        CalendarTableConfiguration $calendarTableConfiguration, HTML_Table $table, int $displayTime,
        ?string $dayUrlTemplate = null
    ): array
    {
        $tableDate = $this->getTableStartTime($calendarTableConfiguration, $displayTime);
        $cell = 0;
        $cellMapping = [];

        while (date('Ym', $tableDate) <= date('Ym', $displayTime)) {
            do {
                $row = intval($cell / 7);
                $column = $cell % 7;

                $cellMapping[date('Ymd', $tableDate)] = [$row, $column];

                $classes = $this->determineCellClasses($tableDate, $displayTime);

                if (count($classes) > 0) {
                    $table->setCellAttributes($row, $column, ['class' => $classes]);
                }

                $table->setCellContents($row, $column, $this->determineCellContent($tableDate, $dayUrlTemplate));

                $cell ++;
                $tableDate = strtotime('+1 Day', $tableDate);
            }
            while ($cell % 7 != 0);
        }

        $this->setHeader($calendarTableConfiguration, $table);

        return $cellMapping;
    }

    /**
     * @return string[]
     */
    protected function determineCellClasses(int $tableDate, int $displayTime): array
    {
        $classes = [];

        // Is current table date today?
        if (date('Ymd', $tableDate) == date('Ymd')) {
            $classes[] = 'table-calendar-highlight';
        }

        // If day of week number is 0 (Sunday) or 6 (Saturday) -> it's a weekend
        if (date('w', $tableDate) % 6 == 0) {
            $classes[] = 'table-calendar-weekend';
        }

        // Is current table date in this month or another one?
        if (date('Ym', $tableDate) != date('Ym', $displayTime)) {
            $classes[] = 'table-calendar-disabled';
        }

        return $classes;
    }

    protected function determineCellContent(int $tableDate, ?string $dayUrlTemplate = null): string
    {
        $dayLabel = date('j', $tableDate);

        if (is_null($dayUrlTemplate)) {
            return $dayLabel;
        }
        else {
            return '<a href="' . $this->getDayUrl($tableDate, $dayUrlTemplate) . '">' . $dayLabel . '</a>';
        }
    }

    public function getDayUrl(int $time, string $dayUrlTemplate): string
    {
        return str_replace(self::TIME_PLACEHOLDER, (string) $time, $dayUrlTemplate);
    }

    public function getTableEndTime(CalendarTableConfiguration $calendarTableConfiguration, int $displayTime): int
    {
        $endTime = $this->getTableStartTime($calendarTableConfiguration, $displayTime);

        while (date('Ym', $endTime) <= date('Ym', $displayTime)) {
            $endTime = strtotime('+1 Week', $endTime);
        }

        return $endTime;
    }

    public function getTableStartTime(CalendarTableConfiguration $calendarTableConfiguration, int $displayTime): int
    {
        $firstDay = mktime(0, 0, 0, (int) date('m', $displayTime), 1, (int) date('Y', $displayTime));

        if ($calendarTableConfiguration->getFirstDayOfWeek() == 'sunday') {
            return strtotime('Next Sunday', strtotime('-1 Week', $firstDay));
        }

        return strtotime('Next Monday', strtotime('-1 Week', $firstDay));
    }

    /**
     * @throws \Exception
     */
    public function setHeader(CalendarTableConfiguration $calendarTableConfiguration, HTML_Table $table): void
    {
        $header = $table->getHeader();

        $setting = $calendarTableConfiguration->getFirstDayOfWeek();

        if ($setting == 'sunday') {
            $header->addRow(
                [
                    $this->translator->trans('SundayShort', [], StringUtilities::LIBRARIES),
                    $this->translator->trans('MondayShort', [], StringUtilities::LIBRARIES),
                    $this->translator->trans('TuesdayShort', [], StringUtilities::LIBRARIES),
                    $this->translator->trans('WednesdayShort', [], StringUtilities::LIBRARIES),
                    $this->translator->trans('ThursdayShort', [], StringUtilities::LIBRARIES),
                    $this->translator->trans('FridayShort', [], StringUtilities::LIBRARIES),
                    $this->translator->trans('SaturdayShort', [], StringUtilities::LIBRARIES)
                ]
            );
        }
        else {
            $header->addRow(
                [
                    $this->translator->trans('MondayShort', [], StringUtilities::LIBRARIES),
                    $this->translator->trans('TuesdayShort', [], StringUtilities::LIBRARIES),
                    $this->translator->trans('WednesdayShort', [], StringUtilities::LIBRARIES),
                    $this->translator->trans('ThursdayShort', [], StringUtilities::LIBRARIES),
                    $this->translator->trans('FridayShort', [], StringUtilities::LIBRARIES),
                    $this->translator->trans('SaturdayShort', [], StringUtilities::LIBRARIES),
                    $this->translator->trans('SundayShort', [], StringUtilities::LIBRARIES)
                ]
            );
        }

        $header->setRowType(0, 'th');
    }
}
