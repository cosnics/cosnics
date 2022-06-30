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
class WeekCalendar extends Calendar
{
    public const TIME_PLACEHOLDER = '__TIME__';

    private ?string $dayUrlTemplate;

    private int $endHour;

    private bool $hideOtherHours;

    private int $hourStep;

    private int $startHour;

    public function __construct(
        int $displayTime, ?string $dayUrlTemplate = null, int $hourStep = 2, int $startHour = 0, int $endHour = 24,
        bool $hideOtherHours = false, array $classes = []
    )
    {
        $this->dayUrlTemplate = $dayUrlTemplate;
        $this->hourStep = $hourStep;
        $this->startHour = $startHour;
        $this->endHour = $endHour;
        $this->hideOtherHours = $hideOtherHours;

        parent::__construct($displayTime, $classes);
        $this->buildTable();
    }

    public function render(): string
    {
        $this->addEvents();

        return $this->toHtml();
    }

    private function addEvents()
    {
        $events = $this->getEventsToShow();
        $workingStart = $this->getStartHour();
        $workingEnd = $this->getEndHour();
        $hide = $this->getHideOtherHours();
        $start = 0;
        $end = 24;

        if ($hide)
        {
            $start = $workingStart;
            $end = $workingEnd;
        }

        foreach ($events as $time => $items)
        {
            $row = (date('H', $time) / $this->hourStep) - $start;

            if ($row > $end - $start - 1)
            {
                continue;
            }

            $column = date('w', $time);

            if ($column == 0)
            {
                $column = 7;
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

    private function buildTable()
    {
        $header = $this->getHeader();
        $header->setRowType(0, 'th');
        $header->setHeaderContents(0, 0, '');
        $header->updateCellAttributes(0, 0, 'class="table-calendar-week-hours"');

        // Go 1 week back end them jump to the next monday to reach the first day of this week
        $firstDay = $this->getStartTime();

        $workingStart = $this->getStartHour();
        $workingEnd = $this->getEndHour();
        $hide = $this->getHideOtherHours();
        $start = 0;
        $end = 24;

        if ($hide)
        {
            $start = $workingStart;
            $end = $workingEnd;
        }

        for ($hour = $start; $hour < $end; $hour += $this->getHourStep())
        {
            $rowId = ($hour / $this->getHourStep()) - $start;
            $cellContent = str_pad($hour, 2, '0', STR_PAD_LEFT);
            $this->setCellContents($rowId, 0, $cellContent);

            $classes = [];

            $classes[] = 'table-calendar-week-hours';

            if ($hour % 2 == 0)
            {
                $classes[] = 'table-calendar-alternate';
            }

            $this->updateCellAttributes($rowId, 0, 'class="' . implode(' ', $classes) . '"');
        }

        $today = date('Y-m-d');

        for ($day = 0; $day < 7; $day ++)
        {
            $weekDayTime = strtotime('+' . $day . ' days', $firstDay);
            $header->setHeaderContents(0, $day + 1, $this->getHeaderContent($weekDayTime));

            for ($hour = $start; $hour < $end; $hour += $this->getHourStep())
            {
                $row = ($hour / $this->getHourStep()) - $start;

                $classes = $this->determineCellClasses($today, $weekDayTime, $hour, $workingStart, $workingEnd);

                if (count($classes) > 0)
                {
                    $this->updateCellAttributes($row, $day + 1, 'class="' . implode(' ', $classes) . '"');
                }

                $this->setCellContents($row, $day + 1, '');
            }
        }
    }

    /**
     * @return string[]
     */
    protected function determineCellClasses(int $today, int $weekDay, int $hour, int $workingStart, int $workingEnd
    ): array
    {
        $classes = [];

        if ($today == date('Y-m-d', $weekDay))
        {
            if (date('H') >= $hour && date('H') < $hour + $this->getHourStep())
            {
                $classes[] = 'table-calendar-highlight';
            }
        }

        // If day of week number is 0 (Sunday) or 6 (Saturday) -> it's a weekend
        if (date('w', $weekDay) % 6 == 0)
        {
            $classes[] = 'table-calendar-weekend';
        }
        elseif ($hour % 2 == 0)
        {
            $classes[] = 'table-calendar-alternate';
        }

        if ($hour < $workingStart || $hour >= $workingEnd)
        {
            $classes[] = 'table-calendar-disabled';
        }

        return $classes;
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

    public function getEndHour(): int
    {
        return $this->endHour;
    }

    public function setEndHour(int $endHour)
    {
        $this->endHour = $endHour;
    }

    /**
     * Gets the end date which will be displayed by this calendar. This is always a sunday.
     */
    public function getEndTime(): int
    {
        $setting = Configuration::getInstance()->get_setting(['Chamilo\Libraries\Calendar', 'first_day_of_week']);

        if ($setting == 'sunday')
        {
            return strtotime('Next Saterday', strtotime('-1 Week', $this->getDisplayTime()));
        }

        return strtotime('Next Sunday', $this->getStartTime());
    }

    protected function getHeaderContent(int $weekDayTime): string
    {
        $dayLabel = Translation::get(date('l', $weekDayTime) . 'Short', null, StringUtilities::LIBRARIES) . ' ' .
            date('d/m', $weekDayTime);

        $dayUrlTemplate = $this->getDayUrlTemplate();

        if (is_null($dayUrlTemplate))
        {
            return $dayLabel;
        }
        else
        {
            return '<a href="' . $this->getDayUrl($weekDayTime) . '">' . $dayLabel . '</a>';
        }
    }

    public function getHideOtherHours(): bool
    {
        return $this->hideOtherHours;
    }

    public function setHideOtherHours(bool $hideOtherHours)
    {
        $this->hideOtherHours = $hideOtherHours;
    }

    public function getHourStep(): int
    {
        return $this->hourStep;
    }

    public function setHourStep(int $hourStep)
    {
        $this->hourStep = $hourStep;
    }

    public function getStartHour(): int
    {
        return $this->startHour;
    }

    public function setStartHour(int $startHour)
    {
        $this->startHour = $startHour;
    }

    /**
     * Gets the first date which will be displayed by this calendar. This is always a monday.
     */
    public function getStartTime(): int
    {
        $setting = Configuration::getInstance()->get_setting(['Chamilo\Libraries\Calendar', 'first_day_of_week']);

        if ($setting == 'sunday')
        {
            return strtotime('Next Sunday', strtotime('-1 Week', $this->getDisplayTime()));
        }

        return strtotime('Next Monday', strtotime('-1 Week', $this->getDisplayTime()));
    }
}
