<?php
namespace Chamilo\Libraries\Calendar\Renderer\Type;

use Chamilo\Libraries\Calendar\Event\RecurrenceRules\VObjectRecurrenceRulesFormatter;
use Chamilo\Libraries\Calendar\Renderer\Interfaces\CalendarRendererProviderInterface;
use Chamilo\Libraries\Calendar\Renderer\Renderer;
use Chamilo\Libraries\Calendar\TimeZone\TimeZoneCalendarWrapper;
use Chamilo\Libraries\Format\Response\Response;
use Sabre\VObject\Component\VCalendar;

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
     *
     * @param CalendarRendererProviderInterface $dataProvider
     */
    public function __construct(CalendarRendererProviderInterface $dataProvider)
    {
        parent :: __construct($dataProvider);

        $this->calendar = new VCalendar();
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
        $this->addTimeZone();
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

    public function renderAndSend()
    {
        $this->sendResponse($this->render());
    }

    private function sendResponse($serializedCalendar)
    {
        $response = new Response($serializedCalendar);
        $response->headers->set('Content-Type', 'text/calendar');
        $response->headers->set('Content-Disposition', 'attachment; filename="myCalendar.ics"');
        $response->send();
    }

    /**
     * Add the correct timezone information
     */
    private function addTimeZone()
    {
        \iCalUtilityFunctions :: createTimezone(
            new TimeZoneCalendarWrapper($this->getCalendar()),
            date_default_timezone_get(),
            array()/*,
            self :: TIMEZONE_START,
            self :: TIMEZONE_END*/);
    }

    /**
     *
     * @param \Chamilo\Libraries\Calendar\Event\Event $providedEvent
     */
    private function addEvent(\Chamilo\Libraries\Calendar\Event\Event $providedEvent)
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
