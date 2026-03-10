<?php
namespace Chamilo\Application\Calendar\Extension\Google\Implementation\Calendar;

use Chamilo\Application\Calendar\Architecture\Interface\CalendarExtensionDataProviderInterface;
use Chamilo\Application\Calendar\Extension\Google\Manager;
use Chamilo\Application\Calendar\Extension\Google\Service\CalendarService;
use Chamilo\Application\Calendar\Extension\Google\Service\EventParser;
use Chamilo\Application\Calendar\Service\AvailabilityService;
use Chamilo\Core\User\Storage\DataClass\User;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Application\Calendar\Extension\Google\Service
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class CalendarExtensionDataProvider implements CalendarExtensionDataProviderInterface
{
    public const CONTEXT = Manager::CONTEXT;

    private AvailabilityService $availabilityService;

    private CalendarService $calendarService;

    private EventParser $eventParser;

    private Translator $translator;

    public function __construct(
        AvailabilityService $availabilityService, CalendarService $calendarService, EventParser $eventParser,
        Translator $translator
    )
    {
        $this->availabilityService = $availabilityService;
        $this->calendarService = $calendarService;
        $this->eventParser = $eventParser;
        $this->translator = $translator;
    }

    public function getAvailabilityService(): AvailabilityService
    {
        return $this->availabilityService;
    }

    /**
     * @return \Chamilo\Libraries\Calendar\Architecture\Domain\Event[]
     * @throws \DateInvalidTimeZoneException
     * @throws \Symfony\Component\Cache\Exception\CacheException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    private function getCalendarEvents(User $user, string $calendarId, int $fromDate, int $toDate): array
    {
        $events = $this->getCalendarService()->getEventsForCalendarIdentifierAndBetweenDates(
            $user, $calendarId, $fromDate, $toDate
        );

        $parsedEvents = [];

        foreach ($events as $event) {
            $parsedEvents = array_merge(
                $parsedEvents, $this->getEventParser()->getEvents($events->getCalendarProperties(), $event)
            );
        }

        return $parsedEvents;
    }

    /**
     * @return string[]
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Symfony\Component\Cache\Exception\CacheException
     */
    private function getCalendarIdentifiers(User $user): array
    {
        $availabilities = $this->getAvailabilityService()->getAvailabilitiesForUserAndCalendarType(
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

    public function getCalendarService(): CalendarService
    {
        return $this->calendarService;
    }

    /**
     * @throws \Symfony\Component\Cache\Exception\CacheException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @see \Chamilo\Application\Calendar\Architecture\Interface\CalendarExtensionDataProviderInterface::getCalendars()
     */
    public function getCalendars(User $user): array
    {
        $calendarService = $this->getCalendarService();

        if (!$calendarService->isConfigured() || !$calendarService->isAuthenticated($user)) {
            return [];
        }

        return $calendarService->getOwnedCalendars($user);
    }

    public function getEventParser(): EventParser
    {
        return $this->eventParser;
    }

    /**
     * @return array|\Chamilo\Libraries\Calendar\Architecture\Domain\Event[]
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \DateInvalidTimeZoneException
     * @throws \Symfony\Component\Cache\Exception\CacheException
     */
    public function getEvents(User $user, $fromDate, $toDate): array
    {
        $calendarService = $this->getCalendarService();

        if (!$calendarService->isConfigured() || !$calendarService->isAuthenticated($user)) {
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
        return $this->getTranslator()->trans('TypeName', [], Manager::CONTEXT);
    }

    public function getTranslator(): Translator
    {
        return $this->translator;
    }
}