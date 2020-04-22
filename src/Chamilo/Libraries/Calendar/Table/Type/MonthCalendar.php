<?php
namespace Chamilo\Libraries\Calendar\Table\Type;

use Chamilo\Configuration\Configuration;
use Chamilo\Libraries\Calendar\Table\Calendar;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\Utilities;
use Exception;

/**
 *
 * @package Chamilo\Libraries\Calendar\Table\Type
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class MonthCalendar extends Calendar
{
    const TIME_PLACEHOLDER = '__TIME__';

    /**
     * Keep mapping of dates and their corresponding table cells
     *
     * @var integer[]
     */
    private $cellMapping;

    /**
     *
     * @var string
     */
    private $dayUrlTemplate;

    /**
     * Creates a new month calendar
     *
     * @param integer $displayTime
     * @param string $dayUrlTemplate
     * @param string[] $classes
     */
    public function __construct($displayTime, $dayUrlTemplate = null, $classes = array())
    {
        parent::__construct($displayTime, $classes);

        $this->cellMapping = array();
        $this->dayUrlTemplate = $dayUrlTemplate;

        $this->buildTable();
    }

    /**
     *
     * @return string
     */
    public function render()
    {
        $this->addEvents();

        return $this->toHtml();
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

            if (is_null($row) || is_null($column))
            {
                continue;
            }

            foreach ($items as $index => $item)
            {
                try
                {
                    $cellContent = $this->getCellContents($row, $column);
                    $cellContent .= $item;
                    $this->setCellContents($row, $column, $cellContent);
                }
                catch (Exception $exception)
                {
                }
            }
        }
    }

    /**
     * Builds the table
     */
    private function buildTable()
    {
        $firstDay = mktime(0, 0, 0, date('m', $this->getDisplayTime()), 1, date('Y', $this->getDisplayTime()));
        $tableDate = $this->getFirstTableDate($firstDay);
        $cell = 0;

        while (date('Ym', $tableDate) <= date('Ym', $this->getDisplayTime()))
        {
            do
            {
                $row = intval($cell / 7);
                $column = $cell % 7;

                $this->cellMapping[date('Ymd', $tableDate)] = array($row, $column);

                $classes = $this->determineCellClasses($tableDate);

                if (count($classes) > 0)
                {
                    $this->updateCellAttributes($row, $column, 'class="' . implode(' ', $classes) . '"');
                }

                $this->setCellContents($row, $column, $this->determineCellContent($tableDate));

                $cell ++;
                $tableDate = strtotime('+1 Day', $tableDate);
            }
            while ($cell % 7 != 0);
        }

        $this->setHeader();
    }

    /**
     *
     * @param integer $tableDate
     *
     * @return string[]
     */
    protected function determineCellClasses($tableDate)
    {
        $classes = array();

        // Is current table date today?
        if (date('Ymd', $tableDate) == date('Ymd'))
        {
            $classes[] = 'table-calendar-highlight';
        }

        // If day of week number is 0 (Sunday) or 6 (Saturday) -> it's a weekend
        if (date('w', $tableDate) % 6 == 0)
        {
            $classes[] = 'table-calendar-weekend';
        }

        // Is current table date in this month or another one?
        if (date('Ym', $tableDate) != date('Ym', $this->getDisplayTime()))
        {
            $classes[] = 'table-calendar-disabled';
        }

        return $classes;
    }

    /**
     *
     * @param integer $tableDate
     *
     * @return string
     */
    protected function determineCellContent($tableDate)
    {
        $dayLabel = date('j', $tableDate);
        $dayUrlTemplate = $this->getDayUrlTemplate();

        if (is_null($dayUrlTemplate))
        {
            return $dayLabel;
        }
        else
        {
            return '<a href="' . $this->getDayUrl($tableDate) . '">' . $dayLabel . '</a>';
        }
    }

    /**
     *
     * @return integer[]
     */
    public function getCellMapping()
    {
        return $this->cellMapping;
    }

    /**
     *
     * @param integer $time
     *
     * @return string
     */
    public function getDayUrl($time)
    {
        return str_replace(self::TIME_PLACEHOLDER, $time, $this->getDayUrlTemplate());
    }

    /**
     *
     * @return string
     */
    public function getDayUrlTemplate()
    {
        return $this->dayUrlTemplate;
    }

    /**
     *
     * @param string $dayUrlTemplate
     */
    public function setDayUrlTemplate($dayUrlTemplate)
    {
        $this->dayUrlTemplate = $dayUrlTemplate;
    }

    /**
     * Gets the end date which will be displayed by this calendar.
     * This is always a sunday. Of the current month doesn't
     * end on a sunday, the first sunday of next month is returned.
     *
     * @return integer
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
     *
     * @param integer $firstDay
     *
     * @return integer
     */
    protected function getFirstTableDate($firstDay)
    {
        $setting = Configuration::getInstance()->get_setting(array('Chamilo\Libraries\Calendar', 'first_day_of_week'));

        if ($setting == 'sunday')
        {
            return strtotime('Next Sunday', strtotime('-1 Week', $firstDay));
        }
        else
        {
            return strtotime('Next Monday', strtotime('-1 Week', $firstDay));
        }
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
        $setting = Configuration::getInstance()->get_setting(array('Chamilo\Libraries\Calendar', 'first_day_of_week'));

        if ($setting == 'sunday')
        {
            return strtotime('Next Sunday', strtotime('-1 Week', $firstDay));
        }

        return strtotime('Next Monday', strtotime('-1 Week', $firstDay));
    }

    protected function setHeader()
    {
        $header = $this->getHeader();

        $setting = Configuration::getInstance()->get_setting(array('Chamilo\Libraries\Calendar', 'first_day_of_week'));

        if ($setting == 'sunday')
        {
            $header->addRow(
                array(
                    Translation::get('SundayShort', null, Utilities::COMMON_LIBRARIES),
                    Translation::get('MondayShort', null, Utilities::COMMON_LIBRARIES),
                    Translation::get('TuesdayShort', null, Utilities::COMMON_LIBRARIES),
                    Translation::get('WednesdayShort', null, Utilities::COMMON_LIBRARIES),
                    Translation::get('ThursdayShort', null, Utilities::COMMON_LIBRARIES),
                    Translation::get('FridayShort', null, Utilities::COMMON_LIBRARIES),
                    Translation::get('SaturdayShort', null, Utilities::COMMON_LIBRARIES)
                )
            );
        }
        else
        {
            $header->addRow(
                array(
                    Translation::get('MondayShort', null, Utilities::COMMON_LIBRARIES),
                    Translation::get('TuesdayShort', null, Utilities::COMMON_LIBRARIES),
                    Translation::get('WednesdayShort', null, Utilities::COMMON_LIBRARIES),
                    Translation::get('ThursdayShort', null, Utilities::COMMON_LIBRARIES),
                    Translation::get('FridayShort', null, Utilities::COMMON_LIBRARIES),
                    Translation::get('SaturdayShort', null, Utilities::COMMON_LIBRARIES),
                    Translation::get('SundayShort', null, Utilities::COMMON_LIBRARIES)
                )
            );
        }

        $header->setRowType(0, 'th');
    }
}
