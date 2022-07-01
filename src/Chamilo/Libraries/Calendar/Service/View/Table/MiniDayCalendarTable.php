<?php
namespace Chamilo\Libraries\Calendar\Service\View\Table;

/**
 *
 * @package Chamilo\Libraries\Calendar\Table\Type
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class MiniDayCalendarTable extends DayCalendarTable
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
