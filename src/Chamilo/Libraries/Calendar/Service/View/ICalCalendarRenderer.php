<?php /** @noinspection PhpUndefinedFieldInspection */
namespace Chamilo\Libraries\Calendar\Service\View;

use Chamilo\Libraries\Calendar\Architecture\Domain\Event;
use Chamilo\Libraries\Calendar\Architecture\Domain\EventAttendee;
use Chamilo\Libraries\Calendar\Architecture\Interface\CalendarRendererProviderInterface;
use Chamilo\Libraries\Calendar\Service\Recurrence\VObjectRecurrenceRulesFormatter;
use DateTime;
use DateTimeZone;
use Exception;
use Sabre\VObject\Component;
use Sabre\VObject\Component\VCalendar;
use Symfony\Component\HttpFoundation\Response;

/**
 * @package Chamilo\Libraries\Calendar\Service\View
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class ICalCalendarRenderer extends CalendarRenderer
{
    private VCalendar $calendar;

    /**
     * @throws \Exception
     */
    public function __construct()
    {
        $this->calendar = new VCalendar();
    }

    /**
     * @throws \Exception
     */
    public function render(CalendarRendererProviderInterface $dataProvider): string
    {
        $this->addTimeZone();
        $this->addEvents($dataProvider);

        return $this->getCalendar()->serialize();
    }

    /**
     * @throws \Exception
     */
    private function addEvent(Event $providedEvent): void
    {
        /**
         * @var \Sabre\VObject\Component\VEvent $event
         */
        $event = $this->getCalendar()->add('VEVENT');

        $event->add(
            'DTSTART', new DateTime(
                date('Y-m-d\TH:i:s', $providedEvent->getStartDate()), new DateTimeZone(date_default_timezone_get())
            )
        );

        $event->add(
            'DTEND', new DateTime(
                date('Y-m-d\TH:i:s', $providedEvent->getEndDate()), new DateTimeZone(date_default_timezone_get())
            )
        );

        $description = trim(strip_tags($providedEvent->getContent()));

        $event->add('LOCATION', trim($providedEvent->getLocation()));
        $event->add('SUMMARY', trim($providedEvent->getTitle()));
        $event->add('DESCRIPTION', $description);

        $event->add(
            'CREATED', new DateTime(date('Y-m-d\TH:i:s', time()), new DateTimeZone(date_default_timezone_get()))
        );
        $event->add(
            'DTSTAMP', new DateTime(date('Y-m-d\TH:i:s', time()), new DateTimeZone(date_default_timezone_get()))
        );

        $uniqueIdentifiers = [
            $providedEvent->getSource(),
            $providedEvent->getId(),
            $providedEvent->getStartDate(),
            $providedEvent->getEndDate()
        ];

        $event->add('UID', md5(serialize($uniqueIdentifiers)));

        if ($providedEvent->getUrl()) {
            $event->add('URL', $providedEvent->getUrl());
        }

        $vObjectRecurrenceRulesFormatter = new VObjectRecurrenceRulesFormatter();

        if ($providedEvent->getRecurrenceRules()->hasRecurrence()) {
            $event->add('RRULE', $vObjectRecurrenceRulesFormatter->format($providedEvent->getRecurrenceRules()));
        }

        if ($providedEvent->getOrganizer() instanceof EventAttendee) {
            $organizerValue = 'MAILTO:' . $providedEvent->getOrganizer()->getEmail();

            if ($providedEvent->getOrganizer()->getName()) {
                $organizerValue = 'CN=' . $providedEvent->getOrganizer()->getName() . ':' . $organizerValue;
            }

            $event->add('ORGANIZER', $organizerValue);
        }

        foreach ($providedEvent->getAttendees() as $attendee) {
            $attendeeValues = [];

            if ($attendee->getICalType()) {
                $attendeeValues['ROLE'] = $attendee->getICalType();
            }

            if ($attendee->getICalResponseStatus()) {
                $attendeeValues['PARTSTAT'] = $attendee->getICalResponseStatus();
            }

            $attendeeValues['CN'] = $attendee->getName();

            $event->add('ATTENDEE', 'MAILTO:' . $attendee->getEmail(), $attendeeValues);
        }
    }

    /**
     * @throws \Exception
     */
    private function addEvents(CalendarRendererProviderInterface $dataProvider): void
    {
        $providedEvents = $dataProvider->getEvents(
            strtotime('first day of 2 months ago midnight'), strtotime('last day of +6 months midnight')
        );

        foreach ($providedEvents as $providedEvent) {
            $this->addEvent($providedEvent);
        }
    }

    /**
     * @return void
     * @throws \Exception
     * @author MicroEducate
     * @url https://microeducate.tech/generating-an-icalender-vtimezone-component-from-phps-timezone-value/
     */
    private function addTimeZone(): void
    {
        try {
            $tz = new DateTimeZone(date_default_timezone_get());

            $currentYear = (int) date('Y');
            $startYear = $currentYear - 1;
            $endYear = $currentYear + 10;

            // Create VTIMEZONE
            /**
             * @var \Sabre\VObject\Component\VTimezone $vtimezone
             */
            $vtimezone = new Component($this->getCalendar(), 'VTIMEZONE');
            $vtimezone->TZID = date_default_timezone_get();

            // Collect transitions
            $transitions = [];
            for ($year = $startYear; $year <= $endYear; $year ++) {
                $yearStart = new DateTime("$year-01-01", $tz);
                $yearEnd = new DateTime(($year + 1) . '-01-01', $tz);

                foreach ($tz->getTransitions($yearStart->getTimestamp(), $yearEnd->getTimestamp()) as $t) {
                    $transitions[] = $t;
                }
            }

            // Keep track of last offsets to properly set TZOFFSETFROM/TZOFFSETTO
            $lastOffsets = [];

            foreach ($transitions as $t) {
                $dt = new DateTime($t['time'], $tz);
                $isDST = $t['isdst'];
                $name = $t['abbr'];

                $offsetSeconds = $t['offset'];
                $hours = floor(abs($offsetSeconds) / 3600);
                $minutes = floor((abs($offsetSeconds) % 3600) / 60);
                $sign = ($offsetSeconds >= 0 ? '+' : '-');
                $tzoffset = sprintf('%s%02d%02d', $sign, $hours, $minutes);

                // Determine previous offset
                $previousOffset = end($lastOffsets) ?: $tzoffset;
                $lastOffsets[] = $tzoffset;

                $type = $isDST ? 'DAYLIGHT' : 'STANDARD';
                $comp = new Component($this->getCalendar(), $type);
                $comp->DTSTART = $dt->format('Ymd\THis');
                $comp->TZOFFSETFROM = $previousOffset;
                $comp->TZOFFSETTO = $tzoffset;
                $comp->TZNAME = $name;

                $vtimezone->add($comp);
            }

            $this->getCalendar()->add($vtimezone);
        }
        catch (Exception) {
        }
    }

    public function getCalendar(): VCalendar
    {
        return $this->calendar;
    }

    public function setCalendar(VCalendar $calendar): void
    {
        $this->calendar = $calendar;
    }

    /**
     * @throws \Exception
     */
    public function renderAndGetResponse(CalendarRendererProviderInterface $dataProvider): Response
    {
        $headers = [];

        $headers['Content-Type'] = 'text/calendar; charset=utf-8';
        $headers['Content-Disposition'] = 'attachment; filename="myCalendar.ics"';

        return new Response($this->render($dataProvider), 200, $headers);
    }
}
