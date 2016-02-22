<?php
namespace Chamilo\Application\Calendar\Extension\Google\Integration\Chamilo\Libraries\Calendar\Event;

use Chamilo\Application\Calendar\Extension\Google\CalendarProperties;

/**
 *
 * @package Chamilo\Application\Calendar\Extension\Google\Integration\Chamilo\Libraries\Calendar\Event
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class Event extends \Chamilo\Libraries\Calendar\Event\Event
{

    /**
     *
     * @var \Chamilo\Application\Calendar\Extension\Google\CalendarProperties
     */
    private $calendarProperties;

    /**
     *
     * @var \Google_Service_Calendar_Event
     */
    private $googleCalendarEvent;

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
     * @return Google_Service_Calendar_Event
     */
    public function getGoogleCalendarEvent()
    {
        return $this->googleCalendarEvent;
    }

    /**
     *
     * @param \Google_Service_Calendar_Event $googleCalendarEvent
     */
    public function setGoogleCalendarEvent(\Google_Service_Calendar_Event $googleCalendarEvent)
    {
        $this->googleCalendarEvent = $googleCalendarEvent;
    }
}
