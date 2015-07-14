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
     * @param \Chamilo\Libraries\Calendar\Renderer\Renderer $renderer
     * @param integer $fromDate
     * @param integer $toDate
     * @return \Chamilo\Libraries\Calendar\Event\Event[]
     */
    public function getEvents(\Chamilo\Libraries\Calendar\Renderer\Renderer $renderer, $fromDate, $toDate);
}