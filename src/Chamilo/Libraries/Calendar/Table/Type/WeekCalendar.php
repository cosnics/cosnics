<?php
namespace Chamilo\Libraries\Calendar\Table\Type;







use Chamilo\Libraries\Calendar\Table\Calendar;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Configuration\LocalSetting;
use Chamilo\Libraries\Platform\Configuration\PlatformSetting;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;
use HTML_Table;

/**
 * A tabular representation of a week calendar
 *
 * @package libraries\calendar\table$WeekCalendar
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class WeekCalendar extends Calendar
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
     * Creates a new week calendar
     *
     * @param int $display_time A time in the week to be displayed
     * @param int $hour_step The number of hours for one table cell. Defaults to 2.
     */
    public function __construct($display_time, $hour_step = 2)
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
     * Gets the first date which will be displayed by this calendar. This is always a monday.
     *
     * @return int
     */
    public function get_start_time()
    {
        $setting = PlatformSetting :: get('first_day_of_week');

        if ($setting == 'sunday')
            return strtotime('Next Sunday', strtotime('-1 Week', $this->get_display_time()));

        return strtotime('Next Monday', strtotime('-1 Week', $this->get_display_time()));
    }

    /**
     * Gets the end date which will be displayed by this calendar. This is always a sunday.
     *
     * @return int
     */
    public function get_end_time()
    {
        $setting = PlatformSetting :: get('first_day_of_week');

        if ($setting == 'sunday')
            return strtotime('Next Saterday', strtotime('-1 Week', $this->get_display_time()));

        return strtotime('Next Sunday', $this->get_start_time());
    }

    /**
     * Builds the table
     */
    private function build_table()
    {
        $header = $this->getHeader();
        $header->setRowType(0, 'th');
        $header->setHeaderContents(0, 0, '');

        $week_number = date('W', $this->get_display_time());
        // Go 1 week back end them jump to the next monday to reach the first day of this week
        $first_day = $this->get_start_time();
        $last_day = $this->get_end_time;

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

        for ($hour = $start; $hour < $end; $hour += $this->hour_step)
        {
            $cell_content = $hour . Translation :: get('h', null, Utilities :: COMMON_LIBRARIES) . ' - ' .
                 ($hour + $this->hour_step) . Translation :: get('h', null, Utilities :: COMMON_LIBRARIES);
            $this->setCellContents(($hour / $this->hour_step) - $start, 0, $cell_content);
        }

        $this->updateColAttributes(0, 'class="week_hours"');
        $dates[] = '';
        $today = date('Y-m-d');
        for ($day = 0; $day < 7; $day ++)
        {
            $week_day = strtotime('+' . $day . ' days', $first_day);
            $header->setHeaderContents(
                0,
                $day + 1,
                Translation :: get(date('l', $week_day) . 'Long', null, Utilities :: COMMON_LIBRARIES) . '<br/>' .
                     date('Y-m-d', $week_day));

            for ($hour = $start; $hour < $end; $hour += $this->hour_step)
            {
                $row = ($hour / $this->hour_step) - $start;
                $class = array();
                if ($today == date('Y-m-d', $week_day))
                {
                    if (date('H') >= $hour && date('H') < $hour + $this->hour_step)
                    {
                        $class[] = 'highlight';
                    }
                }
                // If day of week number is 0 (Sunday) or 6 (Saturday) -> it's a weekend
                if (date('w', $week_day) % 6 == 0)
                {
                    $class[] = 'weekend';
                }
                if (count($class) > 0)
                {
                    $this->updateCellAttributes($row, $day + 1, 'class="' . implode(' ', $class) . '"');
                }

                if ($hour < $working_start || $hour >= $working_end)
                {
                    $this->updateCellAttributes($row, $day + 1, 'class="disabled_month"');
                }
            }
        }

        // $this->setRowType(0,'th');
        // $this->setColType(0,'th');
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
            $row = (date('H', $time) / $this->hour_step) - $start;
            if ($row > $end - $start - 1)
            {
                continue;
            }

            $column = date('w', $time);

            if ($column == 0)
            {
                $column = 7;
            }
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
        $week_number = date('W', $this->get_display_time());
        $prev = strtotime('-1 Week', $this->get_display_time());
        $next = strtotime('+1 Week', $this->get_display_time());
        $navigation = new HTML_Table('class="calendar_navigation"');
        $navigation->updateCellAttributes(0, 0, 'style="text-align: left;"');
        $navigation->updateCellAttributes(0, 1, 'style="text-align: center;"');
        $navigation->updateCellAttributes(0, 2, 'style="text-align: right;"');
        $navigation->setCellContents(
            0,
            0,
            '<a href="' . htmlspecialchars(str_replace(Calendar :: TIME_PLACEHOLDER, $prev, $url_format)) .
                 '"><img src="' . htmlspecialchars(Theme :: getInstance()->getCommonImagePath()) .
                 'action_prev.png" style="vertical-align: middle;" alt="&lt;&lt;"/></a> ');
        $navigation->setCellContents(
            0,
            1,
            htmlentities(Translation :: get('Week', null, Utilities :: COMMON_LIBRARIES)) . ' ' . $week_number . ' : ' .
                 date('l d M Y', $this->get_start_time()) . ' - ' .
                 date('l d M Y', strtotime('+6 Days', $this->get_start_time())));
        $navigation->setCellContents(
            0,
            2,
            ' <a href="' . htmlspecialchars(str_replace(Calendar :: TIME_PLACEHOLDER, $next, $url_format)) .
                 '"><img src="' . htmlspecialchars(Theme :: getInstance()->getCommonImagePath()) .
                 'action_next.png" style="vertical-align: middle;" alt="&gt;&gt;"/></a> ');
        $this->navigation_html = $navigation->toHtml();
    }

    /**
     * Sets the daynames. If you don't use this function, the long daynames will be displayed
     *
     * @param array $daynames An array of 7 elements with keys 0 -> 6 containing the titles to display.
     */
    public function set_daynames($daynames)
    {
        $header = $this->getHeader();
        for ($day = 0; $day < 7; $day ++)
        {
            $header->setHeaderContents(0, $day + 1, $daynames[$day]);
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
