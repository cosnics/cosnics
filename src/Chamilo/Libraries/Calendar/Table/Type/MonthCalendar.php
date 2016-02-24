<?php
namespace Chamilo\Libraries\Calendar\Table\Type;

use Chamilo\Libraries\Calendar\Table\Calendar;
use Chamilo\Libraries\Platform\Configuration\PlatformSetting;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

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
     * Creates a new month calendar
     *
     * @param integer $displayTime A time in the month to be displayed
     */
    public function __construct($displayTime, $classes = array())
    {
        parent :: __construct($displayTime, $classes);
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
        $setting = PlatformSetting :: get('first_day_of_week', 'Chamilo\Libraries\Calendar');

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

        $setting = PlatformSetting :: get('first_day_of_week', 'Chamilo\Libraries\Calendar');

        if ($setting == 'sunday')
        {
            $firstTableDate = strtotime('Next Sunday', strtotime('-1 Week', $firstDay));
            $header->addRow(
                array(
                    Translation :: get('SundayShort', null, Utilities :: COMMON_LIBRARIES),
                    Translation :: get('MondayShort', null, Utilities :: COMMON_LIBRARIES),
                    Translation :: get('TuesdayShort', null, Utilities :: COMMON_LIBRARIES),
                    Translation :: get('WednesdayShort', null, Utilities :: COMMON_LIBRARIES),
                    Translation :: get('ThursdayShort', null, Utilities :: COMMON_LIBRARIES),
                    Translation :: get('FridayShort', null, Utilities :: COMMON_LIBRARIES),
                    Translation :: get('SaturdayShort', null, Utilities :: COMMON_LIBRARIES)));
        }
        else
        {
            $firstTableDate = strtotime('Next Monday', strtotime('-1 Week', $firstDay));
            $header->addRow(
                array(
                    Translation :: get('MondayShort', null, Utilities :: COMMON_LIBRARIES),
                    Translation :: get('TuesdayShort', null, Utilities :: COMMON_LIBRARIES),
                    Translation :: get('WednesdayShort', null, Utilities :: COMMON_LIBRARIES),
                    Translation :: get('ThursdayShort', null, Utilities :: COMMON_LIBRARIES),
                    Translation :: get('FridayShort', null, Utilities :: COMMON_LIBRARIES),
                    Translation :: get('SaturdayShort', null, Utilities :: COMMON_LIBRARIES),
                    Translation :: get('SundayShort', null, Utilities :: COMMON_LIBRARIES)));
        }

        $header->setRowType(0, 'th');

        $tableDate = $firstTableDate;
        $cell = 0;
        while (date('Ym', $tableDate) <= date('Ym', $this->getDisplayTime()))
        {
            do
            {
                $cellContents = date('j', $tableDate);
                $row = intval($cell / 7);
                $column = $cell % 7;
                $this->setCellContents($row, $column, $cellContents);
                $this->cellMapping[date('Ymd', $tableDate)] = array($row, $column);
                $class = array();

                // Is current table date today?
                if (date('Ymd', $tableDate) == date('Ymd'))
                {
                    $class[] = 'table-calendar-highlight';
                }

                // If day of week number is 0 (Sunday) or 6 (Saturday) -> it's a weekend
                if (date('w', $tableDate) % 6 == 0)
                {
                    $class[] = 'table-calendar-weekend';
                }

                // Is current table date in this month or another one?
                if (date('Ym', $tableDate) != date('Ym', $this->getDisplayTime()))
                {
                    $class[] = 'table-calendar-disabled-month';
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

    public function render()
    {
        $this->addEvents();
        return $this->toHtml();
    }

    public function getCellMapping()
    {
        return $this->cellMapping;
    }
}
