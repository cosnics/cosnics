<?php
namespace Chamilo\Application\Calendar\Extension\Office365\Integration\Chamilo\Application\Calendar\Service;

use Chamilo\Application\Calendar\Architecture\ExternalCalendar;
use Chamilo\Application\Calendar\Extension\Office365\Integration\Chamilo\Libraries\Calendar\Event\EventParser;
use Chamilo\Application\Calendar\Service\AvailabilityService;
use Chamilo\Application\Calendar\Storage\DataClass\AvailableCalendar;
use Chamilo\Configuration\Service\ConfigurationConsulter;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Traits\DependencyInjectionContainerTrait;
use Chamilo\Libraries\Calendar\Service\CalendarRendererProvider;
use Chamilo\Libraries\File\ConfigurablePathBuilder;
use Chamilo\Libraries\Protocol\Microsoft\Graph\Exception\AzureUserNotExistsException;
use Chamilo\Libraries\Protocol\Microsoft\Graph\Service\CalendarService;
use Exception;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;

/**
 * @package Chamilo\Application\Calendar\Extension\Personal\Integration\Chamilo\Application\Calendar
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class CalendarEventDataProvider extends ExternalCalendar
{
    public const CALENDAR_EVENT_DATA_PROVIDER_TYPE = 'Chamilo\Application\Calendar\Extension\Office365';

    use DependencyInjectionContainerTrait;

    private FilesystemAdapter $filesystemAdapter;

    /**
     * @throws \Exception
     */
    public function __construct()
    {
        $this->initializeContainer();
    }

    protected function getAvailabilityService(): AvailabilityService
    {
        return $this->getService(AvailabilityService::class);
    }

    protected function getCalendarByIdentifier(string $calendarIdentifier, User $user): AvailableCalendar
    {
        $availableCalendar = new AvailableCalendar();

        try
        {
            $calendar = $this->getCalendarService()->getCalendarByIdentifier($calendarIdentifier, $user);
            $availableCalendar->setType(self::CALENDAR_EVENT_DATA_PROVIDER_TYPE);
            $availableCalendar->setIdentifier($calendar->getId());
            $availableCalendar->setName($calendar->getName());
        }
        catch (Exception $exception)
        {
            $availableCalendar->setIdentifier($calendarIdentifier);
            $availableCalendar->setName('NOT FOUND');
        }

        return $availableCalendar;
    }

    /**
     * @return \Chamilo\Libraries\Calendar\Event\Event[]
     * @throws \Chamilo\Libraries\Protocol\Microsoft\Graph\Exception\AzureUserNotExistsException
     */
    protected function getCalendarEvents(string $calendarIdentifier, User $user, int $fromDate, int $toDate): array
    {
        $office365CalenderEvents = $this->getCalendarService()->findEventsForCalendarIdentifierAndBetweenDates(
            $calendarIdentifier, $user, $fromDate, $toDate
        );

        $availableCalendar = $this->getCalendarByIdentifier($calendarIdentifier, $user);
        $events = [];

        foreach ($office365CalenderEvents as $office365CalenderEvent)
        {
            $eventParser = new EventParser($availableCalendar, $office365CalenderEvent, $fromDate, $toDate);
            $events = array_merge($events, $eventParser->getEvents());
        }

        return $events;
    }

    protected function getCalendarIdentifiers(CalendarRendererProvider $calendarRendererProvider): array
    {
        $availabilities = $this->getAvailabilityService()->getAvailabilitiesForUserAndCalendarType(
            $calendarRendererProvider->getDataUser(), self::CALENDAR_EVENT_DATA_PROVIDER_TYPE
        );

        $calendarIdentifiers = [];

        if ($availabilities->count() == 0)
        {
            $availableCalendars = $this->getCalendars($calendarRendererProvider->getDataUser());

            foreach ($availableCalendars as $availableCalendar)
            {
                $calendarIdentifiers[] = $availableCalendar->getIdentifier();
            }
        }
        else
        {
            foreach ($availabilities as $availability)
            {
                if ($availability->isActive())
                {
                    $calendarIdentifiers[] = $availability->getCalendarId();
                }
            }
        }

        return $calendarIdentifiers;
    }

    protected function getCalendarService(): CalendarService
    {
        return $this->getService(CalendarService::class);
    }

    /**
     * @return \Chamilo\Application\Calendar\Storage\DataClass\AvailableCalendar[]
     */
    public function getCalendars(?User $user = null): array
    {
        $filesystemAdapter = $this->getFilesystemAdapter();

        try
        {
            $identifier = [__METHOD__, $user->getId()];
            $identifierString = md5(serialize($identifier));

            $cacheItem = $filesystemAdapter->getItem($identifierString);

            if (!$cacheItem->isHit())
            {
                try
                {
                    $availableCalendars = [];
                    $ownedCalendars = $this->getCalendarService()->listOwnedCalendars($user);

                    foreach ($ownedCalendars as $calendarItem)
                    {
                        $availableCalendar = new AvailableCalendar();

                        $availableCalendar->setType(self::CALENDAR_EVENT_DATA_PROVIDER_TYPE);
                        $availableCalendar->setIdentifier($calendarItem->getId());
                        $availableCalendar->setName($calendarItem->getName());

                        $availableCalendars[] = $availableCalendar;
                    }
                }
                catch (Exception $exception)
                {
                    $availableCalendars = [];
                }

                $cacheItem->set($availableCalendars);
                $filesystemAdapter->save($cacheItem);
            }

            return $cacheItem->get();
        }
        catch (InvalidArgumentException $e)
        {
            return [];
        }
    }

    protected function getConfigurablePathBuilder(): ConfigurablePathBuilder
    {
        return $this->getService(ConfigurablePathBuilder::class);
    }

    protected function getConfigurationConsulter(): ConfigurationConsulter
    {
        return $this->getService(ConfigurationConsulter::class);
    }

    /**
     * @return \Chamilo\Libraries\Calendar\Event\Event[]
     */
    public function getEvents(CalendarRendererProvider $calendarRendererProvider, int $fromDate, int $toDate): array
    {
        $filesystemAdapter = $this->getFilesystemAdapter();

        try
        {
            $calendarIdentifiers = $this->getCalendarIdentifiers($calendarRendererProvider);

            $identifier = [
                __METHOD__,
                $calendarRendererProvider->getDataUser()->getId(),
                $calendarIdentifiers,
                $fromDate,
                $toDate
            ];

            $cacheItem = $filesystemAdapter->getItem($identifier);

            if (!$cacheItem->isHit())
            {
                $events = [];

                foreach ($calendarIdentifiers as $calendarIdentifier)
                {
                    $events = array_merge(
                        $events, $this->getCalendarEvents(
                        $calendarIdentifier, $calendarRendererProvider->getDataUser(), $fromDate, $toDate
                    )
                    );
                }

                $cacheItem->set($events);
                $filesystemAdapter->save($cacheItem);
            }

            return $cacheItem->get();
        }
        catch (InvalidArgumentException|AzureUserNotExistsException $e)
        {
            return [];
        }
    }

    protected function getFilesystemAdapter(): FilesystemAdapter
    {
        if (!isset($this->filesystemAdapter))
        {
            $this->filesystemAdapter = new FilesystemAdapter(
                md5('Chamilo\Application\Calendar\Extension\Office365'), $this->getRefreshExternalInSeconds(),
                $this->getConfigurablePathBuilder()->getConfiguredCachePath()
            );
        }

        return $this->filesystemAdapter;
    }

    /**
     * @return int
     */
    protected function getRefreshExternalInSeconds()
    {
        $refreshExternal = $this->getConfigurationConsulter()->getSetting(
            ['Chamilo\Libraries\Calendar', 'refresh_external']
        );

        return $refreshExternal * 60;
    }
}