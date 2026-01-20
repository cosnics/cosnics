<?php
namespace Chamilo\Libraries\Utilities;

use DateInvalidTimeZoneException;
use DateTime;
use DateTimeZone;
use Exception;
use IntlDateFormatter;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Libraries\Utilities
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class DatetimeUtilities
{

    private Translator $translator;

    public function __construct(Translator $translator)
    {
        $this->translator = $translator;
    }

    public function formatLocaleDate(
        int $timeStamp, int $dateFormat = IntlDateFormatter::SHORT, int $timeFormat = IntlDateFormatter::SHORT,
        ?string $locale = null, ?string $timezone = null
    ): string
    {
        if (!$locale)
        {
            $locale = $this->getTranslator()->getLocale();
        }

        if (!$timezone)
        {
            $timezone = date_default_timezone_get();
        }

        $formatter = new IntlDateFormatter(
            $locale, $dateFormat, $timeFormat, $timezone, IntlDateFormatter::GREGORIAN
        );

        try
        {
            $dateTime = new DateTime('@' . $timeStamp);
            $dateTime->setTimezone(new DateTimeZone(date_default_timezone_get()));

            return $formatter->format($dateTime);
        }
        catch (Exception)
        {
            return '';
        }
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

    public function getFormattedCurrentTimestamp(string $format = 'Y-m-d H:i:s.000', ?string $timezone = null): string
    {
        if (!$timezone)
        {
            $timezone = date_default_timezone_get();
        }

        $dateTime = new DateTime();

        try
        {
            $dateTime = $dateTime->setTimezone(new DateTimeZone($timezone));
        }
        catch (DateInvalidTimeZoneException)
        {
        }

        return $dateTime->format($format);
    }

    public function getTranslator(): Translator
    {
        return $this->translator;
    }

    /**
     * Converts a date/time value retrieved from a FormValidator datepicker element to the corresponding UNIX timestamp.
     */
    public function timeFromDatepicker(string $string): int
    {
        $dateTime = explode(' ', $string);
        $yearMonthDday = explode('-', $dateTime[0]);
        $hoursMinutesSeconds = explode(':', $dateTime[1]);

        return mktime(
            (int) $hoursMinutesSeconds[0], (int) $hoursMinutesSeconds[1], (int) $hoursMinutesSeconds[2],
            (int) $yearMonthDday[1], (int) $yearMonthDday[2], (int) $yearMonthDday[0]
        );
    }
}
