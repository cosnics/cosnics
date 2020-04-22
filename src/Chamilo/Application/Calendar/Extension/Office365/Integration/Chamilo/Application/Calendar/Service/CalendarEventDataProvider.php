<?php
namespace Chamilo\Application\Calendar\Extension\Office365\Integration\Chamilo\Application\Calendar\Service;

use Chamilo\Application\Calendar\Architecture\ExternalCalendar;
use Chamilo\Application\Calendar\Extension\Office365\Integration\Chamilo\Libraries\Calendar\Event\EventParser;
use Chamilo\Application\Calendar\Service\AvailabilityService;
use Chamilo\Application\Calendar\Storage\DataClass\AvailableCalendar;
use Chamilo\Configuration\Service\ConfigurationConsulter;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Traits\DependencyInjectionContainerTrait;
use Chamilo\Libraries\Cache\Doctrine\Provider\FilesystemCache;
use Chamilo\Libraries\Calendar\Renderer\Service\CalendarRendererProvider;
use Chamilo\Libraries\File\ConfigurablePathBuilder;
use Chamilo\Libraries\Protocol\Microsoft\Graph\Service\CalendarService;
use Exception;

/**
 *
 * @package Chamilo\Application\Calendar\Extension\Personal\Integration\Chamilo\Application\Calendar
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class CalendarEventDataProvider extends ExternalCalendar
{
    const CALENDAR_EVENT_DATA_PROVIDER_TYPE = 'Chamilo\Application\Calendar\Extension\Office365';

    use DependencyInjectionContainerTrait;

    /**
     *
     * @var \Chamilo\Libraries\Cache\Doctrine\Provider\FilesystemCache
     */
    private $filesystemCache;

    public function __construct()
    {
        $this->initializeContainer();
    }

    /**
     *
     * @see \Chamilo\Application\Calendar\CalendarInterface::getEvents()
     */
    public function getEvents(
        CalendarRendererProvider $calendarRendererProvider,
        $requestedSourceType, $fromDate, $toDate)
    {
        try
        {
            $events = array();

            $calendarIdentifiers = $this->getCalendarIdentifiers($calendarRendererProvider);

            $identifier = [
                __METHOD__,
                $calendarRendererProvider->getDataUser()->getId(),
                $calendarIdentifiers,
                $requestedSourceType,
                $fromDate,
                $toDate];

            $identifierString = md5(serialize($identifier));

            $filesystemCache = $this->getFilesystemCache();

            if (! $filesystemCache->contains($identifierString))
            {
                foreach ($calendarIdentifiers as $calendarIdentifier)
                {
                    $events = array_merge(
                        $events,
                        $this->getCalendarEvents(
                            $calendarIdentifier,
                            $calendarRendererProvider->getDataUser(),
                            $fromDate,
                            $toDate));
                }

                $filesystemCache->save($identifierString, $events, $this->getRefreshExternalInSeconds());
            }

            return $filesystemCache->fetch($identifierString);
        }
        catch (Exception $exception)
        {
            return [];
        }
    }

    /**
     *
     * @return \Chamilo\Application\Calendar\Service\AvailabilityService
     */
    protected function getAvailabilityService()
    {
        return $this->getService(AvailabilityService::class);
    }

    /**
     *
     * @param \Chamilo\Libraries\Calendar\Renderer\Service\CalendarRendererProvider $calendarRendererProvider
     * @return string[]
     */
    protected function getCalendarIdentifiers(
        CalendarRendererProvider $calendarRendererProvider)
    {
        $availabilities = $this->getAvailabilityService()->getAvailabilitiesForUserAndCalendarType(
            $calendarRendererProvider->getDataUser(),
            self::CALENDAR_EVENT_DATA_PROVIDER_TYPE);

        $calendarIdentifiers = array();

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

    /**
     *
     * @param string $calendarIdentifier
     * @param integer $fromDate
     * @param integer $toDate
     *
     * @return \Chamilo\Libraries\Calendar\Event\Event[]
     */
    protected function getCalendarEvents($calendarIdentifier, User $user, $fromDate, $toDate)
    {
        $office365CalenderEvents = $this->getCalendarService()->findEventsForCalendarIdentifierAndBetweenDates(
            $calendarIdentifier,
            $user,
            $fromDate,
            $toDate);

        $availableCalendar = $this->getCalendarByIdentifier($calendarIdentifier, $user);
        $events = array();

        foreach ($office365CalenderEvents as $office365CalenderEvent)
        {
            $eventParser = new EventParser($availableCalendar, $office365CalenderEvent, $fromDate, $toDate);
            $events = array_merge($events, $eventParser->getEvents());
        }

        return $events;
    }

    protected function getCalendarByIdentifier($calendarIdentifier, User $user)
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
     *
     * @see \Chamilo\Application\Calendar\Architecture\CalendarInterface::getCalendars()
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User|null $user
     *
     * @return \Chamilo\Application\Calendar\Storage\DataClass\AvailableCalendar[]|false|mixed
     */
    public function getCalendars(User $user = null)
    {
        $identifier = [__METHOD__, $user->getId()];
        $identifierString = md5(serialize($identifier));

        $filesystemCache = $this->getFilesystemCache();

        if (! $filesystemCache->contains($identifierString))
        {
            try
            {
                $availableCalendars = array();
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

            $filesystemCache->save($identifierString, $availableCalendars, $this->getRefreshExternalInSeconds());
        }

        return $filesystemCache->fetch($identifierString);
    }

    /**
     *
     * @return integer
     */
    protected function getRefreshExternalInSeconds()
    {
        $refreshExternal = $this->getConfigurationConsulter()->getSetting(
            ['Chamilo\Libraries\Calendar', 'refresh_external']);
        return $refreshExternal * 60;
    }

    /**
     *
     * @return \Chamilo\Libraries\Cache\Doctrine\Provider\FilesystemCache
     */
    protected function getFilesystemCache()
    {
        if (! isset($this->filesystemCache))
        {
            $this->filesystemCache = new FilesystemCache(
                $this->getConfigurablePathBuilder()->getCachePath('Chamilo\Application\Calendar\Extension\Office365'));
        }

        return $this->filesystemCache;
    }

    /**
     *
     * @return \Chamilo\Libraries\Protocol\Microsoft\Graph\Service\CalendarService
     */
    protected function getCalendarService()
    {
        return $this->getService(CalendarService::class);
    }

    /**
     *
     * @return \Chamilo\Configuration\Service\ConfigurationConsulter
     */
    protected function getConfigurationConsulter()
    {
        return $this->getService(ConfigurationConsulter::class);
    }

    /**
     *
     * @return \Chamilo\Libraries\File\ConfigurablePathBuilder
     */
    protected function getConfigurablePathBuilder()
    {
        return $this->getService(ConfigurablePathBuilder::class);
    }
}