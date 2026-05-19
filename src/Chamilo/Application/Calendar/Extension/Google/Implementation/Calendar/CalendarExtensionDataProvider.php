<?php
namespace Chamilo\Application\Calendar\Extension\Google\Implementation\Calendar;

use Chamilo\Application\Calendar\Architecture\Interface\CalendarExtensionDataProviderInterface;
use Chamilo\Application\Calendar\Extension\Google\Manager;
use Chamilo\Application\Calendar\Extension\Google\Service\CalendarService;
use Chamilo\Application\Calendar\Extension\Google\Service\EventParser;
use Chamilo\Application\Calendar\Service\AvailabilityService;
use Chamilo\Core\User\Storage\Entity\User;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Application\Calendar\Extension\Google\Service
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
readonly class CalendarExtensionDataProvider implements CalendarExtensionDataProviderInterface
{
    public function __construct(
        protected AvailabilityService $availabilityService, protected CalendarService $calendarService,
        protected EventParser $eventParser, protected Translator $translator
    )
    {
    }

    /**
     * @return \Chamilo\Libraries\Calendar\Architecture\Domain\Event[]
     * @throws \DateInvalidTimeZoneException
     * @throws \Symfony\Component\Cache\Exception\CacheException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\EntityAlreadyExistsException
     */
    private function getCalendarEvents(User $user, string $calendarId, int $fromDate, int $toDate): array
    {
        $events = $this->calendarService->getEventsForCalendarIdentifierAndBetweenDates(
            $user, $calendarId, $fromDate, $toDate
        );

        $parsedEvents = [];

        foreach ($events as $event) {
            $parsedEvents = array_merge(
                $parsedEvents, $this->eventParser->getEvents($events->calendarProperties, $event)
            );
        }

        return $parsedEvents;
    }

    /**
     * @return string[]
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Symfony\Component\Cache\Exception\CacheException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\EntityAlreadyExistsException
     */
    private function getCalendarIdentifiers(User $user): array
    {
        $availabilities = $this->availabilityService->getAvailabilitiesForUserAndCalendarType(
            $user, Manager::CONTEXT
        );

        $calendarIdentifiers = [];

        if ($availabilities->count() == 0) {
            $availableCalendars = $this->getCalendars($user);

            foreach ($availableCalendars as $availableCalendar) {
                $calendarIdentifiers[] = $availableCalendar->getIdentifier();
            }
        }
        else {
            foreach ($availabilities as $availability) {
                if ($availability->isActive()) {
                    $calendarIdentifiers[] = $availability->getCalendarId();
                }
            }
        }

        return $calendarIdentifiers;
    }

    /**
     * @throws \Symfony\Component\Cache\Exception\CacheException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\EntityAlreadyExistsException
     * @see \Chamilo\Application\Calendar\Architecture\Interface\CalendarExtensionDataProviderInterface::getCalendars()
     */
    public function getCalendars(User $user): array
    {
        if (!$this->calendarService->isConfigured() || !$this->calendarService->isAuthenticated($user)) {
            return [];
        }

        return $this->calendarService->getOwnedCalendars($user);
    }

    /**
     * @return array|\Chamilo\Libraries\Calendar\Architecture\Domain\Event[]
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \DateInvalidTimeZoneException
     * @throws \Symfony\Component\Cache\Exception\CacheException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\EntityAlreadyExistsException
     */
    public function getEvents(User $user, $fromDate, $toDate): array
    {
        if (!$this->calendarService->isConfigured() || !$this->calendarService->isAuthenticated($user)) {
            return [];
        }

        $events = [];

        foreach ($this->getCalendarIdentifiers($user) as $calendarIdentifier) {
            $events = array_merge($events, $this->getCalendarEvents($user, $calendarIdentifier, $fromDate, $toDate));
        }

        return $events;
    }

    public function getName(): string
    {
        return $this->translator->trans('TypeName', [], Manager::CONTEXT);
    }
}