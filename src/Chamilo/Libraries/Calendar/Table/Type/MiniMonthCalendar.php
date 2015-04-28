<?php
namespace Chamilo\Libraries\Calendar\Table\Type;



use Chamilo\Libraries\Platform\Configuration\PlatformSetting;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * A tabular representation of a mini month calendar
 *
 * @package libraries\calendar\table
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class MiniMonthCalendar extends MonthCalendar
{
    const PERIOD_MONTH = 0;
    const PERIOD_WEEK = 1;
    const PERIOD_DAY = 2;

    public function __construct($display_time)
    {
        parent :: __construct($display_time);

        $setting = PlatformSetting :: get('first_day_of_week');

        if ($setting == 'sunday')
            $daynames[] = Translation :: get('SundayShort', null, Utilities :: COMMON_LIBRARIES);

        $daynames[] = Translation :: get('MondayShort', null, Utilities :: COMMON_LIBRARIES);
        $daynames[] = Translation :: get('TuesdayShort', null, Utilities :: COMMON_LIBRARIES);
        $daynames[] = Translation :: get('WednesdayShort', null, Utilities :: COMMON_LIBRARIES);
        $daynames[] = Translation :: get('ThursdayShort', null, Utilities :: COMMON_LIBRARIES);
        $daynames[] = Translation :: get('FridayShort', null, Utilities :: COMMON_LIBRARIES);
        $daynames[] = Translation :: get('SaturdayShort', null, Utilities :: COMMON_LIBRARIES);

        if ($setting == 'monday')
            $daynames[] = Translation :: get('SundayShort', null, Utilities :: COMMON_LIBRARIES);

        $this->set_daynames($daynames);
        $this->updateAttributes('class="calendar_table mini_calendar"');

        // $this->setRowType(0, 'th');
    }

    public function add_navigation_links($url_format)
    {
        $day = $this->get_start_time();
        $row = 0;
        $max_rows = $this->getRowCount();
        while ($row < $max_rows)
        {
            for ($col = 0; $col < 7; $col ++)
            {
                $url = str_replace(self :: TIME_PLACEHOLDER, $day, $url_format);
                $content = $this->getCellContents($row, $col);
                $content = '<a href="' . $url . '">' . $content . '</a>';
                $this->setCellContents($row, $col, $content);
                $day = strtotime('+24 Hours', $day);
            }
            $row ++;
        }
    }

    public function mark_period($period)
    {
        switch ($period)
        {
            // case self :: PERIOD_MONTH :
            // $rows = $this->getRowCount();
            // $top_row = 'style="border-left: 2px solid black;border-right: 2px solid black;border-top: 2px solid
            // black;"';
            // $middle_row = 'style="border-left: 2px solid black;border-right: 2px solid black;"';
            // $bottom_row = 'style="border-left: 2px solid black;border-right: 2px solid black;border-bottom: 2px solid
            // black;"';
            // for($row = 1; $row < $rows; $row++)
            // {
            // switch($row)
            // {
            // case 1:
            // $style = $top_row;
            // break;
            // case $rows-1:
            // $style = $bottom_row;
            // break;
            // default:
            // $style = $middle_row;
            // break;
            // }
            // $this->updateRowAttributes($row,$style,true);
            // }
            // break;
            case self :: PERIOD_WEEK :
                $monday = strtotime(date('Y-m-d 00:00:00', $this->get_start_time()));
                $this_week = strtotime(date('Y-m-d 00:00:00', strtotime('+1 Week', $this->get_display_time())));
                $week_diff = floor(($this_week - $monday) / (60 * 60 * 24 * 7)) - 1;
                $row = $week_diff;
                $this->updateRowAttributes($row, 'style="background-color: #ffdfb9;"', false);
                // $this->updateCellAttributes($row, date('N', $this->get_display_time()) - 1, 'style=""');
                break;

            // case self :: PERIOD_DAY :
            // $day = strtotime(date('Y-m-d 00:00:00', $this->get_start_time()));
            // $today = $this->get_display_time();
            // $date_diff = floor(($today - $day) / (60 * 60 * 24));
            // $cell = $date_diff;
            // $this->updateCellAttributes(floor($cell / 7), $cell % 7, 'style="border: 2px solid black;"');
            // break;
        }
    }

    public function toHtml()
    {
        $html = parent :: toHtml();
        $html = str_replace('class="calendar_navigation"', 'class="calendar_navigation mini_calendar"', $html);
        return $html;
    }

    public function render()
    {
        $this->add_events();
        return $this->toHtml();
    }
}
