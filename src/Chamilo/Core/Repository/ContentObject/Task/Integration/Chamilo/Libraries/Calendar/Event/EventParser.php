<?php
namespace Chamilo\Core\Repository\ContentObject\Task\Integration\Chamilo\Libraries\Calendar\Event;

use Chamilo\Libraries\Platform\Translation;

/**
 *
 * @package core\repository\content_object\task\integration\libraries\calendar\event
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
        
        if ($object->repeats())
        {
            $repeats = $object->get_repeats($from_date, $to_date);
            
            foreach ($repeats as $repeat)
            {
                $event = $this->get_event_instance();
                
                $event->set_start_date($repeat->DTSTART->getDateTime()->getTimeStamp());
                $event->set_end_date($repeat->DTEND->getDateTime()->getTimeStamp());
                
                if (! is_null($repeat->SUMMARY))
                {
                    $event->set_title($repeat->SUMMARY->getValue());
                }
                
                if (! is_null($repeat->DESCRIPTION))
                {
                    $event->set_content($repeat->DESCRIPTION->getValue());
                }
                
                $event->set_source(Translation :: get('TypeName', null, $object->context()));
                $event->set_content_object($object);
                $events[] = $event;
            }
        }
        elseif ($object->get_start_date() >= $from_date && $object->get_start_date() <= $to_date)
        {
            $event = $this->get_event_instance();
            $event->set_start_date($object->get_start_date());
            $event->set_end_date($object->get_end_date());
            $event->set_title($object->get_title());
            $event->set_content($object->get_description());
            $event->set_source(Translation :: get('TypeName', null, $object->context()));
            $event->set_content_object($object);
            $events[] = $event;
        }
        
        return $events;
    }
}
