<?php
namespace Chamilo\Libraries\Calendar\Event\Recurrence;

use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Translation\Translation;
use Exception;

/**
 * @package Chamilo\Libraries\Calendar\Event\Recurrence
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
abstract class RecurringContentObject extends ContentObject
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
        $translator = Translation::getInstance();

        switch ($frequency)
        {
            case self::FREQUENCY_DAILY :
                $string = $translator->getTranslation('Daily', array(), 'Chamilo\Libraries\Calendar');
                break;
            case self::FREQUENCY_WEEKLY :
                $string = $translator->getTranslation('Weekly', array(), 'Chamilo\Libraries\Calendar');
                break;
            case self::FREQUENCY_MONTHLY :
                $string = $translator->getTranslation('Monthly', array(), 'Chamilo\Libraries\Calendar');
                break;
            case self::FREQUENCY_YEARLY :
                $string = $translator->getTranslation('Yearly', array(), 'Chamilo\Libraries\Calendar');
                break;
            case self::FREQUENCY_WEEKDAYS :
                $string = $translator->getTranslation('Weekdays', array(), 'Chamilo\Libraries\Calendar');
                break;
            case self::FREQUENCY_BIWEEKLY :
                $string = $translator->getTranslation('Biweekly', array(), 'Chamilo\Libraries\Calendar');
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
            1 => $translator->getTranslation("Monday", array(), 'Chamilo\Libraries\Calendar'),
            2 => $translator->getTranslation("Tuesday", array(), 'Chamilo\Libraries\Calendar'),
            3 => $translator->getTranslation("Wednesday", array(), 'Chamilo\Libraries\Calendar'),
            4 => $translator->getTranslation("Thursday", array(), 'Chamilo\Libraries\Calendar'),
            5 => $translator->getTranslation("Friday", array(), 'Chamilo\Libraries\Calendar'),
            6 => $translator->getTranslation("Saturday", array(), 'Chamilo\Libraries\Calendar'),
            7 => $translator->getTranslation("Sunday", array(), 'Chamilo\Libraries\Calendar')
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
            1 => $translator->getTranslation("January", array(), 'Chamilo\Libraries\Calendar'),
            2 => $translator->getTranslation("February", array(), 'Chamilo\Libraries\Calendar'),
            3 => $translator->getTranslation("March", array(), 'Chamilo\Libraries\Calendar'),
            4 => $translator->getTranslation("April", array(), 'Chamilo\Libraries\Calendar'),
            5 => $translator->getTranslation("May", array(), 'Chamilo\Libraries\Calendar'),
            6 => $translator->getTranslation("June", array(), 'Chamilo\Libraries\Calendar'),
            7 => $translator->getTranslation("Juli", array(), 'Chamilo\Libraries\Calendar'),
            8 => $translator->getTranslation("August", array(), 'Chamilo\Libraries\Calendar'),
            9 => $translator->getTranslation("September", array(), 'Chamilo\Libraries\Calendar'),
            10 => $translator->getTranslation("October", array(), 'Chamilo\Libraries\Calendar'),
            11 => $translator->getTranslation("November", array(), 'Chamilo\Libraries\Calendar'),
            12 => $translator->getTranslation("December", array(), 'Chamilo\Libraries\Calendar')
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
        $translator = Translation::getInstance();
        $options = array();

        $options[self::FREQUENCY_DAILY] = $translator->getTranslation('Daily', array(), 'Chamilo\Libraries\Calendar');
        $options[self::FREQUENCY_WEEKDAYS] =
            $translator->getTranslation('Weekdays', array(), 'Chamilo\Libraries\Calendar');
        $options[self::FREQUENCY_WEEKLY] = $translator->getTranslation('Weekly', array(), 'Chamilo\Libraries\Calendar');
        $options[self::FREQUENCY_BIWEEKLY] =
            $translator->getTranslation('BiWeekly', array(), 'Chamilo\Libraries\Calendar');
        $options[self::FREQUENCY_MONTHLY] =
            $translator->getTranslation('Monthly', array(), 'Chamilo\Libraries\Calendar');
        $options[self::FREQUENCY_YEARLY] = $translator->getTranslation('Yearly', array(), 'Chamilo\Libraries\Calendar');

        return $options;
    }

    /**
     * @return string[]
     */
    public static function get_rank_options()
    {
        $translator = Translation::getInstance();
        $ranks = array();

        $ranks[0] = $translator->getTranslation('Every', array(), 'Chamilo\Libraries\Calendar');
        $ranks[1] = $translator->getTranslation('First', array(), 'Chamilo\Libraries\Calendar');
        $ranks[2] = $translator->getTranslation('Second', array(), 'Chamilo\Libraries\Calendar');
        $ranks[3] = $translator->getTranslation('Third', array(), 'Chamilo\Libraries\Calendar');
        $ranks[4] = $translator->getTranslation('Fourth', array(), 'Chamilo\Libraries\Calendar');
        $ranks[5] = $translator->getTranslation('Fifth', array(), 'Chamilo\Libraries\Calendar');
        $ranks[- 1] = $translator->getTranslation('Last', array(), 'Chamilo\Libraries\Calendar');

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
     * @throws \ReflectionException
     */
    public static function get_type_name()
    {
        return ClassnameUtilities::getInstance()->getClassNameFromNamespace(static::class, true);
    }

    /**
     * @return string
     */
    public function get_type_string()
    {
        if ($this->has_frequency())
        {
            return Translation::getInstance()->getTranslation(
                'RepeatingCalendarEvent', array(), 'Chamilo\Libraries\Calendar'
            );
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