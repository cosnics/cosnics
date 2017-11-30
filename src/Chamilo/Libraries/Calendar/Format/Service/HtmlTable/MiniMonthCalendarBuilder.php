<?php
namespace Chamilo\Libraries\Calendar\Format\Service\HtmlTable;

use Chamilo\Libraries\Calendar\Format\HtmlTable\MiniMonthCalendar;

/**
 *
 * @package Chamilo\Libraries\Calendar\Format\Service\HtmlTable
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class MiniMonthCalendarBuilder extends MonthCalendarBuilder
{

    /**
     *
     * @param integer $displayTime
     * @param string[] $classes
     * @return \Chamilo\Libraries\Calendar\Format\HtmlTable\MiniMonthCalendar
     */
    protected function getCalendar($displayTime)
    {
        return new MiniMonthCalendar($this->getCalendarConfiguration(), $displayTime);
    }

    /**
     *
     * @param integer $tableDate
     * @return string
     */
    protected function determineCellContent($tableDate)
    {
        $cellContent = date('j', $tableDate);
        
        // Is current table date today?
        if (date('Ymd', $tableDate) == date('Ymd'))
        {
            $cellContent = '<span class="badge">' . $cellContent . '</span>';
        }
        
        return $cellContent;
    }
}

