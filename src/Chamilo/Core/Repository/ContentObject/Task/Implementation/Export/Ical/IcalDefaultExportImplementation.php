<?php
namespace Chamilo\Core\Repository\ContentObject\Task\Implementation\Export\Ical;

use Chamilo\Core\Repository\ContentObject\Task\Implementation\Export\IcalExportImplementation;
use Chamilo\Core\Repository\ContentObject\Task\Storage\DataClass\Task;
use Chamilo\Libraries\File\Path;
use Vtodo;

require_once Path :: getInstance()->getPluginPath() . 'icalcreator/iCalcreator.class.php';
class IcalDefaultExportImplementation extends IcalExportImplementation
{

    public function render()
    {
        $calendar = $this->get_context()->get_calendar();
        
        $content_object = $this->get_content_object();
        
        $event = $calendar->add('VEVENT');
        
        $event->add('DTSTART', new \DateTime('@' . $content_object->get_start_date()));
        $event->add('DUE', new \DateTime('@' . $content_object->get_end_date()));
        
        $description = trim(preg_replace('/\s\s+/', '\\n', strip_tags($content_object->get_description())));
        
        $event->add('SUMMARY', trim($content_object->get_title()));
        $event->add('DESCRIPTION', $description);
        $event->add('PRIORITY', $content_object->get_priority());
        $event->add('CATEGORIES', $content_object->get_task_type_as_string());
        
        $event->add('ORGANIZER', 'mailto:' . $content_object->get_owner()->get_email());
        
        $event->add('CREATED', new \DateTime('@' . $content_object->get_creation_date()));
        $event->add('LAST-MOD', new \DateTime('@' . $content_object->get_modification_date()));
        
        if ($content_object->has_repeat_type())
        {
            $event->add('RRULE', $this->get_rrule());
        }
    }

    public function get_rrule()
    {
        $rrule = array();
        $content_object = $this->get_content_object();
        $frequency = $content_object->get_frequency();
        
        switch ($frequency)
        {
            case Task :: FREQ_DAILY :
                $rrule['FREQ'] = 'DAILY';
                break;
            case Task :: FREQ_WEEKLY :
                $rrule['FREQ'] = 'WEEKLY';
                
                break;
            case Task :: FREQ_MONTHLY :
                $rrule['FREQ'] = 'MONTHLY';
                break;
            case Task :: FREQ_YEARLY :
                $rrule['FREQ'] = 'YEARLY';
                break;
            case Task :: FREQ_BIWEEK :
                $rrule['FREQ'] = 'WEEKLY';
                $rrule['INTERVAL'] = '2';
                break;
            case Task :: FREQ_WEEK_DAYS :
                $rrule['FREQ'] = 'DAILY';
                $rrule['BYDAY'] = array(
                    array('DAY' => 'MO'), 
                    array('DAY' => 'TU'), 
                    array('DAY' => 'WE'), 
                    array('DAY' => 'TH'), 
                    array('DAY' => 'FR'));
                break;
        }
        
        if (! $content_object->frequency_is_indefinately())
        {
            $rrule['UNTIL'] = $this->get_date_in_ical_format($content_object->get_until());
        }
        
        return $rrule;
    }

    public function get_date_in_ical_format($date)
    {
        $year = date('Y', $date);
        $month = date('m', $date);
        $day = date('d', $date);
        $hour = date('H', $date);
        $Minute = date('i', $date);
        $second = date('s', $date);
        
        return $year . $month . $day . 'T' . $hour . $Minute . $second;
    }
}
