<?php
namespace Chamilo\Libraries\Calendar\Table\Type;

use Chamilo\Libraries\Calendar\Table\Calendar;
use Chamilo\Libraries\Format\Theme;
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
     *
     * @var int
     */
    private $startHour;

    /**
     *
     * @var int
     */
    private $endHour;

    /**
     *
     * @var boolean
     */
    private $hideOtherHours;

    /**
     * Creates a new day calendar
     *
     * @param int $displayTime A time in the day to be displayed
     * @param int $hourStep The number of hours for one table cell. Defaults to 1.
     * @param int $startHour
     * @param int $endHour
     * @param boolean $hideOtherHours
     */
    public function __construct($displayTime, $hourStep = 1, $startHour = 0, $endHour = 24, $hideOtherHours = false)
    {
        $this->navigationHtml = '';
        $this->hourStep = $hourStep;
        $this->startHour = $startHour;
        $this->endHour = $endHour;
        $this->hideOtherHours = $hideOtherHours;

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
     *
     * @return integer
     */
    public function getStartHour()
    {
        return $this->startHour;
    }

    /**
     *
     * @param integer $startHour
     */
    public function setStartHour($startHour)
    {
        $this->startHour = $startHour;
    }

    /**
     *
     * @return integer
     */
    public function getEndHour()
    {
        return $this->endHour;
    }

    /**
     *
     * @param integer $endHour
     */
    public function setEndHour($endHour)
    {
        $this->endHour = $endHour;
    }

    /**
     *
     * @return boolean
     */
    public function getHideOtherHours()
    {
        return $this->hideOtherHours;
    }

    /**
     *
     * @param boolean $hideOtherHours
     */
    public function setHideOtherHours($hideOtherHours)
    {
        $this->hideOtherHours = $hideOtherHours;
    }

    /**
     * Gets the first date which will be displayed by this calendar.
     *
     * @return int
     */
    public function getStartTime()
    {
        if ($this->getHideOtherHours())
        {
            // $workingStart = LocalSetting :: getInstance()->get('working_hours_start');
            return strtotime(date('Y-m-d ' . $this->getStartHour() . ':00:00', $this->getDisplayTime()));
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
        if ($this->getHideOtherHours())
        {
            // $workingEnd = LocalSetting :: getInstance()->get('working_hours_end');
            return strtotime(date('Y-m-d ' . ($this->getEndHour() - 1) . ':59:59', $this->getDisplayTime()));
        }

        return strtotime('+24 Hours', $this->getStartTime());
    }

    /**
     * Builds the table
     */
    protected function buildTable()
    {
        $yearDay = date('z', $this->getDisplayTime()) + 1;
        $yearWeek = date('W', $this->getDisplayTime());

        $header = $this->getHeader();
        $header->addRow(
            array(
                Translation :: get('Day', null, Utilities :: COMMON_LIBRARIES) . ' ' . $yearDay . ', ' .
                     Translation :: get('Week', null, Utilities :: COMMON_LIBRARIES) . ' ' . $yearWeek));
        $header->setRowType(0, 'th');

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

            $tableStartDate = mktime(
                $hour,
                0,
                0,
                date('m', $this->getDisplayTime()),
                date('d', $this->getDisplayTime()),
                date('Y', $this->getDisplayTime()));

            $tableEndDate = strtotime('+' . $this->getHourStep() . ' hours', $tableStartDate);
            $this->setCellContents($rowId, 0, $this->getCellIdentifier($hour));

            // Highlight current hour
            if (date('Y-m-d') == date('Y-m-d', $this->getDisplayTime()))
            {
                if (date('H') >= $hour && date('H') < $hour + $this->getHourStep())
                {
                    $this->updateCellAttributes($rowId, 0, 'class="highlight"');
                }
            }

            // Is current table hour during working hours?
            if ($hour < $this->getStartHour() || $hour >= $this->getEndHour())
            {
                $this->updateCellAttributes($rowId, 0, 'class="disabled_month"');
            }
        }
    }

    /**
     *
     * @param integer $hour
     * @return string
     */
    public function getCellIdentifier($hour)
    {
        return $hour . Translation :: get('h', null, Utilities :: COMMON_LIBRARIES) . ' - ' .
             ($hour + $this->getHourStep()) . Translation :: get('h', null, Utilities :: COMMON_LIBRARIES);
    }

    /**
     * Adds the events to the calendar
     */
    protected function addEvents()
    {
        $events = $this->getEventsToShow();

        $start = 0;
        $end = 24;

        if ($this->getHideOtherHours())
        {
            $start = $this->getStartHour();
            $end = $this->getEndHour();
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

        $navigation = new HTML_Table('class="' . $this->getNavigationClasses() . '"');
        $navigation->updateCellAttributes(0, 0, 'class="navigation-previous" style="text-align: left;"');
        $navigation->updateCellAttributes(0, 1, 'class="navigation-title" style="text-align: center;"');
        $navigation->updateCellAttributes(0, 2, 'class="navigation-next" style="text-align: right;"');
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

    public function getNavigationClasses()
    {
        return 'calendar_navigation';
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
