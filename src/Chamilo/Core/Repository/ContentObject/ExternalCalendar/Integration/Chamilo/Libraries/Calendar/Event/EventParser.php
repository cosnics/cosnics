<?php
namespace Chamilo\Core\Repository\ContentObject\ExternalCalendar\Integration\Chamilo\Libraries\Calendar\Event;

/**
 * Parser to covert ExternalCalendar-instances to renderable calender events
 * 
 * @package core\repository\content_object\external_calendar\integration\libraries\calendar\event
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class EventParser extends \Chamilo\Core\Repository\Integration\Chamilo\Libraries\Calendar\Event\EventParser
{

    public function get_events()
    {
        $events = array();
        $from_date = $this->get_start_date();
        $to_date = $this->get_end_date();
        $object = $this->get_content_object();
        
        $calendar_events = $object->get_occurences($from_date, $to_date);
        
        foreach ($calendar_events as $calendar_event)
        {
            $event = $this->get_event_instance();
            $event->set_start_date($calendar_event->DTSTART->getDateTime()->getTimeStamp());
            $event->set_end_date($calendar_event->DTEND->getDateTime()->getTimeStamp());
            
            if (! is_null($calendar_event->SUMMARY))
            {
                $event->set_title($calendar_event->SUMMARY->getValue());
            }
            
            if (! is_null($calendar_event->DESCRIPTION))
            {
                $event->set_content($calendar_event->DESCRIPTION->getValue());
            }
            
            $event->set_source($object->get_title());
            $event->set_content_object($object);
            $events[] = $event;
        }
        
        return $events;
    }
}
