<?php
namespace Chamilo\Application\Calendar\Extension\Google\Architecture\Domain;

use Google_Service_Calendar_Event;

/**
 * @package Chamilo\Application\Calendar\Extension\Google\Architecture\Domain
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class Event extends \Chamilo\Libraries\Calendar\Event\Event
{

    private CalendarProperties $calendarProperties;

    private Google_Service_Calendar_Event $googleCalendarEvent;

    public function getCalendarProperties(): CalendarProperties
    {
        return $this->calendarProperties;
    }

    public function setCalendarProperties(CalendarProperties $calendarProperties): static
    {
        $this->calendarProperties = $calendarProperties;

        return $this;
    }

    public function getGoogleCalendarEvent(): Google_Service_Calendar_Event
    {
        return $this->googleCalendarEvent;
    }

    public function setGoogleCalendarEvent(Google_Service_Calendar_Event $googleCalendarEvent): static
    {
        $this->googleCalendarEvent = $googleCalendarEvent;

        return $this;
    }
}
