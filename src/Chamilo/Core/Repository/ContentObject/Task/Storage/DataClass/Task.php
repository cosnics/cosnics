<?php
namespace Chamilo\Core\Repository\ContentObject\Task\Storage\DataClass;

use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Architecture\Interfaces\AttachmentSupport;
use Chamilo\Libraries\Architecture\Interfaces\Includeable;
use Chamilo\Libraries\Architecture\Interfaces\Versionable;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 *
 * @author Hans De Bisschop
 * @author Dieter De Neef
 */
class Task extends ContentObject implements Versionable, AttachmentSupport, Includeable
{

    public static function get_type_name()
    {
        return ClassnameUtilities::getInstance()->getClassNameFromNamespace(self::class_name(), true);
    }
    
    /**
     * The start date of the calendar event
     */
    const PROPERTY_START_DATE = 'start_date';
    /**
     * The end date of the calendar event
     */
    const PROPERTY_DUE_DATE = 'due_date';
    /**
     * Wheter the event is to be repeated and if so, when it should be repeated
     */
    const PROPERTY_FREQUENCY = 'frequency';
    /**
     * The end date of the repetition
     */
    const PROPERTY_UNTIL = 'until';
    const PROPERTY_FREQUENCY_COUNT = 'frequency_count';
    const PROPERTY_FREQUENCY_INTERVAL = 'frequency_interval';
    const PROPERTY_BYDAY = 'byday';
    const PROPERTY_BYMONTHDAY = 'bymonthday';
    const PROPERTY_BYMONTH = 'bymonth';
    
    /**
     * The type of the task
     */
    const PROPERTY_CATEGORY = 'category';
    /**
     * The priority of the task
     */
    const PROPERTY_PRIORITY = 'priority';
    
    // The different frequency types
    const FREQUENCY_NONE = 0;
    const FREQUENCY_DAILY = 1;
    const FREQUENCY_WEEKLY = 2;
    const FREQUENCY_WEEKDAYS = 3;
    const FREQUENCY_BIWEEKLY = 4;
    const FREQUENCY_MONTHLY = 5;
    const FREQUENCY_YEARLY = 6;
    
    // The different types of task
    const CATEGORY_ANNIVERSARY = 'Anniversary';
    const CATEGORY_BUSINESS = 'Business';
    const CATEGORY_CALL = 'Call';
    const CATEGORY_HOLIDAY = 'Holiday';
    const CATEGORY_GIFT = 'Gift';
    const CATEGORY_CLIENT = 'Client';
    const CATEGORY_COMPETITION = 'Competition';
    const CATEGORY_CONFERENCE = 'Conference';
    const CATEGORY_VARIOUS = 'Various';
    const CATEGORY_SUPPLIER = 'Supplier';
    const CATEGORY_IDEAS = 'Ideas';
    const CATEGORY_PUBLIC_HOLIDAY = 'PublicHoliday';
    const CATEGORY_PRIVATE = 'Private';
    const CATEGORY_FAVORITE = 'Favorite';
    const CATEGORY_PROBLEMS = 'Problems';
    const CATEGORY_PROFESSIONAL = 'Professional';
    const CATEGORY_PROJECTS = 'Projects';
    const CATEGORY_MEETING = 'Meeting';
    const CATEGORY_MONITORING = 'Monitoring';
    const CATEGORY_TRAVEL = 'Travel';
    
    // Priority
    const PRIORITY_NONE = 0;
    const PRIORITY_LOW = 9;
    const PRIORITY_NORMAL = 5;
    const PRIORITY_HIGH = 1;

    public static $days = array(1 => 'MO', 2 => 'TU', 3 => 'WE', 4 => 'TH', 5 => 'FR', 6 => 'SA', 7 => 'SU');

    /**
     * Gets the type of this task
     * 
     * @return int task type
     */
    public function get_category()
    {
        return $this->get_additional_property(self::PROPERTY_CATEGORY);
    }

    /**
     * Sets the type of this task
     * 
     * @param int The type
     */
    public function set_category($category)
    {
        return $this->set_additional_property(self::PROPERTY_CATEGORY, $category);
    }

    /**
     * Gets the priority of this task
     * 
     * @return String task priority
     */
    public function get_priority()
    {
        return $this->get_additional_property(self::PROPERTY_PRIORITY);
    }

    /**
     * Sets the priority of this task
     * 
     * @param String The priority
     */
    public function set_priority($priority)
    {
        return $this->set_additional_property(self::PROPERTY_PRIORITY, $priority);
    }

    /**
     * Gets the start date of this calendar event
     * 
     * @return int The start date
     */
    public function get_start_date()
    {
        return $this->get_additional_property(self::PROPERTY_START_DATE);
    }

    /**
     * Sets the start date of this calendar event
     * 
     * @param int The start date
     */
    public function set_start_date($start_date)
    {
        return $this->set_additional_property(self::PROPERTY_START_DATE, $start_date);
    }

    /**
     * Gets the due date of this calendar event
     * 
     * @return int The due date
     */
    public function get_due_date()
    {
        return $this->get_additional_property(self::PROPERTY_DUE_DATE);
    }

    /**
     * Sets the due date of this calendar event
     * 
     * @param int The due date
     */
    public function set_due_date($due_date)
    {
        return $this->set_additional_property(self::PROPERTY_DUE_DATE, $due_date);
    }

    /**
     * Gets the frequency of this calendar event
     * 
     * @return int The frequency
     */
    public function get_frequency()
    {
        return $this->get_additional_property(self::PROPERTY_FREQUENCY);
    }

    /**
     * Sets the frequency of this calendar event
     * 
     * @param int The frequency
     */
    public function set_frequency($frequency)
    {
        return $this->set_additional_property(self::PROPERTY_FREQUENCY, $frequency);
    }

    /**
     * Gets the end date of this calendar event repetition
     * 
     * @return int The repetition end date
     */
    public function get_until()
    {
        return $this->get_additional_property(self::PROPERTY_UNTIL);
    }

    /**
     * Sets the end date of this calendar event repetition
     * 
     * @param int The repetition end date
     */
    public function set_until($until)
    {
        return $this->set_additional_property(self::PROPERTY_UNTIL, $until);
    }

    /**
     * Returns whether or not the calendar event repeats itself
     * 
     * @return boolean
     */
    public function has_frequency()
    {
        $repeat = $this->get_frequency();
        return ($repeat != '0');
    }

    /**
     * Returns whether or not the calendar event repeats itself indefinately
     * 
     * @return boolean
     */
    public function frequency_is_indefinately()
    {
        $repeat_to = $this->get_until();
        return ($repeat_to == 0 || is_null($repeat_to));
    }

    /**
     * Return the frequency as a string
     */
    public function get_frequency_as_string()
    {
        return self::frequency_as_string($this->get_frequency());
    }

    /**
     *
     * @return string
     */
    public static function frequency_as_string($frequency)
    {
        switch ($frequency)
        {
            case self::FREQUENCY_DAILY :
                $string = Translation::get('Daily');
                break;
            case self::FREQUENCY_WEEKLY :
                $string = Translation::get('Weekly');
                break;
            case self::FREQUENCY_MONTHLY :
                $string = Translation::get('Monthly');
                break;
            case self::FREQUENCY_YEARLY :
                $string = Translation::get('Yearly');
                break;
            case self::FREQUENCY_WEEKDAYS :
                $string = Translation::get('Weekdays');
                break;
            case self::FREQUENCY_BIWEEKLY :
                $string = Translation::get('Biweekly');
                break;
        }
        
        return $string;
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

    public function get_byday()
    {
        return $this->get_additional_property(self::PROPERTY_BYDAY);
    }

    public function set_byday($byday)
    {
        return $this->set_additional_property(self::PROPERTY_BYDAY, $byday);
    }

    public function get_bymonthday()
    {
        return $this->get_additional_property(self::PROPERTY_BYMONTHDAY);
    }

    public function set_bymonthday($bymonthday)
    {
        return $this->set_additional_property(self::PROPERTY_BYMONTHDAY, $bymonthday);
    }

    public function get_bymonth()
    {
        return $this->get_additional_property(self::PROPERTY_BYMONTH);
    }

    public function set_bymonth($bymonth)
    {
        return $this->set_additional_property(self::PROPERTY_BYMONTH, $bymonth);
    }

    public static function get_frequency_options()
    {
        $options = array();
        
        $options[self::FREQUENCY_DAILY] = Translation::get('Daily');
        $options[self::FREQUENCY_WEEKLY] = Translation::get('Weekly');
        $options[self::FREQUENCY_MONTHLY] = Translation::get('Monthly');
        $options[self::FREQUENCY_YEARLY] = Translation::get('Yearly');
        $options[self::FREQUENCY_WEEKDAYS] = Translation::get('Weekdays');
        $options[self::FREQUENCY_BIWEEKLY] = Translation::get('BiWeekly');
        
        return $options;
    }

    public static function get_additional_property_names()
    {
        return array(
            self::PROPERTY_START_DATE, 
            self::PROPERTY_DUE_DATE, 
            self::PROPERTY_UNTIL, 
            self::PROPERTY_FREQUENCY, 
            self::PROPERTY_FREQUENCY_COUNT, 
            self::PROPERTY_FREQUENCY_INTERVAL, 
            self::PROPERTY_BYDAY, 
            self::PROPERTY_BYMONTH, 
            self::PROPERTY_BYMONTHDAY);
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

    public static function get_priority_options()
    {
        $options = array();
        
        $options[self::PRIORITY_NONE] = Translation::get('Unspecified');
        $options[self::PRIORITY_LOW] = Translation::get('Low');
        $options[self::PRIORITY_NORMAL] = Translation::get('Normal');
        $options[self::PRIORITY_HIGH] = Translation::get('High');
        
        return $options;
    }

    public static function get_types_options()
    {
        $types = array();
        
        $types[self::CATEGORY_ANNIVERSARY] = Translation::get('Anniversary');
        $types[self::CATEGORY_BUSINESS] = Translation::get('Business');
        $types[self::CATEGORY_CALL] = Translation::get('Call');
        $types[self::CATEGORY_HOLIDAY] = Translation::get('Holiday');
        $types[self::CATEGORY_GIFT] = Translation::get('Gift');
        $types[self::CATEGORY_CLIENT] = Translation::get('Client');
        $types[self::CATEGORY_COMPETITION] = Translation::get('Competition');
        $types[self::CATEGORY_CONFERENCE] = Translation::get('Conference');
        $types[self::CATEGORY_VARIOUS] = Translation::get('Various');
        $types[self::CATEGORY_SUPPLIER] = Translation::get('Supplier');
        $types[self::CATEGORY_IDEAS] = Translation::get('Ideas');
        $types[self::CATEGORY_PUBLIC_HOLIDAY] = Translation::get('PublicHoliday');
        $types[self::CATEGORY_PRIVATE] = Translation::get('Private');
        $types[self::CATEGORY_FAVORITE] = Translation::get('Favorite');
        $types[self::CATEGORY_PROBLEMS] = Translation::get('Problems');
        $types[self::CATEGORY_PROFESSIONAL] = Translation::get('Professional');
        $types[self::CATEGORY_PROJECTS] = Translation::get('Projects');
        $types[self::CATEGORY_MEETING] = Translation::get('Meeting');
        $types[self::CATEGORY_MONITORING] = Translation::get('Monitoring');
        $types[self::CATEGORY_TRAVEL] = Translation::get('Travel');
        asort($types);
        return $types;
    }

    public function get_priority_as_string()
    {
        return self::priority_as_string($this->get_priority());
    }

    /**
     * Return the task-priority as a string
     */
    public function priority_as_string($priority)
    {
        switch ($priority)
        {
            case self::PRIORITY_LOW :
                $string = Translation::get('Low');
                break;
            case self::PRIORITY_NORMAL :
                $string = Translation::get('Normal');
                break;
            case self::PRIORITY_HIGH :
                $string = Translation::get('High');
                break;
        }
        return $string;
    }

    public function get_category_as_string()
    {
        return self::category_as_string($this->get_category());
    }

    /**
     *
     * @return string
     */
    public static function category_as_string($type)
    {
        switch ($type)
        {
            case self::CATEGORY_ANNIVERSARY :
                $string = Translation::get('Anniversary');
                break;
            case self::CATEGORY_BUSINESS :
                $string = Translation::get('Business');
                break;
            case self::CATEGORY_CALL :
                $string = Translation::get('Call');
                break;
            case self::CATEGORY_HOLIDAY :
                $string = Translation::get('Holiday');
                break;
            case self::CATEGORY_GIFT :
                $string = Translation::get('Gift');
                break;
            case self::CATEGORY_CLIENT :
                $string = Translation::get('Client');
                break;
            case self::CATEGORY_COMPETITION :
                $string = Translation::get('Competition');
                break;
            case self::CATEGORY_CONFERENCE :
                $string = Translation::get('Conference');
                break;
            case self::CATEGORY_VARIOUS :
                $string = Translation::get('Various');
                break;
            case self::CATEGORY_SUPPLIER :
                $string = Translation::get('Supplier');
                break;
            case self::CATEGORY_IDEAS :
                $string = Translation::get('Ideas');
                break;
            case self::CATEGORY_PUBLIC_HOLIDAY :
                $string = Translation::get('PublicHoliday');
                break;
            case self::CATEGORY_PRIVATE :
                $string = Translation::get('Private');
                break;
            case self::CATEGORY_FAVORITE :
                $string = Translation::get('Favorite');
                break;
            case self::CATEGORY_PROBLEMS :
                $string = Translation::get('Problems');
                break;
            case self::CATEGORY_PROFESSIONAL :
                $string = Translation::get('Professional');
                break;
            case self::CATEGORY_PROJECTS :
                $string = Translation::get('Projects');
                break;
            case self::CATEGORY_MEETING :
                $string = Translation::get('Meeting');
                break;
            case self::CATEGORY_MONITORING :
                $string = Translation::get('Monitoring');
                break;
            case self::CATEGORY_TRAVEL :
                $string = Translation::get('Travel');
                break;
        }
        
        return $string;
    }
}
