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
    private $navigation_html;

    /**
     * The number of hours for one table cell.
     */
    private $hour_step;

    /**
     * Creates a new day calendar
     *
     * @param int $display_time A time in the day to be displayed
     * @param int $hour_step The number of hours for one table cell. Defaults to 1.
     */
    public function __construct($display_time, $hour_step = 1)
    {
        $this->navigation_html = '';
        $this->hour_step = $hour_step;
        parent :: __construct($display_time);
        $cell_mapping = array();
        $this->build_table();
    }

    /**
     * Gets the number of hours for one table cell.
     *
     * @return int
     */
    public function get_hour_step()
    {
        return $this->hour_step;
    }

    /**
     * Sets the number of hours for one table cell.
     *
     * @return int
     */
    public function set_hour_step($hour_step)
    {
        $this->hour_step = $hour_step;
    }

    /**
     * Gets the first date which will be displayed by this calendar.
     *
     * @return int
     */
    public function get_start_time()
    {
        $hide = LocalSetting :: get('hide_none_working_hours');

        if ($hide)
        {
            $working_start = LocalSetting :: get('working_hours_start');
            return strtotime(date('Y-m-d ' . $working_start . ':00:00', $this->get_display_time()));
        }
        return strtotime(date('Y-m-d 00:00:00', $this->get_display_time()));
    }

    /**
     * Gets the end date which will be displayed by this calendar.
     *
     * @return int
     */
    public function get_end_time()
    {
        $hide = LocalSetting :: get('hide_none_working_hours');

        if ($hide)
        {
            $working_end = LocalSetting :: get('working_hours_end');
            return strtotime(date('Y-m-d ' . ($working_end - 1) . ':59:59', $this->get_display_time()));
        }

        return strtotime('+24 Hours', $this->get_start_time());
    }

    /**
     * Builds the table
     */
    protected function build_table()
    {
        $year_day = date('z', $this->get_display_time()) + 1;
        $year_week = date('W', $this->get_display_time());

        $header = $this->getHeader();
        $header->addRow(
            array(
                Translation :: get('Day', null, Utilities :: COMMON_LIBRARIES) . ' ' . $year_day . ', ' .
                     Translation :: get('Week', null, Utilities :: COMMON_LIBRARIES) . ' ' . $year_week));
        $header->setRowType(0, 'th');

        $working_start = LocalSetting :: get('working_hours_start');
        $working_end = LocalSetting :: get('working_hours_end');
        $hide = LocalSetting :: get('hide_none_working_hours');
        $start = 0;
        $end = 24;

        if ($hide)
        {
            $start = $working_start;
            $end = $working_end;
        }

        for ($hour = $start; $hour < $end; $hour += $this->get_hour_step())
        {
            $table_start_date = mktime(
                $hour,
                0,
                0,
                date('m', $this->get_display_time()),
                date('d', $this->get_display_time()),
                date('Y', $this->get_display_time()));
            $table_end_date = strtotime('+' . $this->get_hour_step() . ' hours', $table_start_date);
            $cell_contents = $hour . Translation :: get('h', null, Utilities :: COMMON_LIBRARIES) . ' - ' .
                 ($hour + $this->get_hour_step()) . Translation :: get('h', null, Utilities :: COMMON_LIBRARIES);

            $row = ($hour - $start) / $this->get_hour_step();

            $this->setCellContents($row, 0, $cell_contents);
            // Highlight current hour
            if (date('Y-m-d') == date('Y-m-d', $this->get_display_time()))
            {
                if (date('H') >= $hour && date('H') < $hour + $this->get_hour_step())
                {
                    $this->updateCellAttributes($row, 0, 'class="highlight"');
                }
            }

            // Is current table hour during working hours?
            if ($hour < $working_start || $hour >= $working_end)
            {
                $this->updateCellAttributes($row, 0, 'class="disabled_month"');
            }
        }
    }

    /**
     * Adds the events to the calendar
     */
    private function add_events()
    {
        $events = $this->get_events_to_show();

        $working_start = LocalSetting :: get('working_hours_start');
        $working_end = LocalSetting :: get('working_hours_end');
        $hide = LocalSetting :: get('hide_none_working_hours');
        $start = 0;
        $end = 24;

        if ($hide)
        {
            $start = $working_start;
            $end = $working_end;
        }

        foreach ($events as $time => $items)
        {
            if ($time >= $this->get_end_time())
            {
                continue;
            }
            $row = (date('H', $time) - $start) / $this->hour_step;
            foreach ($items as $index => $item)
            {
                $cell_content = $this->getCellContents($row, 0);
                $cell_content .= $item;
                $this->setCellContents($row, 0, $cell_content);
            }
        }
    }

    /**
     * Adds a navigation bar to the calendar
     *
     * @param string $url_format The -TIME- in this string will be replaced by a timestamp
     */
    public function add_calendar_navigation($url_format)
    {
        $prev = strtotime('-1 Day', $this->get_display_time());
        $next = strtotime('+1 Day', $this->get_display_time());
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
        $navigation->setCellContents(0, 1, date('l d F Y', $this->get_display_time()));
        $navigation->setCellContents(
            0,
            2,
            ' <a href="' . htmlspecialchars(str_replace(Calendar :: TIME_PLACEHOLDER, $next, $url_format)) .
                 '"><img src="' . htmlspecialchars(Theme :: getInstance()->getCommonImagePath('Action/Next')) .
                 '" style="vertical-align: middle;" alt="&gt;&gt;"/></a> ');
        $this->navigation_html = $navigation->toHtml();
    }

    /**
     * Sets the daynames. If you don't use this function, the long daynames will be displayed
     *
     * @param array $daynames An array of 7 elements with keys 0 -> 6 containing the titles to display.
     */
    public function set_daynames($daynames)
    {
        for ($day = 0; $day < 7; $day ++)
        {
            $this->setCellContents(0, $day + 1, $daynames[$day]);
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
}
