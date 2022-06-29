<?php
namespace Chamilo\Libraries\Calendar\Table\Type;

/**
 *
 * @package Chamilo\Libraries\Calendar\Table\Type
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class MiniDayCalendar extends DayCalendar
{

    public function __construct(
        int $displayTime, int $hourStep = 1, int $startHour = 0, int $endHour = 24, bool $hideOtherHours = false
    )
    {
        parent::__construct(
            $displayTime, $hourStep, $startHour, $endHour, $hideOtherHours, ['table-calendar-mini']
        );
    }
}
