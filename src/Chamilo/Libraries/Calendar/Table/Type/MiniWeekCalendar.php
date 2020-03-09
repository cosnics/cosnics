<?php
namespace Chamilo\Libraries\Calendar\Table\Type;

use Chamilo\Configuration\Configuration;
use Chamilo\Libraries\Calendar\Table\Calendar;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\Utilities;
use Exception;
use HTML_Table;

/**
 *
 * @package Chamilo\Libraries\Calendar\Table\Type
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class MiniWeekCalendar extends Calendar
{

    /**
     * The navigation links
     *
     * @var string
     */
    private $navigationHtml;

    /**
     * The number of hours for one table cell.
     *
     * @var integer
     */
    private $hourStep;

    /**
     * Creates a new week calendar
     *
     * @param integer $displayTime A time in the week to be displayed
     * @param integer $hourStep The number of hours for one table cell. Defaults to 2.
     */
    public function __construct($displayTime, $hourStep = 2)
    {
        $this->navigationHtml = '';
        $this->hourStep = $hourStep;
        parent::__construct($displayTime);
        $this->buildTable();
    }

    /**
     * Adds a navigation bar to the calendar
     *
     * @param string $urlFormat The *TIME* in this string will be replaced by a timestamp
     */
    public function addCalendarNavigation($urlFormat)
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
            0, 1,
            htmlentities(Translation::get('Week', null, Utilities::COMMON_LIBRARIES)) . ' ' . $weekNumber . ' : ' .
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

    /**
     * Adds the events to the calendar
     */
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

            foreach ($items as $index => $item)
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
     * Builds the table
     */
    private function buildTable()
    {
        // Go 1 week back end them jump to the next monday to reach the first day of this week
        $firstDay = $this->getStartTime();

        for ($day = 0; $day < 7; $day ++)
        {
            $weekDay = strtotime('+' . $day . ' days', $firstDay);
            $this->setCellContents(
                $day + 1, 0, Translation::get(date('l', $weekDay) . 'Long', null, Utilities::COMMON_LIBRARIES)
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
                $class = array();

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
     * Gets the end date which will be displayed by this calendar.
     * This is always a sunday.
     *
     * @return integer
     */
    public function getEndTime()
    {
        $setting = Configuration::getInstance()->get_setting(array('Chamilo\Libraries\Calendar', 'first_day_of_week'));

        if ($setting == 'sunday')
        {
            return strtotime('Next Saterday', strtotime('-1 Week', $this->getDisplayTime()));
        }

        return strtotime('Next Sunday', $this->getStartTime());
    }

    /**
     * Gets the number of hours for one table cell.
     *
     * @return integer
     */
    public function getHourStep()
    {
        return $this->hourStep;
    }

    /**
     * Gets the first date which will be displayed by this calendar.
     * This is always a monday.
     *
     * @return integer
     */
    public function getStartTime()
    {
        $setting = Configuration::getInstance()->get_setting(array('Chamilo\Libraries\Calendar', 'first_day_of_week'));

        if ($setting == 'sunday')
        {
            return strtotime('Next Sunday', strtotime('-1 Week', $this->getDisplayTime()));
        }

        return strtotime('Next Monday', strtotime('-1 Week', $this->getDisplayTime()));
    }

    /**
     * Returns a html-representation of this monthcalendar
     *
     * @return string
     */
    public function toHtml()
    {
        $this->add_events();
        $html = parent::toHtml();

        return $this->navigationHtml . $html;
    }
}
