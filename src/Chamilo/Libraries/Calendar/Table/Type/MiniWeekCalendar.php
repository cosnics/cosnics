<?php
namespace Chamilo\Libraries\Calendar\Table\Type;

use Chamilo\Configuration\Configuration;
use Chamilo\Libraries\Calendar\Table\Calendar;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;
use Exception;
use HTML_Table;

/**
 *
 * @package Chamilo\Libraries\Calendar\Table\Type
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class MiniWeekCalendar extends Calendar
{

    private int $hourStep;

    private string $navigationHtml;

    public function __construct(int $displayTime, int $hourStep = 2)
    {
        $this->navigationHtml = '';
        $this->hourStep = $hourStep;
        parent::__construct($displayTime);
        $this->buildTable();
    }

    public function render(): string
    {
        $this->addEvents();
        $html = $this->toHtml();

        return $this->navigationHtml . $html;
    }

    public function addCalendarNavigation(string $urlFormat)
    {
        $weekNumber = date('W', $this->getDisplayTime());
        $prev = strtotime('-1 Week', $this->getDisplayTime());
        $next = strtotime('+1 Week', $this->getDisplayTime());
        $navigation = new HTML_Table('class="calendar_navigation"');
        $navigation->updateCellAttributes(0, 0, 'class="navigation-previous" style="text-align: left;"');
        $navigation->updateCellAttributes(0, 1, 'class="navigation-title" style="text-align: center;"');
        $navigation->updateCellAttributes(0, 2, 'class="navigation-next" style="text-align: right;"');

        $glyph = new FontAwesomeGlyph('backward');
        $navigation->setCellContents(
            0, 0,
            '<a href="' . str_replace(Calendar::TIME_PLACEHOLDER, $prev, $urlFormat) . '">' . $glyph->render() . '</a> '
        );

        $navigation->setCellContents(
            0, 1, htmlentities(Translation::get('Week', null, StringUtilities::LIBRARIES)) . ' ' . $weekNumber . ' : ' .
            date('l d M Y', $this->getStartTime()) . ' - ' .
            date('l d M Y', strtotime('+6 Days', $this->getStartTime()))
        );

        $glyph = new FontAwesomeGlyph('forward');
        $navigation->setCellContents(
            0, 2, ' <a href="' . str_replace(Calendar::TIME_PLACEHOLDER, $next, $urlFormat) . '">' . $glyph->render() .
            '</a> '
        );

        $this->navigationHtml = $navigation->toHtml();
    }

    private function addEvents()
    {
        $events = $this->getEventsToShow();

        foreach ($events as $time => $items)
        {
            $column = date('H', $time) / $this->getHourStep() + 1;
            $row = date('w', $time);

            if ($row == 0)
            {
                $row = 7;
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
        // Go 1 week back end them jump to the next monday to reach the first day of this week
        $firstDay = $this->getStartTime();

        for ($day = 0; $day < 7; $day ++)
        {
            $weekDay = strtotime('+' . $day . ' days', $firstDay);
            $this->setCellContents(
                $day + 1, 0, Translation::get(date('l', $weekDay) . 'Long', null, StringUtilities::LIBRARIES)
            );
        }

        $this->updateColAttributes(0, 'class="week_hours"');
        $this->updateColAttributes(0, 'style="height: 15px; width: 10px;"');

        for ($hour = 0; $hour < 24; $hour += $this->getHourStep())
        {
            $cellContent = $hour . ' - ' . ($hour + $this->getHourStep());

            $this->setCellContents(0, $hour / $this->getHourStep() + 1, $cellContent);
            $this->updateColAttributes(
                $hour / $this->getHourStep() + 1,
                'style="width: 8%; height: 15px; padding-left: 0px; padding-right: 0px;"'
            );

            for ($day = 0; $day < 7; $day ++)
            {
                $weekDay = strtotime('+' . $day . ' days', $firstDay);
                $class = [];

                // If day of week number is 0 (Sunday) or 6 (Saturday) -> it's a weekend
                if (date('w', $weekDay) % 6 == 0)
                {
                    $class[] = 'weekend';
                }

                if (count($class) > 0)
                {
                    $this->updateCellAttributes(
                        $day + 1, $hour / $this->getHourStep() + 1, 'class="' . implode(' ', $class) . '"'
                    );
                }
            }
        }

        $this->setRowType(0, 'th');
        $this->setColType(0, 'th');
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

    public function getHourStep(): int
    {
        return $this->hourStep;
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
