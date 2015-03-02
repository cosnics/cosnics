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
     */
    private $cell_mapping;

    /**
     * The navigation links
     */
    private $navigation_html;

    /**
     * Creates a new month calendar
     *
     * @param int $display_time A time in the month to be displayed
     */
    public function __construct($display_time)
    {
        $this->navigation_html = '';
        parent :: __construct($display_time);
        $cell_mapping = array();
        $this->build_table();
        $this->events_to_show = array();
    }

    /**
     * Gets the first date which will be displayed by this calendar.
     * This is always a monday. If the current month
     * doesn't start on a monday, the last monday of previous month is returned.
     *
     * @return int
     */
    public function get_start_time()
    {
        $first_day = mktime(0, 0, 0, date('m', $this->get_display_time()), 1, date('Y', $this->get_display_time()));
        $setting = PlatformSetting :: get('first_day_of_week');

        if ($setting == 'sunday')
        {
            return strtotime('Next Sunday', strtotime('-1 Week', $first_day));
        }

        return strtotime('Next Monday', strtotime('-1 Week', $first_day));
    }

    /**
     * Gets the end date which will be displayed by this calendar.
     * This is always a sunday. Of the current month doesn't
     * end on a sunday, the first sunday of next month is returned.
     *
     * @return int
     */
    public function get_end_time()
    {
        $end_time = $this->get_start_time();
        while (date('Ym', $end_time) <= date('Ym', $this->get_display_time()))
        {
            $end_time = strtotime('+1 Week', $end_time);
        }
        return $end_time;
    }

    /**
     * Builds the table
     */
    private function build_table()
    {
        $first_day = mktime(0, 0, 0, date('m', $this->get_display_time()), 1, date('Y', $this->get_display_time()));
        $first_day_nr = date('w', $first_day) == 0 ? 6 : date('w', $first_day) - 1;
        $header = $this->getHeader();

        $setting = PlatformSetting :: get('first_day_of_week');

        if ($setting == 'sunday')
        {
            $first_table_date = strtotime('Next Sunday', strtotime('-1 Week', $first_day));
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
            $first_table_date = strtotime('Next Monday', strtotime('-1 Week', $first_day));
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

        $table_date = $first_table_date;
        $cell = 0;
        while (date('Ym', $table_date) <= date('Ym', $this->get_display_time()))
        {
            do
            {
                $cell_contents = date('d', $table_date);
                $row = intval($cell / 7);
                $column = $cell % 7;
                $this->setCellContents($row, $column, $cell_contents);
                $this->cell_mapping[date('Ymd', $table_date)] = array($row, $column);
                $class = array();
                // Is current table date today?
                if (date('Ymd', $table_date) == date('Ymd'))
                {
                    $class[] = 'highlight';
                }
                // If day of week number is 0 (Sunday) or 6 (Saturday) -> it's a weekend
                if (date('w', $table_date) % 6 == 0)
                {
                    $class[] = 'weekend';
                }
                // Is current table date in this month or another one?
                if (date('Ym', $table_date) != date('Ym', $this->get_display_time()))
                {
                    $class[] = 'disabled_month';
                }
                if (count($class) > 0)
                {
                    $this->updateCellAttributes($row, $column, 'class="' . implode(' ', $class) . '"');
                }
                $cell ++;
                $table_date = strtotime('+1 Day', $table_date);
            }
            while ($cell % 7 != 0);
        }

        // $this->setRowType(0, 'th');
    }

    /**
     * Adds the events to the calendar
     */
    public function add_events()
    {
        $events = $this->get_events_to_show();
        foreach ($events as $time => $items)
        {
            $cell_mapping_key = date('Ymd', $time);
            $row = $this->cell_mapping[$cell_mapping_key][0];
            $column = $this->cell_mapping[$cell_mapping_key][1];
            foreach ($items as $index => $item)
            {
                $cell_content = $this->getCellContents($row, $column);
                $cell_content .= $item;
                $this->setCellContents($row, $column, $cell_content);
            }
        }
    }

    /**
     * Adds a navigation bar to the calendar
     *
     * @param string $url_format The *TIME* in this string will be replaced by a timestamp
     */
    public function add_calendar_navigation($url_format)
    {
        $prev = strtotime('-1 Month', $this->get_display_time());
        $next = strtotime('+1 Month', $this->get_display_time());
        $navigation = new HTML_Table('class="calendar_navigation"');
        $navigation->updateCellAttributes(0, 0, 'style="text-align: left;"');
        $navigation->updateCellAttributes(0, 1, 'style="text-align: center;"');
        $navigation->updateCellAttributes(0, 2, 'style="text-align: right;"');
        $navigation->setCellContents(
            0,
            0,
            '<a href="' . htmlspecialchars(str_replace(Calendar :: TIME_PLACEHOLDER, $prev, $url_format)) .
                 '"><img src="' . htmlspecialchars(Theme :: getInstance()->getCommonImagePath('action_prev')) .
                 '" style="vertical-align: middle;" alt="&lt;&lt;"/></a> ');
        $navigation->setCellContents(
            0,
            1,
            Translation :: get(date('F', $this->get_display_time()) . 'Long', null, Utilities :: COMMON_LIBRARIES) . ' ' .
                 date('Y', $this->get_display_time()));
        $navigation->setCellContents(
            0,
            2,
            ' <a href="' . htmlspecialchars(str_replace(Calendar :: TIME_PLACEHOLDER, $next, $url_format)) .
                 '"><img src="' . htmlspecialchars(Theme :: getInstance()->getCommonImagePath('action_next')) .
                 '" style="vertical-align: middle;" alt="&gt;&gt;"/></a> ');
        $this->navigation_html = $navigation->toHtml();
    }

    /**
     * Sets the daynames.
     * If you don't use this function, the long daynames will be displayed
     *
     * @param array $daynames An array of 7 elements with keys 0 -> 6 containing the titles to display.
     */
    public function set_daynames($daynames)
    {
        $header = $this->getHeader();
        for ($day = 0; $day < 7; $day ++)
        {
            $header->setHeaderContents(0, $day, $daynames[$day]);
        }
    }

    /**
     * Returns a html-representation of this monthcalendar
     *
     * @return string
     */
    public function toHtml()
    {
        $html = array();
        $html[] = $this->navigation_html;
        $html[] = parent :: toHtml();
        return implode(PHP_EOL, $html);
    }

    public function render()
    {
        $this->add_events();
        return $this->toHtml();
    }

    public function set_navigation_html($navigation_html)
    {
        $this->navigation_html = $navigation_html;
    }
}
