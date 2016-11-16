<?php
namespace Chamilo\Core\Repository\ContentObject\Task\Common\Import;

use Chamilo\Core\Repository\ContentObject\Task\Common\ImportImplementation;
use Chamilo\Core\Repository\ContentObject\Task\Storage\DataClass\Task;
use Sabre\VObject\DateTimeParser;
use Sabre\VObject\Property\FlatText;
use Sabre\VObject\Property\ICalendar\Recur;
use Sabre\VObject\Property\Text;

class IcalImportImplementation extends ImportImplementation
{

    public function import()
    {
        $content_object = new Task();
        
        // General properties
        $content_object->set_owner_id($this->get_controller()->get_parameters()->get_user());
        $content_object->set_parent_id($this->get_controller()->determine_parent_id());
        
        // Task properties as retrieved from the iCal VToDo
        $component = $this->get_content_object_import_parameters()->get_calendar_component();
        
        $content_object->set_title($component->summary->getValue());
        
        if ($component->description instanceof FlatText)
        {
            $content_object->set_description($component->description->getValue());
        }
        else
        {
            $content_object->set_description('-');
        }
        
        $content_object->set_start_date($component->dtstart->getDateTime()->getTimestamp());
        $content_object->set_due_date($component->dtstamp->getDateTime()->getTimestamp());
        
        if ($component->categories instanceof Text)
        {
            $content_object->set_category($component->categories->getValue());
        }
        
        if (isset($component->priority['value']))
        {
            $content_object->set_priority($component->priority['value']);
        }
        
        if ($component->rrule instanceof Recur)
        {
            $recurrence = $component->rrule->getParts();
            
            switch ($recurrence['FREQ'])
            {
                case 'MONTHLY' :
                    $content_object->set_frequency(Task::FREQUENCY_MONTHLY);
                    break;
                case 'YEARLY' :
                    $content_object->set_frequency(Task::FREQUENCY_YEARLY);
                    break;
                case 'WEEKLY' :
                    $content_object->set_frequency(Task::FREQUENCY_BIWEEKLY);
                    
                    if ($recurrence['INTERVAL'] == '2')
                    {
                        $content_object->set_frequency(Task::FREQUENCY_BIWEEKLY);
                    }
                    else
                    {
                        $content_object->set_frequency(Task::FREQUENCY_WEEKLY);
                    }
                    
                    break;
                case 'DAILY' :
                    $weekdays = array('MO', 'TU', 'WE', 'TH', 'FR');
                    
                    if ($recurrence['BYDAY'] == $weekdays)
                    {
                        $content_object->set_frequency(Task::FREQUENCY_WEEKDAYS);
                    }
                    else
                    {
                        $content_object->set_frequency(Task::FREQUENCY_DAILY);
                    }
                    break;
            }
            
            if ($recurrence['UNTIL'])
            {
                $content_object->set_until(DateTimeParser::parseDateTime($recurrence['UNTIL'])->getTimestamp());
            }
        }
        else
        {
            $content_object->set_frequency(Task::FREQUENCY_NONE);
        }
        
        return $content_object;
    }
}
