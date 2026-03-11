<?php
namespace Chamilo\Application\Calendar\Extension\Office365\Implementation\Calendar;

use Chamilo\Application\Calendar\Architecture\Domain\AvailableCalendar;
use Chamilo\Application\Calendar\Architecture\Interface\CalendarExtensionDataProviderInterface;
use Chamilo\Application\Calendar\Extension\Office365\Manager;
use Chamilo\Application\Calendar\Extension\Office365\Service\EventParser;
use Chamilo\Application\Calendar\Service\AvailabilityService;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Protocol\Microsoft\Graph\Service\CalendarService;
use Exception;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Application\Calendar\Extension\Office365\Service
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class CalendarExtensionDataProvider implements CalendarExtensionDataProviderInterface
{
    private AvailabilityService $availabilityService;

    private CalendarService $calendarService;

    private EventParser $eventParser;

    private FilesystemAdapter $filesystemAdapter;

    private Translator $translator;

    public function __construct(
        FilesystemAdapter $filesystemAdapter, AvailabilityService $availabilityService,
        CalendarService $calendarService, EventParser $eventParser, Translator $translator
    )
    {
        $this->filesystemAdapter = $filesystemAdapter;
        $this->availabilityService = $availabilityService;
        $this->calendarService = $calendarService;
        $this->eventParser = $eventParser;
        $this->translator = $translator;
    }

    protected function getAvailabilityService(): AvailabilityService
    {
        return $this->availabilityService;
    }

    protected function getCalendarByIdentifier(string $calendarIdentifier, User $user): AvailableCalendar
    {
        $availableCalendar = new AvailableCalendar();

        try {
            $calendar = $this->getCalendarService()->getCalendarByIdentifier($calendarIdentifier, $user);
            $availableCalendar->setType(Manager::CONTEXT);
            $availableCalendar->setIdentifier($calendar->getId());
            $availableCalendar->setName($calendar->getName());
        }
        catch (Exception) {
            $availableCalendar->setIdentifier($calendarIdentifier);
            $availableCalendar->setName('NOT FOUND');
        }

        return $availableCalendar;
    }

    /**
     * @return \Chamilo\Libraries\Calendar\Architecture\Domain\Event[]
     */
    protected function getCalendarEvents(string $calendarIdentifier, User $user, int $fromDate, int $toDate): array
    {
        try {
            $events = $this->getCalendarService()->findEventsForCalendarIdentifierAndBetweenDates(
                $user, $calendarIdentifier, $fromDate, $toDate
            );

            $availableCalendar = $this->getCalendarByIdentifier($calendarIdentifier, $user);
            $parsedEvents = [];

            foreach ($events as $event) {
                $parsedEvents =
                    array_merge($parsedEvents, $this->getEventParser()->getEvents($availableCalendar, $event));
            }

            return $parsedEvents;
        }
        catch (Exception) {
            return [];
        }
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    protected function getCalendarIdentifiers(User $user): array
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

    protected function getCalendarService(): CalendarService
    {
        return $this->calendarService;
    }

    /**
     * @return \Chamilo\Application\Calendar\Architecture\Domain\AvailableCalendar[]
     */
    public function getCalendars(?User $user = null): array
    {
        $filesystemAdapter = $this->getFilesystemAdapter();

        try {
            $identifier = [__METHOD__, $user->getId()];
            $identifierString = md5(serialize($identifier));

            $cacheItem = $filesystemAdapter->getItem($identifierString);

            if (!$cacheItem->isHit()) {
                try {
                    $availableCalendars = [];

                    $ownedCalendars = $this->getCalendarService()->listOwnedCalendars($user);

                    foreach ($ownedCalendars as $ownedCalendar) {
                        $availableCalendar = new AvailableCalendar();

                        $availableCalendar->setType(Manager::CONTEXT);
                        $availableCalendar->setIdentifier($ownedCalendar->getId());
                        $availableCalendar->setName($ownedCalendar->getName());

                        $availableCalendars[] = $availableCalendar;
                    }
                }
                catch (Exception) {
                    $availableCalendars = [];
                }

                $cacheItem->set($availableCalendars);
                $filesystemAdapter->save($cacheItem);
            }

            return $cacheItem->get();
        }
        catch (InvalidArgumentException) {
            return [];
        }
    }

    public function getEventParser(): EventParser
    {
        return $this->eventParser;
    }

    /**
     * @return \Chamilo\Libraries\Calendar\Architecture\Domain\Event[]
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function getEvents(User $user, int $fromDate, int $toDate): array
    {
        $filesystemAdapter = $this->getFilesystemAdapter();

        try {
            $calendarIdentifiers = $this->getCalendarIdentifiers($user);

            $identifier = [
                __METHOD__,
                $user->getId(),
                $calendarIdentifiers,
                $fromDate,
                $toDate
            ];
            $identifierString = md5(serialize($identifier));

            $cacheItem = $filesystemAdapter->getItem($identifierString);

            if (!$cacheItem->isHit()) {
                $events = [];

                foreach ($calendarIdentifiers as $calendarIdentifier) {
                    $events = array_merge(
                        $events, $this->getCalendarEvents(
                        $calendarIdentifier, $user, $fromDate, $toDate
                    )
                    );
                }

                $cacheItem->set($events);
                $filesystemAdapter->save($cacheItem);
            }

            return $cacheItem->get();
        }
        catch (InvalidArgumentException) {
            return [];
        }
    }

    protected function getFilesystemAdapter(): FilesystemAdapter
    {
        return $this->filesystemAdapter;
    }

    public function getName(): string
    {
        return $this->getTranslator()->trans('TypeName', [], 'Chamilo\Application\Calendar\Extension\Office365');
    }

    public function getTranslator(): Translator
    {
        return $this->translator;
    }
}