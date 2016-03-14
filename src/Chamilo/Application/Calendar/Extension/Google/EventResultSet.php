<?php
namespace Chamilo\Application\Calendar\Extension\Google;

use Chamilo\Libraries\Storage\ResultSet\ArrayResultSet;

/**
 *
 * @package Chamilo\Application\Calendar\Extension\Google
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class EventResultSet extends ArrayResultSet
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
        $this->calendarProperties = $calendarProperties;

        parent :: __construct($googleCalendarEvents);
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

    /**
     *
     * @param boolean $mapToObject
     * @return \Google_Service_Calendar_Event
     */
    public function next_result($mapToObject = false)
    {
        return parent :: next_result($mapToObject);
    }
}