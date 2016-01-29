<?php
namespace Chamilo\Libraries\Calendar\Table\Type;

use Chamilo\Libraries\Platform\Configuration\PlatformSetting;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 *
 * @package Chamilo\Libraries\Calendar\Table\Type
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class MiniMonthCalendar extends MonthCalendar
{
    const PERIOD_MONTH = 0;
    const PERIOD_WEEK = 1;
    const PERIOD_DAY = 2;

    /**
     *
     * @param integer $displayTime
     */
    public function __construct($displayTime)
    {
        parent :: __construct($displayTime);

        $setting = PlatformSetting :: get('first_day_of_week', 'Chamilo\Libraries\Calendar');

        if ($setting == 'sunday')
        {
            $daynames[] = Translation :: get('SundayShort', null, Utilities :: COMMON_LIBRARIES);
        }

        $daynames[] = Translation :: get('MondayShort', null, Utilities :: COMMON_LIBRARIES);
        $daynames[] = Translation :: get('TuesdayShort', null, Utilities :: COMMON_LIBRARIES);
        $daynames[] = Translation :: get('WednesdayShort', null, Utilities :: COMMON_LIBRARIES);
        $daynames[] = Translation :: get('ThursdayShort', null, Utilities :: COMMON_LIBRARIES);
        $daynames[] = Translation :: get('FridayShort', null, Utilities :: COMMON_LIBRARIES);
        $daynames[] = Translation :: get('SaturdayShort', null, Utilities :: COMMON_LIBRARIES);

        if ($setting == 'monday')
        {
            $daynames[] = Translation :: get('SundayShort', null, Utilities :: COMMON_LIBRARIES);
        }

        $this->setDaynames($daynames);
        $this->updateAttributes('class="calendar_table mini_calendar"');

        // $this->setRowType(0, 'th');
    }

    public function addNavigationLinks($urlFormat)
    {
        $day = $this->getStartTime();
        $row = 0;
        $maxRows = $this->getRowCount();

        while ($row < $maxRows)
        {
            for ($col = 0; $col < 7; $col ++)
            {
                $url = str_replace(self :: TIME_PLACEHOLDER, $day, $urlFormat);
                $content = $this->getCellContents($row, $col);
                $content = '<a href="' . $url . '">' . $content . '</a>';
                $this->setCellContents($row, $col, $content);
                $day = strtotime('+24 Hours', $day);
            }

            $row ++;
        }
    }

    public function markPeriod($period)
    {
        switch ($period)
        {
            // case self :: PERIOD_MONTH :
            // $rows = $this->getRowCount();
            // $topRow = 'style="border-left: 2px solid black;border-right: 2px solid black;border-top: 2px solid
            // black;"';
            // $middleRow = 'style="border-left: 2px solid black;border-right: 2px solid black;"';
            // $bottomRow = 'style="border-left: 2px solid black;border-right: 2px solid black;border-bottom: 2px solid
            // black;"';
            // for($row = 1; $row < $rows; $row++)
            // {
            // switch($row)
            // {
            // case 1:
            // $style = $topRow;
            // break;
            // case $rows-1:
            // $style = $bottomRow;
            // break;
            // default:
            // $style = $middleRow;
            // break;
            // }
            // $this->updateRowAttributes($row,$style,true);
            // }
            // break;
            case self :: PERIOD_WEEK :
                $monday = strtotime(date('Y-m-d 00:00:00', $this->getStartTime()));
                $thisWeek = strtotime(date('Y-m-d 00:00:00', strtotime('+1 Week', $this->getDisplayTime())));
                $weekDiff = floor(($thisWeek - $monday) / (60 * 60 * 24 * 7)) - 1;
                $this->updateRowAttributes($weekDiff, 'style="background-color: #ffdfb9;"', false);
                // $this->updateCellAttributes($row, date('N', $this->getDisplayTime()) - 1, 'style=""');
                break;

            // case self :: PERIOD_DAY :
            // $day = strtotime(date('Y-m-d 00:00:00', $this->getStartTime()));
            // $today = $this->getDisplayTime();
            // $dateDiff = floor(($today - $day) / (60 * 60 * 24));
            // $cell = $dateDiff;
            // $this->updateCellAttributes(floor($cell / 7), $cell % 7, 'style="border: 2px solid black;"');
            // break;
        }
    }

    public function toHtml()
    {
        $html = parent :: toHtml();
        return str_replace('class="calendar_navigation"', 'class="calendar_navigation mini_calendar"', $html);
    }

    public function render()
    {
        $this->addEvents();
        return $this->toHtml();
    }

    /**
     * Sets the daynames.
     * If you don't use this function, the long daynames will be displayed
     *
     * @param array $daynames An array of 7 elements with keys 0 -> 6 containing the titles to display.
     */
    public function setDaynames($daynames)
    {
        $header = $this->getHeader();

        for ($day = 0; $day < 7; $day ++)
        {
            $header->setHeaderContents(0, $day, $daynames[$day]);
        }
    }
}
