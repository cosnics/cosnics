<?php
namespace Chamilo\Libraries\Utilities;




use Chamilo\Libraries\Platform\Configuration\LocalSetting;
use Chamilo\Libraries\Platform\Translation;
use DateTime;
use DateTimeZone;

/**
 * $Id: datetime_utilities.class.php 128 2009-11-09 13:13:20Z vanpouckesven $
 *
 * @package common.datetime
 */
class DatetimeUtilities
{

    /**
     * Get a four digit year from a two digit year.
     * The century depends on the year difference between the given year
     * and the current year. e.g. with default $years_difference_for_century value of 20: - calling the function in 2009
     * with a given $year value of 19 return 2019 - calling the function in 2009 with a given $year value of 75 return
     * 1975
     *
     * @param $years_difference_for_century The maximum difference of years between the current year and the given year
     *        to return the current century
     * @return integer A year number
     */
    public static function get_complete_year($year, $years_difference_for_century = 20)
    {
        if (is_numeric($year))
        {
            if ($year > 100)
            {
                return $year;
            }
            else
            {
                if ($year <= date('y') || $year - date('y') < $years_difference_for_century)
                {
                    return (date('Y') - date('y') + $year);
                }
                else
                {
                    return (date('Y') - date('y') - 100 + $year);
                }
            }
        }
        else
        {
            return null;
        }
    }

    /**
     * formats the date according to the locale settings
     *
     * @author Patrick Cool <patrick.cool@UGent.be>, Ghent University
     * @author Christophe Gesch� <gesche@ipm.ucl.ac.be> originally inspired from from PhpMyAdmin
     * @param string $formatOfDate date pattern
     * @param integer $timestamp, default is NOW.
     * @return the formatted date
     */
    public static function format_locale_date($dateFormat = null, $timeStamp = -1)
    {
        if (! $dateFormat)
        {
            $dateFormat = self :: default_date_time_format();
        }

        $DaysShort = self :: get_days_short(); // Defining the shorts for the days
        $DaysLong = self :: get_days_long(); // Defining the days of the week to allow translation of the days
        $MonthsShort = self :: get_month_short(); // Defining the shorts for the months
        $MonthsLong = self :: get_month_long(); // Defining the months of the year to allow translation of the months
                                                // with the ereg we replace %aAbB of date format
                                                // (they can be done by the system when locale date aren't aivailable

        $date = preg_replace('/%[A]/', $DaysLong[(int) strftime('%w', $timeStamp)], $dateFormat);
        $date = preg_replace('/%[a]/', $DaysShort[(int) strftime('%w', $timeStamp)], $date);
        $date = preg_replace('/%[B]/', $MonthsLong[(int) strftime('%m', $timeStamp) - 1], $date);
        $date = preg_replace('/%[b]/', $MonthsShort[(int) strftime('%m', $timeStamp) - 1], $date);

        if ($timeStamp == - 1)
        {
            $timeStamp = time();
        }

        return strftime($date, $timeStamp);
    }

    private static function default_date_time_format()
    {
        $translator = Translation :: get_instance();
        $short_date = $translator->get('DateFormatShort', null, Utilities :: COMMON_LIBRARIES);
        $time = $translator->get('TimeNoSecFormat', null, Utilities :: COMMON_LIBRARIES);
        $dateFormat = "{$short_date},  {$time}";
        return $dateFormat;
    }

    /**
     * Convert the given date to the selected timezone
     *
     * @param String $date The date
     * @param String $timezone The selected timezone
     */
    public static function convert_date_to_timezone($date, $format = null, $timezone = null)
    {
        if (! $format)
        {
            $format = self :: default_date_time_format();
        }

        if (! $timezone)
        {
            $timezone = LocalSetting :: get('platform_timezone');
            if (! $timezone)
            {
                return self :: format_locale_date($format, $date);
            }
        }

        $date_time_zone = new DateTimeZone($timezone);
        $gmt_time_zone = new DateTimeZone('GMT');

        $date_time = new DateTime($date, $gmt_time_zone);
        $offset = $date_time_zone->getOffset($date_time);

        return self :: format_locale_date($format, $date_time->format('U') + $offset);
    }

    /**
     * Convert the seconds to h:m:s or m:s or s
     *
     * @param String $time
     */
    public static function convert_seconds_to_hours($time)
    {
        if ($time / 3600 < 1 && $time / 60 < 1)
        {
            $converted_time = $time . 's';
        }
        else
        {
            if ($time / 3600 < 1)
            {
                $min = (int) ($time / 60);
                $sec = $time % 60;
                $converted_time = $min . 'm ' . $sec . 's';
            }
            else
            {
                $hour = (int) ($time / 3600);
                $rest = $time % 3600;
                $min = (int) ($rest / 60);
                $sec = $rest % 60;
                $converted_time = $hour . 'h ' . $min . 'm ' . $sec . 's';
            }
        }
        return $converted_time;
    }

    /**
     * Defining the shorts for the days.
     * Memoized.
     *
     * @return array
     */
    public static function get_days_short()
    {
        static $result = false;
        if ($result)
        {
            return $result;
        }

        $translator = Translation :: get_instance();

        return $result = array(
            $translator->get('SundayShort', null, Utilities :: COMMON_LIBRARIES),
            $translator->get('MondayShort', null, Utilities :: COMMON_LIBRARIES),
            $translator->get('TuesdayShort', null, Utilities :: COMMON_LIBRARIES),
            $translator->get('WednesdayShort', null, Utilities :: COMMON_LIBRARIES),
            $translator->get('ThursdayShort', null, Utilities :: COMMON_LIBRARIES),
            $translator->get('FridayShort', null, Utilities :: COMMON_LIBRARIES),
            $translator->get('SaturdayShort', null, Utilities :: COMMON_LIBRARIES));
    }

    /**
     * Defining the days of the week to allow translation of the days.
     * Memoized.
     *
     * @return array
     */
    public static function get_days_long()
    {
        static $result = false;
        if ($result)
        {
            return $result;
        }
        $translator = Translation :: get_instance();

        return $result = array(
            $translator->get('SundayLong', null, Utilities :: COMMON_LIBRARIES),
            $translator->get('MondayLong', null, Utilities :: COMMON_LIBRARIES),
            $translator->get('TuesdayLong', null, Utilities :: COMMON_LIBRARIES),
            $translator->get('WednesdayLong', null, Utilities :: COMMON_LIBRARIES),
            $translator->get('ThursdayLong', null, Utilities :: COMMON_LIBRARIES),
            $translator->get('FridayLong', null, Utilities :: COMMON_LIBRARIES),
            $translator->get('SaturdayLong', null, Utilities :: COMMON_LIBRARIES));
    }

    /**
     * Defining the shorts for the months.
     * Memoized.
     *
     * @return array
     */
    public static function get_month_short()
    {
        static $result = false;
        if ($result)
        {
            return $result;
        }

        $translator = Translation :: get_instance();

        return $result = array(
            $translator->get('JanuaryShort', null, Utilities :: COMMON_LIBRARIES),
            $translator->get('FebruaryShort', null, Utilities :: COMMON_LIBRARIES),
            $translator->get('MarchShort', null, Utilities :: COMMON_LIBRARIES),
            $translator->get('AprilShort', null, Utilities :: COMMON_LIBRARIES),
            $translator->get('MayShort', null, Utilities :: COMMON_LIBRARIES),
            $translator->get('JuneShort', null, Utilities :: COMMON_LIBRARIES),
            $translator->get('JulyShort', null, Utilities :: COMMON_LIBRARIES),
            $translator->get('AugustShort', null, Utilities :: COMMON_LIBRARIES),
            $translator->get('SeptemberShort', null, Utilities :: COMMON_LIBRARIES),
            $translator->get('OctoberShort', null, Utilities :: COMMON_LIBRARIES),
            $translator->get('NovemberShort', null, Utilities :: COMMON_LIBRARIES),
            $translator->get('DecemberShort', null, Utilities :: COMMON_LIBRARIES));
    }

    /**
     * Defining the shorts for the months.
     * Memoized.
     *
     * @return array
     */
    public static function get_month_long()
    {
        static $result = false;
        if ($result)
        {
            return $result;
        }

        $translator = Translation :: get_instance();

        return $result = array(
            $translator->get('JanuaryLong', null, Utilities :: COMMON_LIBRARIES),
            $translator->get('FebruaryLong', null, Utilities :: COMMON_LIBRARIES),
            $translator->get('MarchLong', null, Utilities :: COMMON_LIBRARIES),
            $translator->get('AprilLong', null, Utilities :: COMMON_LIBRARIES),
            $translator->get('MayLong', null, Utilities :: COMMON_LIBRARIES),
            $translator->get('JuneLong', null, Utilities :: COMMON_LIBRARIES),
            $translator->get('JulyLong', null, Utilities :: COMMON_LIBRARIES),
            $translator->get('AugustLong', null, Utilities :: COMMON_LIBRARIES),
            $translator->get('SeptemberLong', null, Utilities :: COMMON_LIBRARIES),
            $translator->get('OctoberLong', null, Utilities :: COMMON_LIBRARIES),
            $translator->get('NovemberLong', null, Utilities :: COMMON_LIBRARIES),
            $translator->get('DecemberLong', null, Utilities :: COMMON_LIBRARIES));
    }
}
