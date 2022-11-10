<?php
namespace Chamilo\Libraries\Calendar\Service\View\TableBuilder;

use Chamilo\Libraries\Utilities\StringUtilities;
use Exception;
use HTML_Table;

/**
 * @package Chamilo\Libraries\Calendar\Service\View\TableBuilder
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class MonthCalendarTableBuilder extends CalendarTableBuilder
{
    public const TIME_PLACEHOLDER = '__TIME__';

    protected function addEventItems(HTML_Table $table, $time, $row, $column, $items)
    {
        foreach ($items as $item)
        {
            try
            {
                $cellContent = $table->getCellContents($row, $column);
                $cellContent .= $item;
                $table->setCellContents($row, $column, $cellContent);
            }
            catch (Exception $exception)
            {
            }
        }
    }

    protected function addEvents(int $displayTime, HTML_Table $table, array $cellMapping, array $events)
    {
        foreach ($events as $time => $items)
        {
            $cellMappingKey = date('Ymd', $time);

            $row = $cellMapping[$cellMappingKey][0];
            $column = $cellMapping[$cellMappingKey][1];

            if (is_null($row) || is_null($column))
            {
                continue;
            }

            $this->addEventItems($table, $time, $row, $column, $items);
        }
    }

    /**
     * @throws \Exception
     */
    protected function buildTable(HTML_Table $table, int $displayTime, ?string $dayUrlTemplate = null): array
    {
        $tableDate = $this->getTableStartTime($displayTime);
        $cell = 0;
        $cellMapping = [];

        while (date('Ym', $tableDate) <= date('Ym', $displayTime))
        {
            do
            {
                $row = intval($cell / 7);
                $column = $cell % 7;

                $cellMapping[date('Ymd', $tableDate)] = [$row, $column];

                $classes = $this->determineCellClasses($tableDate, $displayTime);

                if (count($classes) > 0)
                {
                    $table->setCellAttributes($row, $column, ['class' => $classes]);
                }

                $table->setCellContents($row, $column, $this->determineCellContent($tableDate, $dayUrlTemplate));

                $cell ++;
                $tableDate = strtotime('+1 Day', $tableDate);
            }
            while ($cell % 7 != 0);
        }

        $this->setHeader($table);

        return $cellMapping;
    }

    /**
     * @return string[]
     */
    protected function determineCellClasses(int $tableDate, int $displayTime): array
    {
        $classes = [];

        // Is current table date today?
        if (date('Ymd', $tableDate) == date('Ymd'))
        {
            $classes[] = 'table-calendar-highlight';
        }

        // If day of week number is 0 (Sunday) or 6 (Saturday) -> it's a weekend
        if (date('w', $tableDate) % 6 == 0)
        {
            $classes[] = 'table-calendar-weekend';
        }

        // Is current table date in this month or another one?
        if (date('Ym', $tableDate) != date('Ym', $displayTime))
        {
            $classes[] = 'table-calendar-disabled';
        }

        return $classes;
    }

    protected function determineCellContent(int $tableDate, ?string $dayUrlTemplate = null): string
    {
        $dayLabel = date('j', $tableDate);

        if (is_null($dayUrlTemplate))
        {
            return $dayLabel;
        }
        else
        {
            return '<a href="' . $this->getDayUrl($tableDate, $dayUrlTemplate) . '">' . $dayLabel . '</a>';
        }
    }

    public function getDayUrl(int $time, string $dayUrlTemplate): string
    {
        return str_replace(self::TIME_PLACEHOLDER, $time, $dayUrlTemplate);
    }

    protected function getFirstDayOfWeek(): ?string
    {
        return $this->getUserSettingService()->getSettingForUser(
            $this->getUser(), 'Chamilo\Libraries\Calendar', 'first_day_of_week'
        );
    }

    public function getTableEndTime(int $displayTime): int
    {
        $endTime = $this->getTableStartTime($displayTime);

        while (date('Ym', $endTime) <= date('Ym', $displayTime))
        {
            $endTime = strtotime('+1 Week', $endTime);
        }

        return $endTime;
    }

    public function getTableStartTime(int $displayTime): int
    {
        $firstDay = mktime(0, 0, 0, date('m', $displayTime), 1, date('Y', $displayTime));

        if ($this->getFirstDayOfWeek() == 'sunday')
        {
            return strtotime('Next Sunday', strtotime('-1 Week', $firstDay));
        }

        return strtotime('Next Monday', strtotime('-1 Week', $firstDay));
    }

    /**
     * @throws \Exception
     */
    public function setHeader(HTML_Table $table)
    {
        $translator = $this->getTranslator();
        $header = $table->getHeader();

        $setting = $this->getFirstDayOfWeek();

        if ($setting == 'sunday')
        {
            $header->addRow(
                [
                    $translator->trans('SundayShort', [], StringUtilities::LIBRARIES),
                    $translator->trans('MondayShort', [], StringUtilities::LIBRARIES),
                    $translator->trans('TuesdayShort', [], StringUtilities::LIBRARIES),
                    $translator->trans('WednesdayShort', [], StringUtilities::LIBRARIES),
                    $translator->trans('ThursdayShort', [], StringUtilities::LIBRARIES),
                    $translator->trans('FridayShort', [], StringUtilities::LIBRARIES),
                    $translator->trans('SaturdayShort', [], StringUtilities::LIBRARIES)
                ]
            );
        }
        else
        {
            $header->addRow(
                [
                    $translator->trans('MondayShort', [], StringUtilities::LIBRARIES),
                    $translator->trans('TuesdayShort', [], StringUtilities::LIBRARIES),
                    $translator->trans('WednesdayShort', [], StringUtilities::LIBRARIES),
                    $translator->trans('ThursdayShort', [], StringUtilities::LIBRARIES),
                    $translator->trans('FridayShort', [], StringUtilities::LIBRARIES),
                    $translator->trans('SaturdayShort', [], StringUtilities::LIBRARIES),
                    $translator->trans('SundayShort', [], StringUtilities::LIBRARIES)
                ]
            );
        }

        $header->setRowType(0, 'th');
    }

}
