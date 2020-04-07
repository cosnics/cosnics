<?php
namespace Chamilo\Libraries\Calendar\Event\Recurrence;

use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\Utilities;
use Exception;

/**
 * @package Chamilo\Libraries\Calendar\Event\Recurrence
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class RecurringContentObject extends ContentObject
{
    const FREQUENCY_BIWEEKLY = 4;
    const FREQUENCY_DAILY = 1;
    const FREQUENCY_MONTHLY = 5;
    const FREQUENCY_NONE = 0;
    const FREQUENCY_WEEKDAYS = 3;
    const FREQUENCY_WEEKLY = 2;
    const FREQUENCY_YEARLY = 6;

    const PROPERTY_BYDAY = 'byday';
    const PROPERTY_BYMONTH = 'bymonth';
    const PROPERTY_BYMONTHDAY = 'bymonthday';
    const PROPERTY_FREQUENCY = 'frequency';
    const PROPERTY_FREQUENCY_COUNT = 'frequency_count';
    const PROPERTY_FREQUENCY_INTERVAL = 'frequency_interval';
    const PROPERTY_UNTIL = 'until';

    public static $days = array(1 => 'MO', 2 => 'TU', 3 => 'WE', 4 => 'TH', 5 => 'FR', 6 => 'SA', 7 => 'SU');

    /**
     * @param integer $frequency
     *
     * @return string
     * @throws \Exception
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
            default:
                throw new Exception();
        }

        return $string;
    }

    /**
     * @return boolean
     */
    public function frequency_is_indefinately()
    {
        $repeat_to = $this->get_until();

        return ($repeat_to == 0 || is_null($repeat_to));
    }

    /**
     * @return string
     */
    public function get_byday()
    {
        return $this->get_additional_property(self::PROPERTY_BYDAY);
    }

    /**
     * @param integer $rank
     * @param integer $day
     *
     * @return string
     */
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

    /**
     * @return string[]
     */
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
            7 => $translator->getTranslation("Sunday", null, Utilities::COMMON_LIBRARIES)
        );
    }

    /**
     * @param string $bydays
     *
     * @return string[]
     */
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

    /**
     * @return string
     */
    public function get_bymonth()
    {
        return $this->get_additional_property(self::PROPERTY_BYMONTH);
    }

    /**
     * @return string[]
     */
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
            12 => $translator->getTranslation("December", null, Utilities::COMMON_LIBRARIES)
        );
    }

    /**
     * @param integer $month
     *
     * @return string
     */
    public static function get_bymonth_string($month)
    {
        $translation = self::get_bymonth_options();

        return $translation[$month];
    }

    /**
     * @return string
     */
    public function get_bymonthday()
    {
        return $this->get_additional_property(self::PROPERTY_BYMONTHDAY);
    }

    /**
     * @return string[]
     */
    public static function get_bymonthday_options()
    {
        return range(1, 31);
    }

    /**
     * @param string $day
     *
     * @return integer
     */
    public static function get_day_format($day)
    {
        $days = array_flip(self::$days);

        return $days[$day];
    }

    /**
     * @param integer $day
     *
     * @return string
     */
    public static function get_day_ical_format($day)
    {
        return self::$days[$day];
    }

    /**
     * @param integer $day_number
     *
     * @return string
     */
    public static function get_day_string($day_number)
    {
        if (!is_numeric($day_number))
        {
            $day_number = self::get_day_format($day_number);
        }
        $translation = self::get_byday_options();

        return $translation[$day_number];
    }

    /**
     * @return integer
     */
    public function get_frequency()
    {
        return $this->get_additional_property(self::PROPERTY_FREQUENCY);
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function get_frequency_as_string()
    {
        return self::frequency_as_string($this->get_frequency());
    }

    /**
     * @return integer
     */
    public function get_frequency_count()
    {
        return $this->get_additional_property(self::PROPERTY_FREQUENCY_COUNT);
    }

    /**
     * @return integer
     */
    public function get_frequency_interval()
    {
        return $this->get_additional_property(self::PROPERTY_FREQUENCY_INTERVAL);
    }

    /**
     * @return string[]
     */
    public static function get_frequency_options()
    {
        $options = array();

        $options[self::FREQUENCY_DAILY] = Translation::get('Daily');
        $options[self::FREQUENCY_WEEKDAYS] = Translation::get('Weekdays');
        $options[self::FREQUENCY_WEEKLY] = Translation::get('Weekly');
        $options[self::FREQUENCY_BIWEEKLY] = Translation::get('BiWeekly');
        $options[self::FREQUENCY_MONTHLY] = Translation::get('Monthly');
        $options[self::FREQUENCY_YEARLY] = Translation::get('Yearly');

        return $options;
    }

    /**
     * @return string[]
     */
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

    /**
     * @param integer $rank
     *
     * @return string
     */
    public static function get_rank_string($rank)
    {
        $translation = self::get_rank_options();

        return $translation[$rank];
    }

    /**
     * @return string
     */
    public static function get_type_name()
    {
        return ClassnameUtilities::getInstance()->getClassNameFromNamespace(self::class_name(), true);
    }

    /**
     * @return string
     */
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

    /**
     * @return integer
     */
    public function get_until()
    {
        return $this->get_additional_property(self::PROPERTY_UNTIL);
    }

    /**
     * @return boolean
     */
    public function has_frequency()
    {
        $repeat = $this->get_frequency();

        return ($repeat != '0');
    }

    /**
     * @param string $byday
     */
    public function set_byday($byday)
    {
        return $this->set_additional_property(self::PROPERTY_BYDAY, $byday);
    }

    /**
     * @param string $bymonth
     */
    public function set_bymonth($bymonth)
    {
        return $this->set_additional_property(self::PROPERTY_BYMONTH, $bymonth);
    }

    /**
     * @param string $bymonthday
     */
    public function set_bymonthday($bymonthday)
    {
        return $this->set_additional_property(self::PROPERTY_BYMONTHDAY, $bymonthday);
    }

    /**
     * @param integer $frequency
     */
    public function set_frequency($frequency)
    {
        return $this->set_additional_property(self::PROPERTY_FREQUENCY, $frequency);
    }

    /**
     * @param integer $frequency_count
     */
    public function set_frequency_count($frequency_count)
    {
        return $this->set_additional_property(self::PROPERTY_FREQUENCY_COUNT, $frequency_count);
    }

    /**
     * @param integer $frequency_interval
     */
    public function set_frequency_interval($frequency_interval)
    {
        return $this->set_additional_property(self::PROPERTY_FREQUENCY_INTERVAL, $frequency_interval);
    }

    /**
     * @param integer $until
     */
    public function set_until($until)
    {
        return $this->set_additional_property(self::PROPERTY_UNTIL, $until);
    }
}