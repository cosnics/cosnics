<?php
namespace Chamilo\Application\Calendar\Extension\Office365\Integration\Chamilo\Libraries\Calendar\Event;

use Chamilo\Application\Calendar\Extension\Office365\Manager;
use Chamilo\Application\Calendar\Storage\DataClass\AvailableCalendar;
use Chamilo\Libraries\Calendar\Event\EventAttendee;
use Chamilo\Libraries\Translation\Translation;
use DateMalformedStringException;
use DateTime;
use DateTimeZone;
use Exception;
use Microsoft\Graph\Model\Recipient;

/**
 * @package Chamilo\Application\Calendar\Extension\Personal\Integration\Chamilo\Libraries\Calendar\Event
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class EventParser
{

    private AvailableCalendar $availableCalendar;

    private int $fromDate;

    private \Microsoft\Graph\Model\Event $sourceEvent;

    private int $toDate;

    public function __construct(
        AvailableCalendar $availableCalendar, \Microsoft\Graph\Model\Event $office365CalendarEvent, int $fromDate,
        int $toDate
    )
    {
        $this->availableCalendar = $availableCalendar;
        $this->sourceEvent = $office365CalendarEvent;
        $this->fromDate = $fromDate;
        $this->toDate = $toDate;
    }

    private function determineAttendeeType(?string $sourceAttendeeType): ?int
    {
        return match ($sourceAttendeeType)
        {
            'required' => EventAttendee::TYPE_REQUIRED,
            'optional' => EventAttendee::TYPE_OPTIONAL,
            'resource' => EventAttendee::TYPE_RESOURCE,
            default => null
        };
    }

    private function determineResponseStatus(?string $responseType): ?int
    {
        return match ($responseType)
        {
            'organizer' => EventAttendee::RESPONSE_STATUS_ORGANIZER,
            'accepted' => EventAttendee::RESPONSE_STATUS_ACCEPTED,
            'declined' => EventAttendee::RESPONSE_STATUS_DECLINED,
            'tentativelyAccepted' => EventAttendee::RESPONSE_STATUS_TENTATIVE,
            'none', 'notResponded' => EventAttendee::RESPONSE_STATUS_NONE,
            default => null
        };
    }

    private function determineTimeZone(?string $eventTimeZone = null): ?DateTimeZone
    {
        if ($eventTimeZone)
        {
            try
            {
                return new DateTimeZone($eventTimeZone);
            }
            catch (Exception)
            {
                return null;
            }
        }
        else
        {
            return null;
        }
    }

    /**
     * @param ?\Microsoft\Graph\Model\Attendee[] $sourceAttendees
     *
     * @return \Chamilo\Libraries\Calendar\Event\EventAttendee[]
     * @throws \DateMalformedStringException
     */
    private function getAttendees(?array $sourceAttendees): array
    {
        $attendees = [];

        foreach ($sourceAttendees as $sourceAttendee)
        {
            $responseStatus = $this->determineResponseStatus($sourceAttendee['status']['response']);

            if (!is_null($responseStatus) && $responseStatus != EventAttendee::RESPONSE_STATUS_NONE)
            {
                $responseDate = $this->getTimestamp($sourceAttendee['status']['time']);
            }
            else
            {
                $responseDate = null;
            }

            $email = $sourceAttendee['emailAddress']['address'];

            if (is_null($email))
            {
                $email = '-';
            }

            $name = $sourceAttendee['emailAddress']['name'] ?? 'Unknown';

            if (is_null($name))
            {
                $name = 'Unknown';
            }

            $attendees[] = new EventAttendee(
                $email, $name, $this->determineAttendeeType($sourceAttendee['type']), $responseStatus, $responseDate
            );
        }

        return $attendees;
    }

    public function getAvailableCalendar(): AvailableCalendar
    {
        return $this->availableCalendar;
    }

    public function setAvailableCalendar(AvailableCalendar $availableCalendar): EventParser
    {
        $this->availableCalendar = $availableCalendar;

        return $this;
    }

    /**
     * @return \Chamilo\Application\Calendar\Extension\Office365\Integration\Chamilo\Libraries\Calendar\Event\Event[]
     */
    public function getEvents(): array
    {
        try
        {
            $sourceEvent = $this->getSourceEvent();

            $startDate = $this->getTimestamp(
                $sourceEvent->getStart()->getDateTime(), $sourceEvent->getStart()->getTimeZone(),
                (bool) $sourceEvent->getIsAllDay()
            );

            $endDate = $this->getTimestamp(
                $sourceEvent->getEnd()->getDateTime(), $sourceEvent->getEnd()->getTimeZone(),
                (bool) $sourceEvent->getIsAllDay()
            );

            $event = new Event(
                $sourceEvent->getId(), $startDate, $endDate, null, $sourceEvent->getWebLink(),
                $sourceEvent->getSubject(), strip_tags($sourceEvent->getBody()->getContent(), '<br>'),
                $sourceEvent->getLocation()->getDisplayName(),
                $this->getSource($this->getAvailableCalendar()->getName()), Manager::CONTEXT,
                $this->getOrganizer($sourceEvent->getOrganizer()), $this->getAttendees($sourceEvent->getAttendees())
            );

            return [$event];
        }
        catch (DateMalformedStringException)
        {
            return [];
        }
    }

    public function getFromDate(): int
    {
        return $this->fromDate;
    }

    public function setFromDate(int $fromDate): EventParser
    {
        $this->fromDate = $fromDate;

        return $this;
    }

    private function getOrganizer(?Recipient $sourceOrganizer): ?EventAttendee
    {
        if ($sourceOrganizer instanceof Recipient)
        {
            $email = $sourceOrganizer->getEmailAddress()->getAddress();

            if (is_null($email))
            {
                $email = '-';
            }

            $name = $sourceOrganizer->getEmailAddress()->getName();

            if (is_null($name))
            {
                $name = 'Unknown';
            }

            return new EventAttendee(
                $email, $name, EventAttendee::TYPE_ORGANIZER, null, EventAttendee::RESPONSE_STATUS_ORGANIZER
            );
        }

        return null;
    }

    private function getSource(string $calendarName): string
    {
        return Translation::get(
            'SourceName', ['CALENDAR' => $calendarName], Manager::CONTEXT
        );
    }

    public function getSourceEvent(): \Microsoft\Graph\Model\Event
    {
        return $this->sourceEvent;
    }

    public function setSourceEvent(\Microsoft\Graph\Model\Event $sourceEvent): EventParser
    {
        $this->sourceEvent = $sourceEvent;

        return $this;
    }

    /**
     * @throws \DateMalformedStringException
     */
    private function getTimestamp(string $eventDateTime, ?string $eventTimeZone = null, bool $isAllDay = false): int
    {
        $dateTime = new DateTime($eventDateTime, $this->determineTimeZone($eventTimeZone));

        if ($isAllDay)
        {
            return mktime(
                0, 0, 0, (int) $dateTime->format('n'), (int) $dateTime->format('j'), (int) $dateTime->format('Y')
            );
        }

        return $dateTime->getTimestamp();
    }

    public function getToDate(): int
    {
        return $this->toDate;
    }

    public function setToDate(int $toDate): EventParser
    {
        $this->toDate = $toDate;

        return $this;
    }
}
