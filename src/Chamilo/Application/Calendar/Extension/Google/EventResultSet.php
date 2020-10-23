<?php
namespace Chamilo\Application\Calendar\Extension\Google;

use ArrayIterator;

/**
 *
 * @package Chamilo\Application\Calendar\Extension\Google
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class EventResultSet extends ArrayIterator
{

    /**
     *
     * @var \Chamilo\Application\Calendar\Extension\Google\CalendarProperties
     */
    private $calendarProperties;

    /**
     *
     * @param \Chamilo\Application\Calendar\Extension\Google\CalendarProperties $calendarProperties
     * @param \Google_Service_Calendar_Event[] $googleCalendarEvents
     */
    public function __construct(CalendarProperties $calendarProperties, array $googleCalendarEvents)
    {
        parent::__construct($googleCalendarEvents);
        $this->calendarProperties = $calendarProperties;
    }

    /**
     *
     * @return \Chamilo\Application\Calendar\Extension\Google\CalendarProperties
     */
    public function getCalendarProperties()
    {
        return $this->calendarProperties;
    }

    /**
     *
     * @param \Chamilo\Application\Calendar\Extension\Google\CalendarProperties $calendarProperties
     */
    public function setCalendarProperties(CalendarProperties $calendarProperties)
    {
        $this->calendarProperties = $calendarProperties;
    }
}