<?php
namespace Chamilo\Libraries\Calendar\Renderer\Type;

use Chamilo\Libraries\Calendar\Event\Event;
use Chamilo\Libraries\Calendar\Event\RecurrenceRules\VObjectRecurrenceRulesFormatter;
use Chamilo\Libraries\Calendar\Renderer\Interfaces\CalendarRendererProviderInterface;
use Chamilo\Libraries\Calendar\Renderer\Renderer;
use DateTime;
use DateTimeZone;
use Exception;
use Sabre\VObject\Component;
use Sabre\VObject\Component\VCalendar;
use Symfony\Component\HttpFoundation\Response;

/**
 *
 * @package Chamilo\Libraries\Calendar\Renderer\Type
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class ICalRenderer extends Renderer
{
    const TIMEZONE_END = 2145916799;
    const TIMEZONE_START = 100;

    /**
     *
     * @var \Sabre\VObject\Component\VCalendar
     */
    private $calendar;

    /**
     *
     * @param \Chamilo\Libraries\Calendar\Renderer\Interfaces\CalendarRendererProviderInterface $dataProvider
     *
     * @throws \Exception
     */
    public function __construct(CalendarRendererProviderInterface $dataProvider)
    {
        parent::__construct($dataProvider);

        $this->calendar = new VCalendar();
    }

    /**
     *
     * @see \Chamilo\Libraries\Calendar\Renderer\Renderer::render()
     */
    public function render()
    {
        $this->addTimeZone();
        $this->addEvents();

        return $this->getCalendar()->serialize();
    }

    /**
     *
     * @param \Chamilo\Libraries\Calendar\Event\Event $providedEvent
     *
     * @throws \Exception
     */
    private function addEvent(Event $providedEvent)
    {
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

        $uniqueIdentifiers = array(
            $providedEvent->getSource(),
            $providedEvent->getId(),
            $providedEvent->getStartDate(),
            $providedEvent->getEndDate()
        );

        $event->add('UID', md5(serialize($uniqueIdentifiers)));

        if ($providedEvent->getUrl())
        {
            $event->add('URL', $providedEvent->getUrl());
        }

        $vObjectRecurrenceRulesFormatter = new VObjectRecurrenceRulesFormatter();

        if ($providedEvent->getRecurrenceRules()->hasRecurrence())
        {
            $event->add('RRULE', $vObjectRecurrenceRulesFormatter->format($providedEvent->getRecurrenceRules()));
        }
    }

    /**
     * @throws \Exception
     */
    private function addEvents()
    {
        $providedEvents = $this->getDataProvider()->getInternalEvents();

        foreach ($providedEvents as $providedEvent)
        {
            $this->addEvent($providedEvent);
        }
    }

    /**
     * @return false|void
     * @throws \Exception
     * @author MicroEducate
     * @url https://microeducate.tech/generating-an-icalender-vtimezone-component-from-phps-timezone-value/
     */
    private function addTimeZone()
    {
        $from = time();
        $to = $from;

        try
        {
            $tz = new DateTimeZone(date_default_timezone_get());

            // get all transitions for one year back/ahead
            $year = 86400 * 360;
            $transitions = $tz->getTransitions($from - $year, $to + $year);

            $vt = new Component($this->getCalendar(), 'VTIMEZONE');
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
                    $dst = new Component($this->getCalendar(), 'DAYLIGHT');
                    $cmp = $dst;
                }
                // standard time definition
                else
                {
                    $t_std = $trans['ts'];
                    $std = new Component($this->getCalendar(), 'STANDARD');
                    $cmp = $std;
                }

                if ($cmp)
                {
                    $dt = new DateTime($trans['time']);
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

            $this->getCalendar()->add($vt);
        }
        catch (Exception $e)
        {
        }
    }

    /**
     *
     * @return \Sabre\VObject\Component\VCalendar
     */
    public function getCalendar()
    {
        return $this->calendar;
    }

    /**
     *
     * @param \Sabre\VObject\Component\VCalendar $calendar
     */
    public function setCalendar(VCalendar $calendar)
    {
        $this->calendar = $calendar;
    }

    /**
     * Render the iCal calendar and send it
     */
    public function renderAndSend()
    {
        $this->sendResponse($this->render());
    }

    /**
     *
     * @param string $serializedCalendar
     */
    private function sendResponse($serializedCalendar)
    {
        $headers = [];

        $headers['Content-Type'] = 'text/calendar; charset=utf-8';
        $headers['Content-Disposition'] = 'attachment; filename="myCalendar.ics"';

        $response = new Response($serializedCalendar, 200, $headers);
        $response->send();
    }
}
