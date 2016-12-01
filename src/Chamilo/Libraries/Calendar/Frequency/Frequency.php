<?php
namespace Chamilo\Libraries\Calendar\Frequency;

use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Utilities\Utilities;
use Sabre\VObject;

trait Frequency
{
    
    // Properties
    const PROPERTY_FREQUENCY = 'frequency';
    const PROPERTY_FREQUENCY_UNTIL = 'frequency_until';
    const PROPERTY_FREQUENCY_COUNT = 'frequency_count';
    const PROPERTY_FREQUENCY_INTERVAL = 'frequency_interval';
    const PROPERTY_FREQUENCY_BYDAY = 'frequency_byday';
    const PROPERTY_FREQUENCY_BYMONTHDAY = 'frequency_bymonthday';
    const PROPERTY_FREQUENCY_BYMONTH = 'frequency_bymonth';
    
    // Frequency options
    const FREQUENCY_NONE = '0';
    const FREQUENCY_DAILY = '1';
    const FREQUENCY_WEEKLY = '2';
    const FREQUENCY_WEEKDAYS = '3';
    const FREQUENCY_BIWEEK = '4';
    const FREQUENCY_MONTHLY = '5';
    const FREQUENCY_YEARLY = '6';

    public function get_frequency()
    {
        return $this->get_additional_property(self::PROPERTY_FREQUENCY);
    }

    public function set_frequency($frequency)
    {
        return $this->set_additional_property(self::PROPERTY_FREQUENCY, $frequency);
    }

    public function get_frequency_until()
    {
        return $this->get_additional_property(self::PROPERTY_FREQUENCY_UNTIL);
    }

    public function set_frequency_until($frequency_until)
    {
        return $this->set_additional_property(self::PROPERTY_FREQUENCY_UNTIL, $frequency_until);
    }

    public function get_frequency_count()
    {
        return $this->get_additional_property(self::PROPERTY_FREQUENCY_COUNT);
    }

    public function set_frequency_count($frequency_count)
    {
        return $this->set_additional_property(self::PROPERTY_FREQUENCY_COUNT, $frequency_count);
    }

    public function get_frequency_interval()
    {
        return $this->get_additional_property(self::PROPERTY_FREQUENCY_INTERVAL);
    }

    public function set_frequency_interval($frequency_interval)
    {
        return $this->set_additional_property(self::PROPERTY_FREQUENCY_INTERVAL, $frequency_interval);
    }

    public function get_frequency_byday()
    {
        return $this->get_additional_property(self::PROPERTY_FREQUENCY_BYDAY);
    }

    public function set_frequency_byday($frequency_byday)
    {
        return $this->set_additional_property(self::PROPERTY_FREQUENCY_BYDAY, $frequency_byday);
    }

    public function get_frequency_bymonthday()
    {
        return $this->get_additional_property(self::PROPERTY_FREQUENCY_BYMONTHDAY);
    }

    public function set_frequency_bymonthday($frequency_bymonthday)
    {
        return $this->set_additional_property(self::PROPERTY_FREQUENCY_BYMONTHDAY, $frequency_bymonthday);
    }

    public function get_frequency_bymonth()
    {
        return $this->get_additional_property(self::PROPERTY_FREQUENCY_BYMONTH);
    }

    public function set_frequency_bymonth($frequency_bymonth)
    {
        return $this->set_additional_property(self::PROPERTY_FREQUENCY_BYMONTH, $frequency_bymonth);
    }

    public function has_frequency()
    {
        return $this->get_frequency() != 0;
    }

    public function is_frequency_unending()
    {
        return $this->get_frequency_until() == 0;
    }

    public function get_frequency_as_string()
    {
        return self::frequency_as_string($this->get_frequency());
    }

    public static function frequency_as_string($frequency)
    {
        switch ($frequency)
        {
            case self::FREQ_DAILY :
                $string = Translation::get('Daily');
                break;
            case self::FREQ_WEEKLY :
                $string = Translation::get('Weekly');
                break;
            case self::FREQ_MONTHLY :
                $string = Translation::get('Monthly');
                break;
            case self::FREQ_YEARLY :
                $string = Translation::get('Yearly');
                break;
            case self::FREQ_WEEK_DAYS :
                $string = Translation::get('Weekdays');
                break;
            case self::FREQ_BIWEEK :
                $string = Translation::get('Biweekly');
                break;
        }
        
        return $string;
    }

    public static function get_frequency_options()
    {
        $options = array();
        
        $options[self::FREQ_DAILY] = Translation::get('Daily');
        $options[self::FREQ_WEEKLY] = Translation::get('Weekly');
        $options[self::FREQ_MONTHLY] = Translation::get('Monthly');
        $options[self::FREQ_YEARLY] = Translation::get('Yearly');
        $options[self::FREQ_WEEK_DAYS] = Translation::get('Weekdays');
        $options[self::FREQ_BIWEEK] = Translation::get('BiWeekly');
        
        return $options;
    }

    public static function get_additional_property_names()
    {
        return array(
            self::PROPERTY_START_DATE, 
            self::PROPERTY_END_DATE, 
            self::PROPERTY_UNTIL, 
            self::PROPERTY_FREQUENCY, 
            self::PROPERTY_FREQUENCY_COUNT, 
            self::PROPERTY_FREQUENCY_INTERVAL, 
            self::PROPERTY_BYDAY, 
            self::PROPERTY_BYMONTH, 
            self::PROPERTY_BYMONTHDAY);
    }

    public function get_icon_name($size = Theme :: ICON_SMALL)
    {
        if ($this->has_frequency())
        {
            return $size . '_repeat';
        }
        else
        {
            return $size;
        }
    }

    public static function icon_image($context, $size = Theme :: ICON_SMALL, $is_current = true, $has_frequency = false)
    {
        if ($has_frequency)
        {
            $size = $size . '_repeat';
        }
        
        return parent::icon_image($context, $size, $is_current);
    }

    public function get_type_string()
    {
        if ($this->has_frequency())
        {
            return Translation::get('RepeatingCalendarEvent');
        }
        else
        {
            return parent::get_type_string();
        }
    }

    public static function get_byday_options()
    {
        $translator = Translation::getInstance();
        
        return $result = array(
            1 => $translator->getTranslation("Monday", null, Utilities::COMMON_LIBRARIES), 
            2 => $translator->getTranslation("Tuesday", null, Utilities::COMMON_LIBRARIES), 
            3 => $translator->getTranslation("Wednesday", null, Utilities::COMMON_LIBRARIES), 
            4 => $translator->getTranslation("Thursday", null, Utilities::COMMON_LIBRARIES), 
            5 => $translator->getTranslation("Friday", null, Utilities::COMMON_LIBRARIES), 
            6 => $translator->getTranslation("Saturday", null, Utilities::COMMON_LIBRARIES), 
            7 => $translator->getTranslation("Sunday", null, Utilities::COMMON_LIBRARIES));
    }

    public static function get_bymonthday_options()
    {
        return array(
            1 => 1, 
            2 => 2, 
            3 => 3, 
            4 => 4, 
            5 => 5, 
            6 => 6, 
            7 => 7, 
            8 => 8, 
            9 => 9, 
            10 => 10, 
            11 => 11, 
            12 => 12, 
            13 => 13, 
            14 => 14, 
            15 => 15, 
            16 => 16, 
            17 => 17, 
            18 => 18, 
            19 => 19, 
            20 => 20, 
            21 => 21, 
            22 => 22, 
            23 => 23, 
            24 => 24, 
            25 => 25, 
            26 => 26, 
            27 => 27, 
            28 => 28, 
            29 => 29, 
            30 => 30, 
            31 => 31);
    }

    public static function get_bymonth_options()
    {
        $translator = Translation::getInstance();
        
        return array(
            1 => $translator->getTranslation("January", null, Utilities::COMMON_LIBRARIES), 
            2 => $translator->getTranslation("February", null, Utilities::COMMON_LIBRARIES), 
            3 => $translator->getTranslation("March", null, Utilities::COMMON_LIBRARIES), 
            4 => $translator->getTranslation("April", null, Utilities::COMMON_LIBRARIES), 
            5 => $translator->getTranslation("May", null, Utilities::COMMON_LIBRARIES), 
            6 => $translator->getTranslation("June", null, Utilities::COMMON_LIBRARIES), 
            7 => $translator->getTranslation("Juli", null, Utilities::COMMON_LIBRARIES), 
            8 => $translator->getTranslation("August", null, Utilities::COMMON_LIBRARIES), 
            9 => $translator->getTranslation("September", null, Utilities::COMMON_LIBRARIES), 
            10 => $translator->getTranslation("October", null, Utilities::COMMON_LIBRARIES), 
            11 => $translator->getTranslation("November", null, Utilities::COMMON_LIBRARIES), 
            12 => $translator->getTranslation("December", null, Utilities::COMMON_LIBRARIES));
    }

    public static function get_bymonth_string($month)
    {
        $translation = self::get_bymonth_options();
        return $translation[$month];
    }

    public static function get_byday_ical_format($rank, $day)
    {
        $format = array();
        if ($rank != 0)
        {
            $format[] = $rank;
        }
        
        $format[] = self::get_day_ical_format($day);
        return implode('', $format);
    }

    public static function get_day_ical_format($day)
    {
        return self::$days[$day];
    }

    public static function get_rank_options()
    {
        $ranks = array();
        $ranks[0] = Translation::get('Every');
        $ranks[1] = Translation::get('First');
        $ranks[2] = Translation::get('Second');
        $ranks[3] = Translation::get('Third');
        $ranks[4] = Translation::get('Fourth');
        $ranks[5] = Translation::get('Fifth');
        $ranks[- 1] = Translation::get('Last');
        
        return $ranks;
    }

    public static function get_rank_string($rank)
    {
        $translation = self::get_rank_options();
        return $translation[$rank];
    }

    public static function get_day_format($day)
    {
        $days = array_flip(self::$days);
        return $days[$day];
    }

    public static function get_day_string($day_number)
    {
        if (! is_numeric($day_number))
        {
            $day_number = self::get_day_format($day_number);
        }
        $translation = self::get_byday_options();
        return $translation[$day_number];
    }

    public static function get_byday_parts($bydays)
    {
        $bydays = explode(',', $bydays);
        $parts = array();
        foreach ($bydays as $byday)
        {
            preg_match_all('/(-?[1-5]?)([A-Z]+)/', $byday, $byday_parts);
            $parts[] = array($byday_parts[1] == 0 ? 0 : $byday_parts[1][0], $byday_parts[2][0]);
        }
        
        return $parts;
    }

    public function get_repeats($from_date = 0, $to_date = 0)
    {
        $vcalendar = new VObject\Component\VCalendar();
        
        $start_date_time = new \DateTime();
        $start_date_time->setTimestamp($this->get_start_date());
        
        $end_date_time = new \DateTime();
        $end_date_time->setTimestamp($this->get_end_date());
        
        $vevent = $vcalendar->add('VEVENT');
        
        $vevent->add('SUMMARY', $this->get_title());
        $vevent->add('DESCRIPTION', $this->get_description());
        $vevent->add('DTSTART', $start_date_time);
        $vevent->add('DTEND', $end_date_time);
        
        $rrules = self::rrule($this);
        
        $bydays = array();
        
        foreach ($rrules['BYDAY'] as $byday)
        {
            $bydays[] = implode('', $byday);
        }
        
        $rrules['BYDAY'] = implode(',', $bydays);
        
        $vevent->add('RRULE', $rrules);
        $vevent->add('UID', uniqid());
        
        $from_date_time = new \DateTime();
        $from_date_time->setTimestamp($from_date);
        
        $to_date_time = new \DateTime();
        $to_date_time->setTimestamp($to_date);
        
        $vcalendar->expand($from_date_time, $to_date_time);
        
        return $vcalendar->VEVENT;
    }

    public static function rrule(DataClass $data_class)
    {
        $rrule = array();
        
        $frequency = $data_class->get_frequency();
        switch ($frequency)
        {
            case DataClass::FREQ_DAILY :
                $rrule['FREQ'] = 'DAILY';
                break;
            case DataClass::FREQ_WEEKLY :
                $rrule['FREQ'] = 'WEEKLY';
                
                break;
            case DataClass::FREQ_MONTHLY :
                $rrule['FREQ'] = 'MONTHLY';
                break;
            case DataClass::FREQ_YEARLY :
                $rrule['FREQ'] = 'YEARLY';
                break;
            case DataClass::FREQ_BIWEEK :
                $rrule['FREQ'] = 'WEEKLY';
                $rrule['INTERVAL'] = '2';
                break;
            case DataClass::FREQ_WEEK_DAYS :
                $rrule['FREQ'] = 'DAILY';
                $rrule['BYDAY'] = array(
                    array('DAY' => 'MO'), 
                    array('DAY' => 'TU'), 
                    array('DAY' => 'WE'), 
                    array('DAY' => 'TH'), 
                    array('DAY' => 'FR'));
                break;
        }
        
        if (! $data_class->frequency_is_indefinately())
        {
            // TODO: Use \DateTime here
            $rrule['UNTIL'] = self::get_date_in_ical_format($data_class->get_until());
        }
        
        if ($data_class->get_frequency_count() > 0)
        {
            $rrule['COUNT'] = $data_class->get_frequency_count();
        }
        
        if ($data_class->get_frequency_interval() > 0)
        {
            $rrule['INTERVAL'] = $data_class->get_frequency_interval();
        }
        
        if ($data_class->get_byday())
        {
            $rrule['BYDAY'] = self::get_byday_parts($data_class->get_byday());
        }
        
        if ($data_class->get_bymonthday())
        {
            $rrule['BYMONTHDAY'] = $data_class->get_bymonthday();
        }
        
        if ($data_class->get_bymonth())
        {
            $rrule['BYMONTH'] = $data_class->get_bymonth();
        }
        
        return $rrule;
    }
}