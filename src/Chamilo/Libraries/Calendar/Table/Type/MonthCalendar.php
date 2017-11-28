<?php
namespace Chamilo\Libraries\Calendar\Table\Type;

use Chamilo\Libraries\Calendar\Table\Calendar;

/**
 *
 * @package Chamilo\Libraries\Calendar\Table\Type
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
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
     * @param integer $displayTime
     * @param string[] $classes
     */
    public function __construct($displayTime, $classes = [])
    {
        parent::__construct($displayTime, $classes);

        $this->cellMapping = array();
    }

    /**
     * Gets the first date which will be displayed by this calendar.
     * This is always a monday. If the current month
     * doesn't start on a monday, the last monday of previous month is returned.
     *
     * @param string $firstDayOfTheWeek
     * @return integer
     */
    public function getStartTime($firstDayOfTheWeek = null)
    {
        $firstDay = mktime(0, 0, 0, date('m', $this->getDisplayTime()), 1, date('Y', $this->getDisplayTime()));

        if ($firstDayOfTheWeek == 'sunday')
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
                catch (\Exception $exception)
                {
                }
            }
        }
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
     *
     * @return integer[]
     */
    public function getCellMapping()
    {
        return $this->cellMapping;
    }

    /**
     *
     * @param integer[] $cellMapping
     */
    public function setCellMapping($cellMapping)
    {
        $this->cellMapping = $cellMapping;
    }

    /**
     *
     * @param integer $key
     * @param integer[] $value
     */
    public function setCellMappingForKey($key, $value)
    {
        $this->cellMapping[$key] = $value;
    }
}
