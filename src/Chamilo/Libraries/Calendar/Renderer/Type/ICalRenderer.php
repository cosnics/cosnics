<?php
namespace Chamilo\Libraries\Calendar\Renderer\Type;

use Chamilo\Libraries\Calendar\Event\Event;
use Chamilo\Libraries\Calendar\Event\RecurrenceRules\VObjectRecurrenceRulesFormatter;
use Chamilo\Libraries\Calendar\Renderer\Interfaces\CalendarRendererProviderInterface;
use Chamilo\Libraries\Calendar\Renderer\Renderer;
use Chamilo\Libraries\Calendar\TimeZone\TimeZoneCalendarWrapper;
use Chamilo\Libraries\Calendar\TimeZone\TimeZoneGenerator;
use kigkonsult\iCalcreator\timezoneHandler;
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
    const TIMEZONE_START = 100;
    const TIMEZONE_END = 2145916799;

    /**
     *
     * @var \Sabre\VObject\Component\VCalendar
     */
    private $calendar;
    /**
     * @var \Chamilo\Libraries\Calendar\TimeZone\TimeZoneGenerator
     */
    private $timeZoneGenerator;

    /**
     *
     * @param \Chamilo\Libraries\Calendar\Renderer\Interfaces\CalendarRendererProviderInterface $dataProvider
     * @param \Chamilo\Libraries\Calendar\TimeZone\TimeZoneGenerator $timeZoneGenerator
     *
     * @throws \Exception
     */
    public function __construct(CalendarRendererProviderInterface $dataProvider, TimeZoneGenerator $timeZoneGenerator)
    {
        parent::__construct($dataProvider);

        $this->calendar = new VCalendar();
        $this->timeZoneGenerator = $timeZoneGenerator;
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
     *
     * @see \Chamilo\Libraries\Calendar\Renderer\Renderer::render()
     */
    public function render()
    {
        $this->timeZoneGenerator->generateTimeZoneForCalendar($this->getCalendar());
        $this->addEvents();
        return $this->getCalendar()->serialize();
    }

    private function addEvents()
    {
        $providedEvents = $this->getDataProvider()->getInternalEvents();

        foreach ($providedEvents as $providedEvent)
        {
            $this->addEvent($providedEvent);
        }
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
        $headers = array();

        $headers['Content-Type'] = 'text/calendar; charset=utf-8';
        $headers['Content-Disposition'] = 'attachment; filename="myCalendar.ics"';

        $response = new Response($serializedCalendar, 200, $headers);
        $response->send();
    }

    /**
     *
     * @param \Chamilo\Libraries\Calendar\Event\Event $providedEvent
     */
    private function addEvent(Event $providedEvent)
    {
        $event = $this->getCalendar()->add('VEVENT');

        $event->add(
            'DTSTART',
            new \DateTime(
                date('Y-m-d\TH:i:s', $providedEvent->getStartDate()),
                new \DateTimeZone(date_default_timezone_get())));

        $event->add(
            'DTEND',
            new \DateTime(
                date('Y-m-d\TH:i:s', $providedEvent->getEndDate()),
                new \DateTimeZone(date_default_timezone_get())));

        $description = trim(strip_tags($providedEvent->getContent()));

        $event->add('LOCATION', trim($providedEvent->getLocation()));
        $event->add('SUMMARY', trim($providedEvent->getTitle()));
        $event->add('DESCRIPTION', $description);

        $event->add(
            'CREATED',
            new \DateTime(date('Y-m-d\TH:i:s', time()), new \DateTimeZone(date_default_timezone_get())));
        $event->add(
            'DTSTAMP',
            new \DateTime(date('Y-m-d\TH:i:s', time()), new \DateTimeZone(date_default_timezone_get())));

        $uniqueIdentifiers = array(
            $providedEvent->getSource(),
            $providedEvent->getId(),
            $providedEvent->getStartDate(),
            $providedEvent->getEndDate());

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
}
