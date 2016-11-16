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
        parent::__construct($displayTime, null, array('table-calendar-mini'));
    }

    /**
     *
     * @param unknown $urlFormat
     */
    public function addNavigationLinks($urlFormat)
    {
        $day = $this->getStartTime();
        $row = 0;
        $maxRows = $this->getRowCount();
        
        while ($row < $maxRows)
        {
            for ($col = 0; $col < 7; $col ++)
            {
                $url = str_replace(self::TIME_PLACEHOLDER, $day, $urlFormat);
                $content = $this->getCellContents($row, $col);
                $content = '<a href="' . $url . '">' . $content . '</a>';
                $this->setCellContents($row, $col, $content);
                $day = strtotime('+24 Hours', $day);
            }
            
            $row ++;
        }
    }

    /**
     *
     * @see \Chamilo\Libraries\Calendar\Table\Type\MonthCalendar::addEvents()
     */
    public function addEvents()
    {
        $events = $this->getEventsToShow();
        $cellMapping = $this->getCellMapping();
        
        foreach ($events as $time => $items)
        {
            $cellMappingKey = date('Ymd', $time);
            
            $row = $cellMapping[$cellMappingKey][0];
            $column = $cellMapping[$cellMappingKey][1];
            
            if (is_null($row) || is_null($column))
            {
                continue;
            }
            
            $tooltip = htmlentities(implode("\n", $items));
            
            if (date('Ymd', $time) != date('Ymd'))
            {
                try
                {
                    $this->setCellContents(
                        $row, 
                        $column, 
                        '<span class="badge" data-toggle="tooltip" data-placement="top" data-content="' . $tooltip . '">' .
                             $this->getCellContents($row, $column) . '</span>');
                }
                catch (\Exception $exception)
                {
                }
            }
        }
    }

    /**
     *
     * @see \Chamilo\Libraries\Calendar\Table\Type\MonthCalendar::render()
     */
    public function render()
    {
        $this->addEvents();
        
        return $this->toHtml();
    }

    /**
     *
     * @param integer $tableDate
     *
     * @return string
     */
    protected function determineCellContent($tableDate)
    {
        $cellContent = parent::determineCellContent($tableDate);
        
        // Is current table date today?
        if (date('Ymd', $tableDate) == date('Ymd'))
        {
            $cellContent = '<span class="badge">' . $cellContent . '</span>';
        }
        
        return $cellContent;
    }
}
