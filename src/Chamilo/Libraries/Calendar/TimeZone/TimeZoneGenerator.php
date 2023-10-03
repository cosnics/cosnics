<?php

namespace Chamilo\Libraries\Calendar\TimeZone;

use Kigkonsult\Icalcreator\CalendarComponent;
use Kigkonsult\Icalcreator\TimezoneHandler;
use Sabre\VObject\Component\VCalendar;
use Sabre\VObject\Component;

/**
 * SABRE VObject does not provide the functionality to generate the timezone metadata for ICAL. However
 * ICalCreator does provide the necessary metadata and calculations. This service uses ICalCreator to generate
 * the Timezone metadata and then maps this to the SABRE VCalendar object.
 *
 * @package Chamilo\Libraries\Calendar\TimeZone
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class TimeZoneGenerator
{
    /**
     * SABRE VObject does not provide the functionality to generate the timezone metadata for ICAL. However
     * ICalCreator does provide the necessary metadata and calculations. This service uses ICalCreator to generate
     * the Timezone metadata and then maps this to the SABRE VCalendar object.
     *
     * @param \Sabre\VObject\Component\VCalendar $calendar
     */
    public function generateTimeZoneForCalendar(VCalendar $calendar)
    {
        $from = time();
        $to = $from;

        try
        {
            $tz = new \DateTimeZone(date_default_timezone_get());

            // get all transitions for one year back/ahead
            $year = 86400 * 360;
            $transitions = $tz->getTransitions($from - $year, $to + $year);

            $vt = $calendar->createComponent('VTIMEZONE');
            $vt->TZID = $tz->getName();

            $std = null;
            $dst = null;
            foreach ($transitions as $i => $trans)
            {
                $cmp = null;

                // skip the first entry...
                if ($i == 0)
                {
                    // ... but remember the offset for the next TZOFFSETFROM value
                    $tzfrom = $trans['offset'] / 3600;
                    continue;
                }

                // daylight saving time definition
                if ($trans['isdst'])
                {
                    $t_dst = $trans['ts'];
                    $dst = new Component($calendar, 'DAYLIGHT');
                    $cmp = $dst;
                }
                // standard time definition
                else
                {
                    $t_std = $trans['ts'];
                    $std = new Component($calendar, 'STANDARD');
                    $cmp = $std;
                }

                if ($cmp)
                {
                    $dt = new \DateTime($trans['time']);
                    $offset = $trans['offset'] / 3600;

                    $cmp->DTSTART = $dt->format('Ymd\THis');
                    $cmp->TZOFFSETFROM =
                        sprintf('%s%02d%02d', $tzfrom >= 0 ? '+' : '', floor($tzfrom), ($tzfrom - floor($tzfrom)) * 60);
                    $cmp->TZOFFSETTO =
                        sprintf('%s%02d%02d', $offset >= 0 ? '+' : '', floor($offset), ($offset - floor($offset)) * 60);

                    // add abbreviated timezone name if available
                    if (!empty($trans['abbr']))
                    {
                        $cmp->TZNAME = $trans['abbr'];
                    }

                    $tzfrom = $offset;
                    $vt->add($cmp);
                }

                // we covered the entire date range
                if ($std && $dst && min($t_std, $t_dst) < $from && max($t_std, $t_dst) > $to)
                {
                    break;
                }
            }

            $calendar->add($vt);
        }
        catch (Exception $e)
        {
        }
    }
}
