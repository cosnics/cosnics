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
    private $navigation_html = '';

    private $month_tables = array();

    /**
     * Creates a new month calendar
     *
     * @param int $display_time A time in the month to be displayed
     */
    public function __construct($display_time)
    {
        parent :: __construct($display_time);
        $this->setAttributes(array('class' => 'year_calendar_table'));
        $this->build_tables();
        $this->events_to_show = array();
    }

    /**
     * Gets the first date which will be displayed by this calendar. This is always a monday. If the current month
     * doesn't start on a monday, the last monday of previous month is returned.
     *
     * @return int
     */
    public function get_start_time()
    {
        $first_day = mktime(0, 0, 0, 1, 1, date('Y', $this->get_display_time()));
        $setting = PlatformSetting :: get('first_day_of_week');

        if ($setting == 'sunday')
        {
            $first_day = strtotime('Next Sunday', strtotime('-1 Week', $first_day));
        }
        else
        {
            $first_day = strtotime('Next Monday', strtotime('-1 Week', $first_day));
        }

        return $first_day;
    }

    /**
     * Gets the end date which will be displayed by this calendar. This is always a sunday. Of the current month doesn't
     * end on a sunday, the first sunday of next month is returned.
     *
     * @return int
     */
    public function get_end_time()
    {
        $last_day = mktime(23, 59, 59, 12, 31, date('Y', $this->get_display_time()));
        $setting = PlatformSetting :: get('first_day_of_week');

        if ($setting == 'sunday')
        {
            if (date('N', $last_day) != 6)
            {
                $last_day = strtotime('Next Saturday 23 hours 59 minutes 59 seconds', $last_day);
            }
        }
        else
        {
            if (date('N', $last_day) != 7)
            {
                $last_day = strtotime('Next Sunday 23 hours 59 minutes 59 seconds', $last_day);
            }
        }

        return $last_day;
    }

    public function build_tables()
    {
        $date_parts = getdate($this->get_display_time());

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

            $this->month_tables[$month] = new MiniMonthCalendar($time);
            $this->month_tables[$month]->set_navigation_html($navigation->toHtml());
        }
    }

    /**
     * Adds a navigation bar to the calendar
     *
     * @param string $url_format The *TIME* in this string will be replaced by a timestamp
     */
    public function add_calendar_navigation($url_format)
    {
        $prev = strtotime('-1 Year', $this->get_display_time());
        $next = strtotime('+1 Year', $this->get_display_time());
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
        $navigation->setCellContents(0, 1, date('Y', $this->get_display_time()));
        $navigation->setCellContents(
            0,
            2,
            ' <a href="' . htmlspecialchars(str_replace(Calendar :: TIME_PLACEHOLDER, $next, $url_format)) .
                 '"><img src="' . htmlspecialchars(Theme :: getInstance()->getCommonImagePath()) .
                 'action_next.png" style="vertical-align: middle;" alt="&gt;&gt;"/></a> ');
        $this->navigation_html = $navigation->toHtml();
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
            $this->setCellContents($row, $column, $this->month_tables[$month]->render());
        }

        $html[] = parent :: toHtml();
        return implode("\n", $html);
    }

    public function render()
    {
        return $this->toHtml();
    }

    public function contains_events_for_time($time)
    {
        $month = date('n', $time);
        return $this->month_tables[$month]->contains_events_for_time($time);
    }

    public function add_event($time, $content)
    {
        $month = date('n', $time);
        // $this->month_tables[$month]->add_event($time, $content);
        foreach ($this->month_tables as $month_table)
        {
            if ($time >= $month_table->get_start_time() && $time <= $month_table->get_end_time())
            {
                $month_table->add_event($time, $content);
            }
        }
    }
}
