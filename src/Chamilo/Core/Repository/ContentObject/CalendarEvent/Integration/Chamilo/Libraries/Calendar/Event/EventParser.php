<?php
namespace Chamilo\Core\Repository\ContentObject\CalendarEvent\Integration\Chamilo\Libraries\Calendar\Event;

use Chamilo\Libraries\Platform\Translation;

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

    public function get_events()
    {
        $from_date = $this->get_start_date();
        $to_date = $this->get_end_date();
        $object = $this->get_content_object();

        $event = $this->get_event_instance();
        $event->set_start_date($object->get_start_date());
        $event->set_end_date($object->get_end_date());
        $event->set_title($object->get_title());
        $event->set_content($object->get_description());
        $event->set_source(Translation :: get('TypeName', null, $object->context()));
        $event->set_content_object($object);

        $recurrenceRulesParser = new RecurrenceRulesParser($object);
        $event->setRecurrenceRules($recurrenceRulesParser->getRules());

        return array($event);
    }
}
