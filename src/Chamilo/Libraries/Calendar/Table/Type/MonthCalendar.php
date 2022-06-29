<?php
namespace Chamilo\Libraries\Calendar\Table\Type;

use Chamilo\Configuration\Configuration;
use Chamilo\Libraries\Calendar\Table\Calendar;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;
use Exception;

/**
 *
 * @package Chamilo\Libraries\Calendar\Table\Type
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class MonthCalendar extends Calendar
{
    public const TIME_PLACEHOLDER = '__TIME__';

    /**
     * Keep mapping of dates and their corresponding table cells
     *
     * @var int[]
     */
    private array $cellMapping;

    private ?string $dayUrlTemplate;

    /**
     * @param string[] $classes
     *
     * @throws \ReflectionException
     */
    public function __construct(int $displayTime, ?string $dayUrlTemplate = null, array $classes = [])
    {
        parent::__construct($displayTime, $classes);

        $this->cellMapping = [];
        $this->dayUrlTemplate = $dayUrlTemplate;

        $this->buildTable();
    }

    public function render(): string
    {
        $this->addEvents();

        return $this->toHtml();
    }

    public function addEvents()
    {
        $events = $this->getEventsToShow();

        foreach ($events as $time => $items)
        {
            $cellMappingKey = date('Ymd', $time);

            $row = $this->cellMapping[$cellMappingKey][0];
            $column = $this->cellMapping[$cellMappingKey][1];

            if (is_null($row) || is_null($column))
            {
                continue;
            }

            foreach ($items as $item)
            {
                try
                {
                    $cellContent = $this->getCellContents($row, $column);
                    $cellContent .= $item;
                    $this->setCellContents($row, $column, $cellContent);
                }
                catch (Exception $exception)
                {
                }
            }
        }
    }

    /**
     * @throws \ReflectionException
     */
    private function buildTable()
    {
        $firstDay = mktime(0, 0, 0, date('m', $this->getDisplayTime()), 1, date('Y', $this->getDisplayTime()));
        $tableDate = $this->getFirstTableDate($firstDay);
        $cell = 0;

        while (date('Ym', $tableDate) <= date('Ym', $this->getDisplayTime()))
        {
            do
            {
                $row = intval($cell / 7);
                $column = $cell % 7;

                $this->cellMapping[date('Ymd', $tableDate)] = [$row, $column];

                $classes = $this->determineCellClasses($tableDate);

                if (count($classes) > 0)
                {
                    $this->updateCellAttributes($row, $column, 'class="' . implode(' ', $classes) . '"');
                }

                $this->setCellContents($row, $column, $this->determineCellContent($tableDate));

                $cell ++;
                $tableDate = strtotime('+1 Day', $tableDate);
            }
            while ($cell % 7 != 0);
        }

        $this->setHeader();
    }

    /**
     * @return string[]
     */
    protected function determineCellClasses(int $tableDate): array
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
        if (date('Ym', $tableDate) != date('Ym', $this->getDisplayTime()))
        {
            $classes[] = 'table-calendar-disabled';
        }

        return $classes;
    }

    protected function determineCellContent(int $tableDate): string
    {
        $dayLabel = date('j', $tableDate);
        $dayUrlTemplate = $this->getDayUrlTemplate();

        if (is_null($dayUrlTemplate))
        {
            return $dayLabel;
        }
        else
        {
            return '<a href="' . $this->getDayUrl($tableDate) . '">' . $dayLabel . '</a>';
        }
    }

    /**
     * @return int[]
     */
    public function getCellMapping(): array
    {
        return $this->cellMapping;
    }

    public function getDayUrl(int $time): string
    {
        return str_replace(self::TIME_PLACEHOLDER, $time, $this->getDayUrlTemplate());
    }

    public function getDayUrlTemplate(): ?string
    {
        return $this->dayUrlTemplate;
    }

    public function setDayUrlTemplate(?string $dayUrlTemplate)
    {
        $this->dayUrlTemplate = $dayUrlTemplate;
    }

    /**
     * Gets the end date which will be displayed by this calendar. This is always a sunday. If the current month
     * doesn't end on a sunday, the first sunday of next month is returned.
     *
     * @return int
     */
    public function getEndTime(): int
    {
        $endTime = $this->getStartTime();

        while (date('Ym', $endTime) <= date('Ym', $this->getDisplayTime()))
        {
            $endTime = strtotime('+1 Week', $endTime);
        }

        return $endTime;
    }

    protected function getFirstTableDate(int $firstDay): int
    {
        $setting = Configuration::getInstance()->get_setting(['Chamilo\Libraries\Calendar', 'first_day_of_week']);

        if ($setting == 'sunday')
        {
            return strtotime('Next Sunday', strtotime('-1 Week', $firstDay));
        }
        else
        {
            return strtotime('Next Monday', strtotime('-1 Week', $firstDay));
        }
    }

    /**
     * Gets the first date which will be displayed by this calendar. This is always a monday. If the current month
     * doesn't start on a monday, the last monday of previous month is returned.
     */
    public function getStartTime(): int
    {
        $firstDay = mktime(0, 0, 0, date('m', $this->getDisplayTime()), 1, date('Y', $this->getDisplayTime()));
        $setting = Configuration::getInstance()->get_setting(['Chamilo\Libraries\Calendar', 'first_day_of_week']);

        if ($setting == 'sunday')
        {
            return strtotime('Next Sunday', strtotime('-1 Week', $firstDay));
        }

        return strtotime('Next Monday', strtotime('-1 Week', $firstDay));
    }

    /**
     * @throws \ReflectionException
     * @throws \Exception
     */
    protected function setHeader()
    {
        $header = $this->getHeader();

        $setting = Configuration::getInstance()->get_setting(['Chamilo\Libraries\Calendar', 'first_day_of_week']);

        if ($setting == 'sunday')
        {
            $header->addRow(
                [
                    Translation::get('SundayShort', [], StringUtilities::LIBRARIES),
                    Translation::get('MondayShort', [], StringUtilities::LIBRARIES),
                    Translation::get('TuesdayShort', [], StringUtilities::LIBRARIES),
                    Translation::get('WednesdayShort', [], StringUtilities::LIBRARIES),
                    Translation::get('ThursdayShort', [], StringUtilities::LIBRARIES),
                    Translation::get('FridayShort', [], StringUtilities::LIBRARIES),
                    Translation::get('SaturdayShort', [], StringUtilities::LIBRARIES)
                ]
            );
        }
        else
        {
            $header->addRow(
                [
                    Translation::get('MondayShort', [], StringUtilities::LIBRARIES),
                    Translation::get('TuesdayShort', [], StringUtilities::LIBRARIES),
                    Translation::get('WednesdayShort', [], StringUtilities::LIBRARIES),
                    Translation::get('ThursdayShort', [], StringUtilities::LIBRARIES),
                    Translation::get('FridayShort', [], StringUtilities::LIBRARIES),
                    Translation::get('SaturdayShort', [], StringUtilities::LIBRARIES),
                    Translation::get('SundayShort', [], StringUtilities::LIBRARIES)
                ]
            );
        }

        $header->setRowType(0, 'th');
    }
}
