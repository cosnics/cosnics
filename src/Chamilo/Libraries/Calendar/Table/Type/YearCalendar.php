<?php
namespace Chamilo\Libraries\Calendar\Table\Type;

use Chamilo\Libraries\Calendar\Table\Calendar;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Configuration\PlatformSetting;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;
use HTML_Table;

/**
 * A tabular representation of a year calendar
 *
 * @package libraries\calendar\table
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class YearCalendar extends Calendar
{

    /**
     * The navigation links
     */
    private $navigationHtml = '';

    private $monthTables = array();

    /**
     * Creates a new month calendar
     *
     * @param int $displayTime A time in the month to be displayed
     */
    public function __construct($displayTime)
    {
        parent :: __construct($displayTime);
        $this->setAttributes(array('class' => 'year_calendar_table'));
        $this->buildTables();
    }

    /**
     * Gets the first date which will be displayed by this calendar.
     * This is always a monday. If the current month
     * doesn't start on a monday, the last monday of previous month is returned.
     *
     * @return int
     */
    public function getStartTime()
    {
        $firstDay = mktime(0, 0, 0, 1, 1, date('Y', $this->getDisplayTime()));
        $setting = PlatformSetting :: get('first_day_of_week');

        if ($setting == 'sunday')
        {
            $firstDay = strtotime('Next Sunday', strtotime('-1 Week', $firstDay));
        }
        else
        {
            $firstDay = strtotime('Next Monday', strtotime('-1 Week', $firstDay));
        }

        return $firstDay;
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
        $lastDay = mktime(23, 59, 59, 12, 31, date('Y', $this->getDisplayTime()));
        $setting = PlatformSetting :: get('first_day_of_week');

        if ($setting == 'sunday')
        {
            if (date('N', $lastDay) != 6)
            {
                $lastDay = strtotime('Next Saturday 23 hours 59 minutes 59 seconds', $lastDay);
            }
        }
        else
        {
            if (date('N', $lastDay) != 7)
            {
                $lastDay = strtotime('Next Sunday 23 hours 59 minutes 59 seconds', $lastDay);
            }
        }

        return $lastDay;
    }

    public function buildTables()
    {
        $date_parts = getdate($this->getDisplayTime());

        for ($month = 1; $month <= 12; $month ++)
        {
            $time = mktime(
                $date_parts['hours'],
                $date_parts['minutes'],
                $date_parts['seconds'],
                $month,
                $date_parts['mday'],
                $date_parts['year']);

            $navigation = new HTML_Table('class="calendar_navigation"');
            $navigation->updateCellAttributes(0, 0, 'style="text-align: center;"');
            $navigation->setCellContents(
                0,
                0,
                Translation :: get(date('F', $time) . 'Long', null, Utilities :: COMMON_LIBRARIES)/* . ' ' . date('Y', $time)*/);

            $this->monthTables[$month] = new MiniMonthCalendar($time);
            $this->monthTables[$month]->setNavigationHtml($navigation->toHtml());
        }
    }

    /**
     * Adds a navigation bar to the calendar
     *
     * @param string $url_format The *TIME* in this string will be replaced by a timestamp
     */
    public function addCalendarNavigation($url_format)
    {
        $prev = strtotime('-1 Year', $this->getDisplayTime());
        $next = strtotime('+1 Year', $this->getDisplayTime());
        $navigation = new HTML_Table('class="calendar_navigation"');
        $navigation->updateCellAttributes(0, 0, 'style="text-align: left;"');
        $navigation->updateCellAttributes(0, 1, 'style="text-align: center;"');
        $navigation->updateCellAttributes(0, 2, 'style="text-align: right;"');
        $navigation->setCellContents(
            0,
            0,
            '<a href="' . htmlspecialchars(str_replace(Calendar :: TIME_PLACEHOLDER, $prev, $url_format)) .
                 '"><img src="' . htmlspecialchars(Theme :: getInstance()->getCommonImagePath('Action/Prev')) .
                 '" style="vertical-align: middle;" alt="&lt;&lt;"/></a> ');
        $navigation->setCellContents(0, 1, date('Y', $this->getDisplayTime()));
        $navigation->setCellContents(
            0,
            2,
            ' <a href="' . htmlspecialchars(str_replace(Calendar :: TIME_PLACEHOLDER, $next, $url_format)) .
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

        for ($month = 1; $month <= 12; $month ++)
        {
            $row = intval(($month - 1) / 3);
            $column = ($month - 1) % 3;

            $padding_right = ($month % 3 == 0) ? '0px' : '15px';
            $padding_bottom = ($month < 10) ? '15px' : '0px';

            $this->setCellAttributes(
                $row,
                $column,
                array(
                    'style' => 'vertical-align: top; padding: 0px ' . $padding_right . ' ' . $padding_bottom . ' 0px;'));
            $this->setCellContents($row, $column, $this->monthTables[$month]->render());
        }

        $html[] = parent :: toHtml();
        return implode(PHP_EOL, $html);
    }

    public function render()
    {
        return $this->toHtml();
    }

    public function containsEventsForTime($time)
    {
        $month = date('n', $time);
        return $this->monthTables[$month]->containsEventsForTime($time);
    }

    public function addEvent($time, $content)
    {
        $month = date('n', $time);

        foreach ($this->monthTables as $month_table)
        {
            if ($time >= $month_table->getStartTime() && $time <= $month_table->getEndTime())
            {
                $month_table->addEvent($time, $content);
            }
        }
    }
}
