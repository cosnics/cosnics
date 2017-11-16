<?php
namespace Chamilo\Core\Repository\ContentObject\CalendarEvent\Integration\Chamilo\Libraries\Calendar\Event;

use Chamilo\Libraries\Translation\Translation;

/**
 * Parser to covert CalendarEvent-instances to renderable calender events
 * 
 * @package Chamilo\Core\Repository\ContentObject\CalendarEvent\Integration\Chamilo\Libraries\Calendar\Event
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class EventParser extends \Chamilo\Core\Repository\Integration\Chamilo\Libraries\Calendar\Event\EventParser
{

    public function getEvents()
    {
        $object = $this->getContentObject();
        
        $event = $this->getEventInstance();
        $event->setStartDate($object->get_start_date());
        $event->setEndDate($object->get_end_date());
        $event->setTitle($object->get_title());
        $event->setContent($object->get_description());
        $event->setLocation($object->get_location());
        $event->setSource(Translation::get('TypeName', null, $object->context()));
        $event->setContentObject($object);
        
        $recurrenceRulesParser = new RecurrenceRulesParser($object);
        $event->setRecurrenceRules($recurrenceRulesParser->getRules());
        
        return array($event);
    }
}
