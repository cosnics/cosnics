<?php
namespace Chamilo\Core\Repository\ContentObject\CalendarEvent\Common\Export\Ical;

use Chamilo\Core\Repository\ContentObject\CalendarEvent\Common\Export\IcalExportImplementation;
use Chamilo\Core\Repository\ContentObject\CalendarEvent\Integration\Chamilo\Libraries\Calendar\Event\RecurrenceRulesParser;
use Chamilo\Libraries\Calendar\Event\RecurrenceRules\VObjectRecurrenceRulesFormatter;
use DateTime;
use DateTimeZone;

class IcalDefaultExportImplementation extends IcalExportImplementation
{

    public function render()
    {
        $calendar = $this->get_context()->get_calendar();
        
        $content_object = $this->get_content_object();
        
        $event = $calendar->add('VEVENT');
        
        $event->add(
            'DTSTART', 
            new DateTime(
                date('Y-m-d\TH:i:s', $content_object->get_start_date()), 
                new DateTimeZone(date_default_timezone_get())));
        
        $event->add(
            'DTEND', 
            new DateTime(
                date('Y-m-d\TH:i:s', $content_object->get_end_date()), 
                new DateTimeZone(date_default_timezone_get())));
        
        $description = trim(preg_replace('/\s\s+/', '\\n', strip_tags($content_object->get_description())));
        
        $event->add('SUMMARY', trim($content_object->get_title()));
        $event->add('DESCRIPTION', $description);
        
        $event->add('ORGANIZER', 'mailto:' . $content_object->get_owner()->get_email());
        $event->add('ATTENDEE', 'mailto:' . $content_object->get_owner()->get_email());
        
        $event->add(
            'CREATED', 
            new DateTime(
                date('Y-m-d\TH:i:s', $content_object->get_creation_date()), 
                new DateTimeZone(date_default_timezone_get())));
        
        $event->add(
            'LAST-MODIFIED', 
            new DateTime(
                date('Y-m-d\TH:i:s', $content_object->get_modification_date()), 
                new DateTimeZone(date_default_timezone_get())));
        
        $event->add(
            'DTSTAMP', 
            new DateTime(
                date('Y-m-d\TH:i:s', $content_object->get_modification_date()), 
                new DateTimeZone(date_default_timezone_get())));
        
        $event->add('UID', uniqid());
        
        $recurrenceRulesParser = new RecurrenceRulesParser($content_object);
        $vObjectRecurrenceRulesFormatter = new VObjectRecurrenceRulesFormatter();
        
        $event->add('RRULE', $vObjectRecurrenceRulesFormatter->format(($recurrenceRulesParser->getRules())));
    }
}
