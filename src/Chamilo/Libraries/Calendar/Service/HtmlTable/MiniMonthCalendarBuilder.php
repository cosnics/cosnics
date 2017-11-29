<?php
namespace Chamilo\Libraries\Calendar\Service\HtmlTable;

use Chamilo\Libraries\Calendar\HtmlTable\MiniMonthCalendar;

/**
 *
 * @package Chamilo\Libraries\Calendar\Service\HtmlTable
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class MiniMonthCalendarBuilder extends MonthCalendarBuilder
{

    /**
     *
     * @param integer $displayTime
     * @param string[] $classes
     * @return \Chamilo\Libraries\Calendar\HtmlTable\MiniMonthCalendar
     */
    protected function getCalendar($displayTime, $classes = [])
    {
        return new MiniMonthCalendar($this->getCalendarConfiguration(), $displayTime, $classes);
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

