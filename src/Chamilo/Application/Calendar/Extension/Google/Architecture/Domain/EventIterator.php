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
    private CalendarProperties $calendarProperties;

    /**
     * @param \Google_Service_Calendar_Event[] $googleCalendarEvents
     */
    public function __construct(CalendarProperties $calendarProperties, array $googleCalendarEvents)
    {
        parent::__construct($googleCalendarEvents);
        $this->calendarProperties = $calendarProperties;
    }

    public function getCalendarProperties(): CalendarProperties
    {
        return $this->calendarProperties;
    }

    public function setCalendarProperties(CalendarProperties $calendarProperties): static
    {
        $this->calendarProperties = $calendarProperties;

        return $this;
    }
}