<?php
namespace Chamilo\Application\Calendar\Extension\Google\Architecture\Domain;

use ArrayIterator;

/**
 * @package Chamilo\Application\Calendar\Extension\Google\Architecture\Domain
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class EventIterator extends ArrayIterator
{
    /**
     * @param \Google_Service_Calendar_Event[] $googleCalendarEvents
     */
    public function __construct(public CalendarProperties $calendarProperties, array $googleCalendarEvents)
    {
        parent::__construct($googleCalendarEvents);
    }
}