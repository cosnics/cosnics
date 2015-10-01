<?php
namespace Chamilo\Libraries\Calendar\Table\Type;

use Chamilo\Libraries\Calendar\Table\Calendar;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Configuration\LocalSetting;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;
use HTML_Table;

/**
 * A tabular representation of a day calendar
 *
 * @package libraries\calendar\table
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class DayCalendar extends Calendar
{

    /**
     * The navigation links
     */
    private $navigationHtml;

    /**
     * The number of hours for one table cell.
     */
    private $hourStep;

    /**
     * Creates a new day calendar
     *
     * @param int $displayTime A time in the day to be displayed
     * @param int $hourStep The number of hours for one table cell. Defaults to 1.
     */
    public function __construct($displayTime, $hourStep = 1)
    {
        $this->navigationHtml = '';
        $this->hourStep = $hourStep;
        parent :: __construct($displayTime);
        $this->buildTable();
    }

    /**
     * Gets the number of hours for one table cell.
     *
     * @return int
     */
    public function getHourStep()
    {
        return $this->hourStep;
    }

    /**
     * Sets the number of hours for one table cell.
     *
     * @return int
     */
    public function setHourStep($hourStep)
    {
        $this->hourStep = $hourStep;
    }

    /**
     * Gets the first date which will be displayed by this calendar.
     *
     * @return int
     */
    public function getStartTime()
    {
        $hide = LocalSetting :: get('hide_none_working_hours');

        if ($hide)
        {
            $workingStart = LocalSetting :: get('working_hours_start');
            return strtotime(date('Y-m-d ' . $workingStart . ':00:00', $this->getDisplayTime()));
        }

        return strtotime(date('Y-m-d 00:00:00', $this->getDisplayTime()));
    }

    /**
     * Gets the end date which will be displayed by this calendar.
     *
     * @return int
     */
    public function getEndTime()
    {
        $hide = LocalSetting :: get('hide_none_working_hours');

        if ($hide)
        {
            $workingEnd = LocalSetting :: get('working_hours_end');
            return strtotime(date('Y-m-d ' . ($workingEnd - 1) . ':59:59', $this->getDisplayTime()));
        }

        return strtotime('+24 Hours', $this->getStartTime());
    }

    /**
     * Builds the table
     */
    protected function buildTable()
    {
        $year_day = date('z', $this->getDisplayTime()) + 1;
        $year_week = date('W', $this->getDisplayTime());

        $header = $this->getHeader();
        $header->addRow(
            array(
                Translation :: get('Day', null, Utilities :: COMMON_LIBRARIES) . ' ' . $year_day . ', ' .
                     Translation :: get('Week', null, Utilities :: COMMON_LIBRARIES) . ' ' . $year_week));
        $header->setRowType(0, 'th');

        $workingStart = LocalSetting :: get('working_hours_start');
        $workingEnd = LocalSetting :: get('working_hours_end');
        $hide = LocalSetting :: get('hide_none_working_hours');
        $start = 0;
        $end = 24;

        if ($hide)
        {
            $start = $workingStart;
            $end = $workingEnd;
        }

        for ($hour = $start; $hour < $end; $hour += $this->getHourStep())
        {
            $table_start_date = mktime(
                $hour,
                0,
                0,
                date('m', $this->getDisplayTime()),
                date('d', $this->getDisplayTime()),
                date('Y', $this->getDisplayTime()));
            $table_end_date = strtotime('+' . $this->getHourStep() . ' hours', $table_start_date);
            $cellContents = $hour . Translation :: get('h', null, Utilities :: COMMON_LIBRARIES) . ' - ' .
                 ($hour + $this->getHourStep()) . Translation :: get('h', null, Utilities :: COMMON_LIBRARIES);

            $row = ($hour - $start) / $this->getHourStep();

            $this->setCellContents($row, 0, $cellContents);
            // Highlight current hour
            if (date('Y-m-d') == date('Y-m-d', $this->getDisplayTime()))
            {
                if (date('H') >= $hour && date('H') < $hour + $this->getHourStep())
                {
                    $this->updateCellAttributes($row, 0, 'class="highlight"');
                }
            }

            // Is current table hour during working hours?
            if ($hour < $workingStart || $hour >= $workingEnd)
            {
                $this->updateCellAttributes($row, 0, 'class="disabled_month"');
            }
        }
    }

    /**
     * Adds the events to the calendar
     */
    private function addEvents()
    {
        $events = $this->getEventsToShow();

        $workingStart = LocalSetting :: get('working_hours_start');
        $workingEnd = LocalSetting :: get('working_hours_end');
        $hide = LocalSetting :: get('hide_none_working_hours');
        $start = 0;
        $end = 24;

        if ($hide)
        {
            $start = $workingStart;
            $end = $workingEnd;
        }

        foreach ($events as $time => $items)
        {
            if ($time >= $this->getEndTime())
            {
                continue;
            }
            $row = (date('H', $time) - $start) / $this->hourStep;
            foreach ($items as $index => $item)
            {
                $cellContent = $this->getCellContents($row, 0);
                $cellContent .= $item;
                $this->setCellContents($row, 0, $cellContent);
            }
        }
    }

    /**
     * Adds a navigation bar to the calendar
     *
     * @param string $url_format The -TIME- in this string will be replaced by a timestamp
     */
    public function addCalendarNavigation($urlFormat)
    {
        $prev = strtotime('-1 Day', $this->getDisplayTime());
        $next = strtotime('+1 Day', $this->getDisplayTime());

        $navigation = new HTML_Table('class="calendar_navigation"');
        $navigation->updateCellAttributes(0, 0, 'style="text-align: left;"');
        $navigation->updateCellAttributes(0, 1, 'style="text-align: center;"');
        $navigation->updateCellAttributes(0, 2, 'style="text-align: right;"');
        $navigation->setCellContents(
            0,
            0,
            '<a href="' . htmlspecialchars(str_replace(Calendar :: TIME_PLACEHOLDER, $prev, $urlFormat)) . '"><img src="' .
                 htmlspecialchars(Theme :: getInstance()->getCommonImagePath('Action/Prev')) .
                 '" style="vertical-align: middle;" alt="&lt;&lt;"/></a> ');
        $navigation->setCellContents(0, 1, date('l d F Y', $this->getDisplayTime()));
        $navigation->setCellContents(
            0,
            2,
            ' <a href="' . htmlspecialchars(str_replace(Calendar :: TIME_PLACEHOLDER, $next, $urlFormat)) .
                 '"><img src="' . htmlspecialchars(Theme :: getInstance()->getCommonImagePath('Action/Next')) .
                 '" style="vertical-align: middle;" alt="&gt;&gt;"/></a> ');

        $this->navigationHtml = $navigation->toHtml();
    }

    /**
     * Returns a html-representation of this monthcalendar
     *
     * @return string
     */
    public function toHtml()
    {
        $html = array();
        $html[] = $this->navigationHtml;
        $html[] = parent :: toHtml();
        return implode(PHP_EOL, $html);
    }

    public function render()
    {
        $this->addEvents();
        return $this->toHtml();
    }
}
