<?php
namespace Chamilo\Application\Calendar\Extension\Office365\Service;

use Chamilo\Application\Calendar\Extension\Office365\Architecture\Domain\Event;
use Chamilo\Application\Calendar\Extension\Office365\Manager;
use Chamilo\Application\Calendar\Storage\DataClass\AvailableCalendar;
use Chamilo\Libraries\Calendar\Architecture\Domain\EventAttendee;
use DateTime;
use DateTimeZone;
use Exception;
use Microsoft\Graph\Generated\Models\Recipient;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Application\Calendar\Extension\Office365\Service
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class EventParser
{

    private Translator $translator;

    public function __construct(Translator $translator)
    {
        $this->translator = $translator;
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
     * @param ?\Microsoft\Graph\Generated\Models\Attendee[] $attendees
     *
     * @return \Chamilo\Libraries\Calendar\Architecture\Domain\EventAttendee[]
     */
    private function getAttendees(?array $attendees): array
    {
        $eventAttendees = [];

        foreach ($attendees as $sourceAttendee)
        {
            if (!$sourceAttendee->getStatus()->getResponse()->is(EventAttendee::RESPONSE_STATUS_NONE))
            {
                $responseDate = $sourceAttendee->getStatus()->getTime()->getTimestamp();
            }
            else
            {
                $responseDate = null;
            }

            $email = $sourceAttendee->getEmailAddress()->getAddress();

            if (is_null($email))
            {
                $email = '-';
            }

            $name = $sourceAttendee->getEmailAddress()->getAddress() ?? 'Unknown';

            if (is_null($name))
            {
                $name = 'Unknown';
            }

            $eventAttendees[] = new EventAttendee(
                $email, $name, $this->determineAttendeeType($sourceAttendee->getType()->value()),
                $this->determineResponseStatus($sourceAttendee->getStatus()->getResponse()->value()), $responseDate
            );
        }

        return $eventAttendees;
    }

    /**
     * @return \Chamilo\Application\Calendar\Extension\Office365\Architecture\Domain\Event[]
     * @throws \Exception
     */
    public function getEvents(
        AvailableCalendar $availableCalendar, \Microsoft\Graph\Generated\Models\Event $sourceEvent
    ): array
    {
        try
        {

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
                $sourceEvent->getLocation()->getDisplayName(), $this->getSource($availableCalendar->getName()),
                Manager::CONTEXT, $this->getOrganizer($sourceEvent->getOrganizer()),
                $this->getAttendees($sourceEvent->getAttendees())
            );

            return [$event];
        }
        catch (Exception)
        {
            return [];
        }
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
        return $this->getTranslator()->trans(
            'SourceName', ['%Calendar%' => $calendarName], Manager::CONTEXT
        );
    }

    /**
     * @throws \Exception
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

    public function getTranslator(): Translator
    {
        return $this->translator;
    }
}
