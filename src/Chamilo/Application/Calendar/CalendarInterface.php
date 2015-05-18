<?php
namespace Chamilo\Application\Calendar;

/**
 *
 * @package application\personal_calendar
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
interface CalendarInterface
{

    /**
     * Gets the events published in the implementing context
     * 
     * @param Renderer $renderer
     * @param int $from_date
     * @param int $to_date
     * @return \application\personal_calendar\Event[]
     */
    public function get_events(\Chamilo\Libraries\Calendar\Renderer\Renderer $renderer, $from_date, $to_date);
}