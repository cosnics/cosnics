<?php
namespace Chamilo\Core\Repository\ContentObject\CalendarEvent\Common\Import;

use Chamilo\Core\Repository\ContentObject\CalendarEvent\Common\ImportImplementation;
use Chamilo\Core\Repository\ContentObject\CalendarEvent\Storage\DataClass\CalendarEvent;
use Sabre\VObject\DateTimeParser;
use Sabre\VObject\Property\FlatText;
use Sabre\VObject\Property\ICalendar\Recur;

class IcalImportImplementation extends ImportImplementation
{

    public function import()
    {
        $content_object = new CalendarEvent();
        
        // General properties
        $content_object->set_owner_id($this->get_controller()->get_parameters()->get_user());
        $content_object->set_parent_id($this->get_controller()->determine_parent_id());
        
        // Calendar event properties as retrieved from the iCal VEvent
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
        $content_object->set_end_date($component->dtend->getDateTime()->getTimestamp());
        
        if ($component->rrule instanceof Recur)
        {
            $recurrence = $component->rrule->getParts();
            
            switch ($recurrence['FREQ'])
            {
                case 'MONTHLY' :
                    $content_object->set_frequency(CalendarEvent::FREQUENCY_MONTHLY);
                    break;
                case 'YEARLY' :
                    $content_object->set_frequency(CalendarEvent::FREQUENCY_YEARLY);
                    break;
                case 'WEEKLY' :
                    $content_object->set_frequency(CalendarEvent::FREQUENCY_BIWEEKLY);
                    
                    if ($recurrence['INTERVAL'] == '2')
                    {
                        $content_object->set_frequency(CalendarEvent::FREQUENCY_BIWEEKLY);
                    }
                    else
                    {
                        $content_object->set_frequency(CalendarEvent::FREQUENCY_WEEKLY);
                    }
                    
                    break;
                case 'DAILY' :
                    $weekdays = array('MO', 'TU', 'WE', 'TH', 'FR');
                    
                    if ($recurrence['BYDAY'] == $weekdays)
                    {
                        $content_object->set_frequency(CalendarEvent::FREQUENCY_WEEKDAYS);
                    }
                    else
                    {
                        $content_object->set_frequency(CalendarEvent::FREQUENCY_DAILY);
                    }
                    break;
            }
            
            if ($recurrence['UNTIL'])
            {
                $content_object->set_until(DateTimeParser::parseDateTime($recurrence['UNTIL'])->getTimestamp());
            }
            
            if ($recurrence['COUNT'])
            {
                $content_object->set_frequency_count($recurrence['COUNT']);
            }
            
            if ($recurrence['INTERVAL'])
            {
                $content_object->set_frequency_count($recurrence['INTERVAL']);
            }
            
            if ($recurrence['BYMONTHDAY'])
            {
                if (is_array($recurrence['BYMONTHDAY']))
                {
                    $by_month_day = implode(',', $recurrence['BYMONTHDAY']);
                }
                else
                {
                    $by_month_day = $recurrence['BYMONTHDAY'];
                }
                
                $content_object->set_bymonthday($by_month_day);
            }
            
            if ($recurrence['BYMONTH'])
            {
                if (is_array($recurrence['BYMONTH']))
                {
                    $by_month = implode(',', $recurrence['BYMONTH']);
                }
                else
                {
                    $by_month = $recurrence['BYMONTH'];
                }
                
                $content_object->set_bymonth($by_month);
            }
            
            if ($recurrence['BYDAY'] && $content_object->get_frequency() != CalendarEvent::FREQUENCY_WEEKDAYS)
            {
                if (is_array($recurrence['BYDAY']))
                {
                    $by_day = implode(',', $recurrence['BYDAY']);
                }
                else
                {
                    $by_day = $recurrence['BYDAY'];
                }
                
                $content_object->set_byday($by_day);
            }
        }
        else
        {
            $content_object->set_frequency(CalendarEvent::FREQUENCY_NONE);
        }
        
        return $content_object;
    }
}
