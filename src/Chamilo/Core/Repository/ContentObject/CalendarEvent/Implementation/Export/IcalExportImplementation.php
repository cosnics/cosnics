<?php
namespace Chamilo\Core\Repository\ContentObject\CalendarEvent\Implementation\Export;

use Chamilo\Core\Repository\ContentObject\CalendarEvent\Implementation\ExportImplementation;
use Chamilo\Core\Repository\ContentObject\CalendarEvent\Storage\DataClass\CalendarEvent;

class IcalExportImplementation extends ExportImplementation
{

    public static function rrule(CalendarEvent $content_object)
    {
        $rrule = array();

        $frequency = $content_object->get_frequency();
        switch ($frequency)
        {
            case CalendarEvent :: FREQUENCY_DAILY :
                $rrule['FREQ'] = 'DAILY';
                break;
            case CalendarEvent :: FREQUENCY_WEEKLY :
                $rrule['FREQ'] = 'WEEKLY';

                break;
            case CalendarEvent :: FREQUENCY_MONTHLY :
                $rrule['FREQ'] = 'MONTHLY';
                break;
            case CalendarEvent :: FREQUENCY_YEARLY :
                $rrule['FREQ'] = 'YEARLY';
                break;
            case CalendarEvent :: FREQUENCY_BIWEEKLY :
                $rrule['FREQ'] = 'WEEKLY';
                $rrule['INTERVAL'] = '2';
                break;
            case CalendarEvent :: FREQUENCY_WEEKDAYS :
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
            $rrule['UNTIL'] = self :: get_date_in_ical_format($content_object->get_until());
        }

        if ($content_object->get_frequency_count() > 0)
        {
            $rrule['COUNT'] = $content_object->get_frequency_count();
        }

        if ($content_object->get_frequency_interval() > 0)
        {
            $rrule['INTERVAL'] = $content_object->get_frequency_interval();
        }

        if ($content_object->get_byday())
        {
            $rrule['BYDAY'] = CalendarEvent :: get_byday_parts($content_object->get_byday());
        }

        if ($content_object->get_bymonthday())
        {
            $rrule['BYMONTHDAY'] = $content_object->get_bymonthday();
        }

        if ($content_object->get_bymonth())
        {
            $rrule['BYMONTH'] = $content_object->get_bymonth();
        }

        return $rrule;
    }

    public function get_rrule()
    {
        return self :: rrule($this->get_content_object());
    }

    public static function get_date_in_ical_format($date)
    {
        $y = date('Y', $date);
        $m = date('m', $date);
        $d = date('d', $date);
        $h = date('H', $date);
        $M = date('i', $date);
        $s = date('s', $date);

        return $y . $m . $d . 'T' . $h . $M . $s;
    }
}
