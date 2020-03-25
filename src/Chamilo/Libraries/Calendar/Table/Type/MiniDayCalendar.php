<?php
namespace Chamilo\Libraries\Calendar\Table\Type;

/**
 *
 * @package Chamilo\Libraries\Calendar\Table\Type
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class MiniDayCalendar extends DayCalendar
{

    /**
     *
     * @param integer $displayTime
     * @param integer $hourStep
     * @param integer $startHour
     * @param integer $endHour
     * @param boolean $hideOtherHours
     */
    public function __construct($displayTime, $hourStep = 1, $startHour = 0, $endHour = 24, $hideOtherHours = false)
    {
        parent::__construct(
            $displayTime, $hourStep, $startHour, $endHour, $hideOtherHours, array('table-calendar-mini')
        );
    }
}
