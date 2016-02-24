<?php
namespace Chamilo\Libraries\Calendar\Table\Type;

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
        parent :: __construct($displayTime, array('table-calendar-mini'));
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

    public function addEvents()
    {
        $events = $this->getEventsToShow();
        $cellMapping = $this->getCellMapping();

        foreach ($events as $time => $items)
        {
            $cellMappingKey = date('Ymd', $time);

            $row = $cellMapping[$cellMappingKey][0];
            $column = $cellMapping[$cellMappingKey][1];

            $attributes = $this->getCellAttributes($row, $column);
            $classes = isset($attributes['class']) ? array($attributes['class']) : array();
            $classes[] = 'table-calendar-contains-events';

            $this->updateCellAttributes($row, $column, 'class="' . implode(' ', $classes) . '"');

            // foreach ($items as $index => $item)
            // {
            // $cellContent = $this->getCellContents($row, $column);
            // $cellContent .= $item;
            // $this->setCellContents($row, $column, $cellContent);
            // }
        }
    }

    public function render()
    {
        $this->addEvents();
        return $this->toHtml();
    }
}
