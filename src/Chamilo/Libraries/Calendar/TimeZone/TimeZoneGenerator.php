<?php

namespace Chamilo\Libraries\Calendar\TimeZone;

use kigkonsult\iCalcreator\calendarComponent;
use kigkonsult\iCalcreator\timezoneHandler;
use Sabre\VObject\Component\VCalendar;

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
        $icalCreatorCalendar = new \kigkonsult\iCalcreator\vcalendar();
        timezoneHandler::createTimezone($icalCreatorCalendar, date_default_timezone_get());

        $icalCreatorTimezoneComponent = $icalCreatorCalendar->getComponent('vtimezone');

        $timezone = $calendar->createComponent('VTIMEZONE');
        $timezone->add('TZID', $icalCreatorTimezoneComponent->getProperty('tzid'));

        $componentNames = ['standard', 'daylight'];
        foreach ($componentNames as $componentName)
        {
            $sabreComponent = $this->createComponent($componentName, $icalCreatorTimezoneComponent, $calendar);
            $timezone->add($sabreComponent);
        }

        $calendar->add($timezone);
    }

    /**
     * @param string $componentName
     * @param \kigkonsult\iCalcreator\calendarComponent $icalCreatorTimezoneComponent
     * @param \Sabre\VObject\Component\VCalendar $calendar
     *
     * @return \Sabre\VObject\Component
     */
    protected function createComponent(
        string $componentName, calendarComponent $icalCreatorTimezoneComponent, VCalendar $calendar
    )
    {
        $icalCreatorComponent = $icalCreatorTimezoneComponent->getComponent($componentName);

        $sabreComponent = $calendar->createComponent(strtoupper($componentName));
        $sabreComponent->add('DTSTART', $this->formatDateToSabre($icalCreatorComponent->getProperty('dtstart')));
        $sabreComponent->add('TZOFFSETFROM', $icalCreatorComponent->getProperty('tzoffsetfrom'));
        $sabreComponent->add('TZOFFSETTO', $icalCreatorComponent->getProperty('tzoffsetto'));
        $sabreComponent->add('RDATE', $this->formatDateToSabre($icalCreatorComponent->getProperty('rdate')[0]));
        $sabreComponent->add('TZNAME', $icalCreatorComponent->getProperty('tzname'));

        return $sabreComponent;
    }

    /**
     * @param integer[] $dateArray
     *
     * @return string
     */
    private function formatDateToSabre($dateArray)
    {
        $date = array();

        $date[] = str_pad($dateArray['year'], 4, '0', STR_PAD_LEFT);
        $date[] = str_pad($dateArray['month'], 2, '0', STR_PAD_LEFT);
        $date[] = str_pad($dateArray['day'], 2, '0', STR_PAD_LEFT);
        $date[] = 'T';
        $date[] = str_pad($dateArray['hour'], 2, '0', STR_PAD_LEFT);
        $date[] = str_pad($dateArray['minute'], 2, '0', STR_PAD_LEFT);
        $date[] = str_pad($dateArray['second'], 2, '0', STR_PAD_LEFT);

        return implode('', $date);
    }
}
