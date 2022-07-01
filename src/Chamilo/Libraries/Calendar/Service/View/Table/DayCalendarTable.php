<?php
namespace Chamilo\Libraries\Calendar\Service\View\Table;

use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;
use Exception;

/**
 * @package Chamilo\Libraries\Calendar\Table\Type
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class DayCalendarTable extends CalendarTable
{

    private int $endHour;

    private bool $hideOtherHours;

    private int $hourStep;

    private int $startHour;

    public function __construct(
        int $displayTime, int $hourStep = 1, int $startHour = 0, int $endHour = 24, bool $hideOtherHours = false,
        array $classes = []
    )
    {
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

    protected function addEvents()
    {
        $events = $this->getEventsToShow();

        $start = 0;

        if ($this->getHideOtherHours())
        {
            $start = $this->getStartHour();
        }

        foreach ($events as $time => $items)
        {
            if ($time >= $this->getEndTime())
            {
                continue;
            }

            $row = (date('H', $time) - $start) / $this->hourStep;

            foreach ($items as $item)
            {
                try
                {
                    $cellContent = $this->getCellContents($row, 1);
                    $cellContent .= $item;
                    $this->setCellContents($row, 1, $cellContent);
                }
                catch (Exception $exception)
                {
                }
            }
        }
    }

    protected function buildTable()
    {
        $header = $this->getHeader();
        $header->setRowType(0, 'th');
        $header->setHeaderContents(0, 0, '');
        $header->updateCellAttributes(0, 0, 'class="table-calendar-day-hours"');

        $header->setHeaderContents(
            0, 1,
            Translation::get(date('l', $this->getDisplayTime()) . 'Short', null, StringUtilities::LIBRARIES) . ' ' .
            date('d/m', $this->getDisplayTime())
        );

        $startHour = 0;
        $endHour = 24;

        if ($this->getHideOtherHours())
        {
            $startHour = $this->getStartHour();
            $endHour = $this->getEndHour();
        }

        for ($hour = $startHour; $hour < $endHour; $hour += $this->getHourStep())
        {
            $rowId = ($hour / $this->getHourStep()) - $startHour;
            $cellContent = str_pad($hour, 2, '0', STR_PAD_LEFT);
            $this->setCellContents($rowId, 0, $cellContent);

            $classes = [];

            $classes[] = 'table-calendar-day-hours';

            if ($hour % 2 == 0)
            {
                $classes[] = 'table-calendar-alternate';
            }

            $this->updateCellAttributes($rowId, 0, 'class="' . implode(' ', $classes) . '"');
        }

        for ($hour = $startHour; $hour < $endHour; $hour += $this->getHourStep())
        {
            $rowId = ($hour / $this->getHourStep()) - $startHour;

            $this->setCellContents($rowId, 1, '');

            $classes = $this->determineCellClasses($hour);

            if (count($classes) > 0)
            {
                $this->updateCellAttributes($rowId, 1, 'class="' . implode(' ', $classes) . '"');
            }
        }
    }

    /**
     * @return string[]
     */
    protected function determineCellClasses(int $hour): array
    {
        $classes = [];

        // Highlight current hour
        if (date('Y-m-d') == date('Y-m-d', $this->getDisplayTime()))
        {
            if (date('H') >= $hour && date('H') < $hour + $this->getHourStep())
            {
                $classes[] = 'table-calendar-highlight';
            }
        }

        // Is current table hour during working hours?
        if ($hour < $this->getStartHour() || $hour >= $this->getEndHour())
        {
            $classes[] = 'table-calendar-disabled';
        }

        if ($hour % 2 == 0)
        {
            $classes[] = 'table-calendar-alternate';
        }

        return $classes;
    }

    public function getEndHour(): int
    {
        return $this->endHour;
    }

    public function setEndHour(int $endHour)
    {
        $this->endHour = $endHour;
    }

    public function getEndTime(): int
    {
        if ($this->getHideOtherHours())
        {
            return strtotime(date('Y-m-d ' . ($this->getEndHour() - 1) . ':59:59', $this->getDisplayTime()));
        }

        return strtotime('+24 Hours', $this->getStartTime());
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

    public function getStartTime(): int
    {
        if ($this->getHideOtherHours())
        {
            return strtotime(date('Y-m-d ' . $this->getStartHour() . ':00:00', $this->getDisplayTime()));
        }

        return strtotime(date('Y-m-d 00:00:00', $this->getDisplayTime()));
    }
}
