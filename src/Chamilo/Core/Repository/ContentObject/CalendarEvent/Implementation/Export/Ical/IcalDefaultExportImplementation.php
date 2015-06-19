<?php
namespace Chamilo\Core\Repository\ContentObject\CalendarEvent\Implementation\Export\Ical;

use Chamilo\Core\Repository\ContentObject\Task\Implementation\Export\IcalExportImplementation;

class IcalDefaultExportImplementation extends IcalExportImplementation
{

    public function render()
    {
        $calendar = $this->get_context()->get_calendar();
        
        $content_object = $this->get_content_object();
        
        $event = $calendar->add('VEVENT');
        
        $event->add('DTSTART', new \DateTime('@' . $content_object->get_start_date()));
        $event->add('DTEND', new \DateTime('@' . $content_object->get_end_date()));
        
        $description = trim(preg_replace('/\s\s+/', '\\n', strip_tags($content_object->get_description())));
        
        $event->add('SUMMARY', trim($content_object->get_title()));
        $event->add('DESCRIPTION', $description);
        
        $event->add('ORGANIZER', 'mailto:' . $content_object->get_owner()->get_email());
        $event->add('ATTENDEE', 'mailto:' . $content_object->get_owner()->get_email());
        
        $event->add('CREATED', new \DateTime('@' . $content_object->get_creation_date()));
        $event->add('LAST-MOD', new \DateTime('@' . $content_object->get_modification_date()));
        
        if ($content_object->has_frequency())
        {
            $event->add('RRULE', $this->get_rrule());
        }
    }
}
