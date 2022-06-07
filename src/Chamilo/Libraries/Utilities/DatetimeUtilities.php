<?php
namespace Chamilo\Libraries\Utilities;

use Chamilo\Libraries\DependencyInjection\DependencyInjectionContainerBuilder;
use Chamilo\Libraries\Platform\Configuration\LocalSetting;
use DateTime;
use DateTimeZone;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Libraries\Utilities
 */
class DatetimeUtilities
{
    protected static ?DatetimeUtilities $instance = null;

    /**
     * @var string[][]
     */
    private array $daysLong = [];

    /**
     * @var string[][]
     */
    private array $daysShort = [];

    /**
     * @var string[][]
     */
    private array $monthsLong = [];

    /**
     * @var string[][]
     */
    private array $monthsShort = [];

    private Translator $translator;

    public function __construct(Translator $translator)
    {
        $this->translator = $translator;
    }

    /**
     * @throws \Exception
     */
    public function convertDateToTimezone(string $date, ?string $format = null, ?string $timezone = null): string
    {
        if (!$format)
        {
            $format = $this->defaultDateTimeFormat();
        }

        if (!$timezone)
        {
            $timezone = LocalSetting::getInstance()->get('platform_timezone');
            if (!$timezone)
            {
                return $this->formatLocaleDate($format, $date);
            }
        }

        $date_time_zone = new DateTimeZone($timezone);
        $gmt_time_zone = new DateTimeZone('GMT');

        $date_time = new DateTime($date, $gmt_time_zone);
        $offset = $date_time_zone->getOffset($date_time);

        return $this->formatLocaleDate($format, (int) $date_time->format('U') + $offset);
    }

    /**
     * Convert the seconds to hhh:mm:ss or mm:ss or ss
     */
    public function convertSecondsToHours(int $time): string
    {
        if ($time / 3600 < 1 && $time / 60 < 1)
        {
            $converted_time = '000h 00m ' . str_pad($time, 2, '0', STR_PAD_LEFT) . 's';
        }
        else
        {
            if ($time / 3600 < 1)
            {
                $min = (int) ($time / 60);
                $sec = $time % 60;
                $converted_time =
                    '000h ' . str_pad($min, 2, '0', STR_PAD_LEFT) . 'm ' . str_pad($sec, 2, '0', STR_PAD_LEFT) . 's';
            }
            else
            {
                $hour = (int) ($time / 3600);
                $rest = $time % 3600;
                $min = (int) ($rest / 60);
                $sec = $rest % 60;
                $converted_time =
                    str_pad($hour, 3, '0', STR_PAD_LEFT) . 'h ' . str_pad($min, 2, '0', STR_PAD_LEFT) . 'm ' .
                    str_pad($sec, 2, '0', STR_PAD_LEFT) . 's';
            }
        }

        return $converted_time;
    }

    private function defaultDateTimeFormat(): string
    {
        $shortDate = $this->translator->trans('DateFormatShort', [], StringUtilities::LIBRARIES);
        $time = $this->translator->trans('TimeNoSecFormat', [], StringUtilities::LIBRARIES);

        return "$shortDate,  $time";
    }

    /**
     * @author Patrick Cool <patrick.cool@UGent.be>, Ghent University
     * @author Christophe Gesche <gesche@ipm.ucl.ac.be> originally inspired by PhpMyAdmin
     */
    public function formatLocaleDate(?string $dateFormat = null, int $timeStamp = - 1): string
    {
        if (!$dateFormat)
        {
            $dateFormat = $this->defaultDateTimeFormat();
        }

        $DaysShort = $this->getDaysShort(); // Defining the shorts for the days
        $DaysLong = $this->getDaysLong(); // Defining the days of the week to allow translation of the days
        $MonthsShort = $this->getMonthsShort(); // Defining the shorts for the months
        $MonthsLong = $this->getMonthslong(); // Defining the months of the year to allow translation of the months
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

    public function formatSecondsToHours(int $seconds): string
    {
        $hours = floor($seconds / 3600);
        $rest = $seconds % 3600;

        $minutes = floor($rest / 60);
        $seconds = $rest % 60;

        if ($minutes < 10)
        {
            $minutes = '0' . $minutes;
        }

        if ($seconds < 10)
        {
            $seconds = '0' . $seconds;
        }

        return $hours . ':' . $minutes . ':' . $seconds;
    }

    public function formatSecondsToMinutes(int $seconds): string
    {
        $minutes = floor($seconds / 60);
        $seconds = $seconds % 60;

        if ($minutes < 10)
        {
            $minutes = '0' . $minutes;
        }

        if ($seconds < 10)
        {
            $seconds = '0' . $seconds;
        }

        return $minutes . ':' . $seconds;
    }

    /**
     * Defining the days of the week to allow translation of the days.
     *
     * @return string[]
     */
    public function getDaysLong(): array
    {
        $translator = $this->translator;
        $locale = $this->translator->getLocale();

        if (!($this->daysLong[$locale]))
        {
            $this->daysLong[$locale] = array(
                $translator->trans('SundayLong', [], StringUtilities::LIBRARIES),
                $translator->trans('MondayLong', [], StringUtilities::LIBRARIES),
                $translator->trans('TuesdayLong', [], StringUtilities::LIBRARIES),
                $translator->trans('WednesdayLong', [], StringUtilities::LIBRARIES),
                $translator->trans('ThursdayLong', [], StringUtilities::LIBRARIES),
                $translator->trans('FridayLong', [], StringUtilities::LIBRARIES),
                $translator->trans('SaturdayLong', [], StringUtilities::LIBRARIES)
            );
        }

        return $this->daysLong[$locale];
    }

    /**
     * Defining the shorts for the days.
     *
     * @return string[]
     */
    public function getDaysShort(): array
    {
        $translator = $this->translator;
        $locale = $this->translator->getLocale();

        if (!($this->daysShort[$locale]))
        {
            $this->daysShort[$locale] = array(
                $translator->trans('SundayShort', [], StringUtilities::LIBRARIES),
                $translator->trans('MondayShort', [], StringUtilities::LIBRARIES),
                $translator->trans('TuesdayShort', [], StringUtilities::LIBRARIES),
                $translator->trans('WednesdayShort', [], StringUtilities::LIBRARIES),
                $translator->trans('ThursdayShort', [], StringUtilities::LIBRARIES),
                $translator->trans('FridayShort', [], StringUtilities::LIBRARIES),
                $translator->trans('SaturdayShort', [], StringUtilities::LIBRARIES)
            );
        }

        return $this->daysShort[$locale];
    }

    /**
     * @throws \Exception
     */
    public static function getInstance(): DatetimeUtilities
    {
        if (is_null(static::$instance))
        {
            /**
             * @var \Symfony\Component\Translation\Translator $translator
             */
            $translator = DependencyInjectionContainerBuilder::getInstance()->createContainer()->get(
                'Symfony\Component\Translation\Translator'
            );

            self::$instance = new static($translator);
        }

        return static::$instance;
    }

    /**
     * Defining the shorts for the months.
     *
     * @return string[]
     */
    public function getMonthsShort(): array
    {
        $translator = $this->translator;
        $locale = $this->translator->getLocale();

        if (!($this->monthsShort[$locale]))
        {
            $this->monthsShort[$locale] = array(
                $translator->trans('JanuaryShort', [], StringUtilities::LIBRARIES),
                $translator->trans('FebruaryShort', [], StringUtilities::LIBRARIES),
                $translator->trans('MarchShort', [], StringUtilities::LIBRARIES),
                $translator->trans('AprilShort', [], StringUtilities::LIBRARIES),
                $translator->trans('MayShort', [], StringUtilities::LIBRARIES),
                $translator->trans('JuneShort', [], StringUtilities::LIBRARIES),
                $translator->trans('JulyShort', [], StringUtilities::LIBRARIES),
                $translator->trans('AugustShort', [], StringUtilities::LIBRARIES),
                $translator->trans('SeptemberShort', [], StringUtilities::LIBRARIES),
                $translator->trans('OctoberShort', [], StringUtilities::LIBRARIES),
                $translator->trans('NovemberShort', [], StringUtilities::LIBRARIES),
                $translator->trans('DecemberShort', [], StringUtilities::LIBRARIES)
            );
        }

        return $this->monthsShort[$locale];
    }

    /**
     * Defining the shorts for the months.
     *
     * @return string[]
     */
    public function getMonthslong(): array
    {
        $translator = $this->translator;
        $locale = $this->translator->getLocale();

        if (!($this->monthsLong[$locale]))
        {
            $this->monthsLong[$locale] = array(
                $translator->trans('JanuaryLong', [], StringUtilities::LIBRARIES),
                $translator->trans('FebruaryLong', [], StringUtilities::LIBRARIES),
                $translator->trans('MarchLong', [], StringUtilities::LIBRARIES),
                $translator->trans('AprilLong', [], StringUtilities::LIBRARIES),
                $translator->trans('MayLong', [], StringUtilities::LIBRARIES),
                $translator->trans('JuneLong', [], StringUtilities::LIBRARIES),
                $translator->trans('JulyLong', [], StringUtilities::LIBRARIES),
                $translator->trans('AugustLong', [], StringUtilities::LIBRARIES),
                $translator->trans('SeptemberLong', [], StringUtilities::LIBRARIES),
                $translator->trans('OctoberLong', [], StringUtilities::LIBRARIES),
                $translator->trans('NovemberLong', [], StringUtilities::LIBRARIES),
                $translator->trans('DecemberLong', [], StringUtilities::LIBRARIES)
            );
        }

        return $this->monthsLong[$locale];
    }

    /**
     * Converts a date/time value retrieved from a FormValidator datepicker element to the corresponding UNIX itmestamp.
     */
    public function timeFromDatepicker(string $string): int
    {
        $dateTime = explode(' ', $string);
        $yearMonthDday = explode('-', $dateTime[0]);
        $hoursMinutesSeconds = explode(':', $dateTime[1]);

        return mktime(
            $hoursMinutesSeconds[0], $hoursMinutesSeconds[1], $hoursMinutesSeconds[2], $yearMonthDday[1],
            $yearMonthDday[2], $yearMonthDday[0]
        );
    }
}
