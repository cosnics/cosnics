<?php
namespace Chamilo\Libraries\Calendar\Table\Type;

use Chamilo\Libraries\Calendar\Table\Calendar;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Configuration\PlatformSetting;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;
use HTML_Table;

/**
 * A tabular representation of a month calendar
 *
 * @package libraries\calendar\table
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class MonthCalendar extends Calendar
{

    /**
     * Keep mapping of dates and their corresponding table cells
     *
     * @var integer[]
     */
    private $cellMapping;

    /**
     * The navigation links
     *
     * @var string
     */
    private $navigationHtml;

    /**
     * Creates a new month calendar
     *
     * @param integer $displayTime A time in the month to be displayed
     */
    public function __construct($displayTime)
    {
        $this->navigationHtml = '';
        parent :: __construct($displayTime);
        $this->cellMapping = array();
        $this->buildTable();
    }

    /**
     * Gets the first date which will be displayed by this calendar.
     * This is always a monday. If the current month
     * doesn't start on a monday, the last monday of previous month is returned.
     *
     * @return integer
     */
    public function getStartTime()
    {
        $firstDay = mktime(0, 0, 0, date('m', $this->getDisplayTime()), 1, date('Y', $this->getDisplayTime()));
        $setting = PlatformSetting :: get('first_day_of_week');

        if ($setting == 'sunday')
        {
            return strtotime('Next Sunday', strtotime('-1 Week', $firstDay));
        }

        return strtotime('Next Monday', strtotime('-1 Week', $firstDay));
    }

    /**
     * Gets the end date which will be displayed by this calendar.
     * This is always a sunday. Of the current month doesn't
     * end on a sunday, the first sunday of next month is returned.
     *
     * @return int
     */
    public function getEndTime()
    {
        $endTime = $this->getStartTime();

        while (date('Ym', $endTime) <= date('Ym', $this->getDisplayTime()))
        {
            $endTime = strtotime('+1 Week', $endTime);
        }

        return $endTime;
    }

    /**
     * Builds the table
     */
    private function buildTable()
    {
        $firstDay = mktime(0, 0, 0, date('m', $this->getDisplayTime()), 1, date('Y', $this->getDisplayTime()));
        $firstDayNr = date('w', $firstDay) == 0 ? 6 : date('w', $firstDay) - 1;
        $header = $this->getHeader();

        $setting = PlatformSetting :: get('first_day_of_week');

        if ($setting == 'sunday')
        {
            $firstTableDate = strtotime('Next Sunday', strtotime('-1 Week', $firstDay));
            $header->addRow(
                array(
                    Translation :: get('SundayLong', null, Utilities :: COMMON_LIBRARIES),
                    Translation :: get('MondayLong', null, Utilities :: COMMON_LIBRARIES),
                    Translation :: get('TuesdayLong', null, Utilities :: COMMON_LIBRARIES),
                    Translation :: get('WednesdayLong', null, Utilities :: COMMON_LIBRARIES),
                    Translation :: get('ThursdayLong', null, Utilities :: COMMON_LIBRARIES),
                    Translation :: get('FridayLong', null, Utilities :: COMMON_LIBRARIES),
                    Translation :: get('SaturdayLong', null, Utilities :: COMMON_LIBRARIES)));
        }
        else
        {
            $firstTableDate = strtotime('Next Monday', strtotime('-1 Week', $firstDay));
            $header->addRow(
                array(
                    Translation :: get('MondayLong', null, Utilities :: COMMON_LIBRARIES),
                    Translation :: get('TuesdayLong', null, Utilities :: COMMON_LIBRARIES),
                    Translation :: get('WednesdayLong', null, Utilities :: COMMON_LIBRARIES),
                    Translation :: get('ThursdayLong', null, Utilities :: COMMON_LIBRARIES),
                    Translation :: get('FridayLong', null, Utilities :: COMMON_LIBRARIES),
                    Translation :: get('SaturdayLong', null, Utilities :: COMMON_LIBRARIES),
                    Translation :: get('SundayLong', null, Utilities :: COMMON_LIBRARIES)));
        }

        $header->setRowType(0, 'th');

        $tableDate = $firstTableDate;
        $cell = 0;
        while (date('Ym', $tableDate) <= date('Ym', $this->getDisplayTime()))
        {
            do
            {
                $cellContents = date('d', $tableDate);
                $row = intval($cell / 7);
                $column = $cell % 7;
                $this->setCellContents($row, $column, $cellContents);
                $this->cellMapping[date('Ymd', $tableDate)] = array($row, $column);
                $class = array();

                // Is current table date today?
                if (date('Ymd', $tableDate) == date('Ymd'))
                {
                    $class[] = 'highlight';
                }

                // If day of week number is 0 (Sunday) or 6 (Saturday) -> it's a weekend
                if (date('w', $tableDate) % 6 == 0)
                {
                    $class[] = 'weekend';
                }

                // Is current table date in this month or another one?
                if (date('Ym', $tableDate) != date('Ym', $this->getDisplayTime()))
                {
                    $class[] = 'disabled_month';
                }

                if (count($class) > 0)
                {
                    $this->updateCellAttributes($row, $column, 'class="' . implode(' ', $class) . '"');
                }

                $cell ++;
                $tableDate = strtotime('+1 Day', $tableDate);
            }
            while ($cell % 7 != 0);
        }
    }

    /**
     * Adds the events to the calendar
     */
    public function addEvents()
    {
        $events = $this->getEventsToShow();

        foreach ($events as $time => $items)
        {
            $cellMappingKey = date('Ymd', $time);

            $row = $this->cellMapping[$cellMappingKey][0];
            $column = $this->cellMapping[$cellMappingKey][1];

            foreach ($items as $index => $item)
            {
                $cellContent = $this->getCellContents($row, $column);
                $cellContent .= $item;
                $this->setCellContents($row, $column, $cellContent);
            }
        }
    }

    /**
     * Adds a navigation bar to the calendar
     *
     * @param string $urlFormat The *TIME* in this string will be replaced by a timestamp
     */
    public function addCalendarNavigation($urlFormat)
    {
        $prev = strtotime('-1 Month', $this->getDisplayTime());
        $next = strtotime('+1 Month', $this->getDisplayTime());

        $navigation = new HTML_Table('class="calendar_navigation"');

        $navigation->updateCellAttributes(0, 0, 'class="navigation-previous" style="text-align: left;"');
        $navigation->updateCellAttributes(0, 1, 'class="navigation-title" style="text-align: center;"');
        $navigation->updateCellAttributes(0, 2, 'class="navigation-next" style="text-align: right;"');
        $navigation->setCellContents(
            0,
            0,
            '<a href="' . htmlspecialchars(str_replace(Calendar :: TIME_PLACEHOLDER, $prev, $urlFormat)) . '"><img src="' .
                 htmlspecialchars(Theme :: getInstance()->getCommonImagePath('Action/Prev')) .
                 '" style="vertical-align: middle;" alt="&lt;&lt;"/></a> ');
        $navigation->setCellContents(
            0,
            1,
            Translation :: get(date('F', $this->getDisplayTime()) . 'Long', null, Utilities :: COMMON_LIBRARIES) . ' ' .
                 date('Y', $this->getDisplayTime()));
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

    public function setNavigationHtml($navigationHtml)
    {
        $this->navigationHtml = $navigationHtml;
    }
}
