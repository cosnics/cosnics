<?php
namespace Chamilo\Libraries\Calendar\Service\Table;

use Chamilo\Libraries\Calendar\Table\Type\MiniMonthCalendar;

/**
 *
 * @package Chamilo\Libraries\Calendar\Service\Table
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class MiniMonthCalendarBuilder extends MonthCalendarBuilder
{

    /**
     *
     * @param integer $displayTime
     * @param string[] $classes
     * @return \Chamilo\Libraries\Calendar\Table\Type\MiniMonthCalendar
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

