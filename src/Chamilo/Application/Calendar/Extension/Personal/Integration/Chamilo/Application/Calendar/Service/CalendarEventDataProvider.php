<?php
namespace Chamilo\Application\Calendar\Extension\Personal\Integration\Chamilo\Application\Calendar\Service;

use Chamilo\Application\Calendar\Architecture\MixedCalendar;
use Chamilo\Application\Calendar\Extension\Personal\Integration\Chamilo\Application\Calendar\Interfaces\PersonalCalendarEventDataProviderRepositoryInterface;
use Chamilo\Application\Calendar\Extension\Personal\Integration\Chamilo\Application\Calendar\Repository\CalendarEventDataProviderRepository;
use Chamilo\Application\Calendar\Extension\Personal\Integration\Chamilo\Libraries\Calendar\Event\EventParser;
use Chamilo\Application\Calendar\Extension\Personal\Manager;
use Chamilo\Application\Calendar\Service\AvailabilityService;
use Chamilo\Application\Calendar\Storage\DataClass\AvailableCalendar;
use Chamilo\Configuration\Storage\DataClass\Registration;
use Chamilo\Core\Repository\Publication\Storage\Repository\PublicationRepository;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Calendar\Service\CalendarRendererProvider;
use Chamilo\Libraries\DependencyInjection\Traits\DependencyInjectionContainerTrait;
use Chamilo\Libraries\Storage\Parameters\RecordRetrievesParameters;

/**
 * @package Chamilo\Application\Calendar\Extension\Personal\Integration\Chamilo\Application\Calendar
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class CalendarEventDataProvider extends MixedCalendar
{
    use DependencyInjectionContainerTrait;

    protected function getAvailabilityService(): AvailabilityService
    {
        return $this->getService(AvailabilityService::class);
    }

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User|null $user
     *
     * @return array|\Chamilo\Application\Calendar\Storage\DataClass\AvailableCalendar[]
     * @see \Chamilo\Application\Calendar\Architecture\CalendarInterface::getCalendars()
     */
    public function getCalendars(User $user = null): array
    {
        $translator = $this->getTranslator();

        $calendars = [];

        $personalCalendar = new AvailableCalendar();
        $personalCalendar->setType(ClassnameUtilities::getInstance()->getNamespaceParent(__NAMESPACE__, 4));
        $personalCalendar->setIdentifier('personal');
        $personalCalendar->setName($translator->trans('PersonalCalendarName', [], Manager::CONTEXT));
        $personalCalendar->setDescription($translator->trans('PersonalCalendarDescription', [], Manager::CONTEXT));

        $calendars[] = $personalCalendar;

        $personalCalendar = new AvailableCalendar();
        $personalCalendar->setType(ClassnameUtilities::getInstance()->getNamespaceParent(__NAMESPACE__, 4));
        $personalCalendar->setIdentifier('shared');
        $personalCalendar->setName($translator->trans('SharedCalendarName', [], Manager::CONTEXT));
        $personalCalendar->setDescription($translator->trans('SharedCalendarDescription', [], Manager::CONTEXT));

        $calendars[] = $personalCalendar;

        return $calendars;
    }

    /**
     * @throws \Symfony\Component\Cache\Exception\CacheException
     */
    public function getEvents(CalendarRendererProvider $calendarRendererProvider, $fromDate, $toDate): array
    {
        $package = ClassnameUtilities::getInstance()->getNamespaceParent(__NAMESPACE__, 5);
        $availabilityService = $this->getAvailabilityService();

        if ($availabilityService->isAvailableForUserAndCalendarTypeAndCalendarIdentifier(
            $calendarRendererProvider->getDataUser(), $package, 'personal'
        ))
        {
            $userEvents = $this->getUserEvents($calendarRendererProvider, $fromDate, $toDate);
        }
        else
        {
            $userEvents = [];
        }

        if ($availabilityService->isAvailableForUserAndCalendarTypeAndCalendarIdentifier(
            $calendarRendererProvider->getDataUser(), $package, 'shared'
        ))
        {
            $sharedEvents = $this->getSharedEvents($calendarRendererProvider, $fromDate, $toDate);
        }
        else
        {
            $sharedEvents = [];
        }

        return array_merge($userEvents, $sharedEvents);
    }

    /**
     * @param \Chamilo\Libraries\Calendar\Service\CalendarRendererProvider $calendarRendererProvider
     * @param RecordRetrievesParameters $recordRetrievesParameters
     * @param int $fromDate
     * @param int $toDate
     *
     * @return array
     * @throws \Symfony\Component\Cache\Exception\CacheException
     */
    protected function getEventsByParameters(
        CalendarRendererProvider $calendarRendererProvider, RecordRetrievesParameters $recordRetrievesParameters,
        int $fromDate, int $toDate
    ): array
    {
        $publications = [];

        $registrations = $this->getRegistrationConsulter()->getIntegrationRegistrations(
            Manager::CONTEXT
        );

        $publicationRepository = $this->getPublicationRepository();

        foreach ($registrations as $registration)
        {
            if ($registration[Registration::PROPERTY_STATUS])
            {
                $context = $registration[Registration::PROPERTY_CONTEXT];
                $class_name = $context . '\Repository\PersonalCalendarEventDataProviderRepository';

                if (class_exists($class_name))
                {
                    $source = new $class_name($publicationRepository);

                    if ($source instanceof PersonalCalendarEventDataProviderRepositoryInterface)
                    {
                        $publications = array_merge(
                            $publications, $source->getPublications($recordRetrievesParameters, $fromDate, $toDate)
                        );
                    }
                }
            }
        }

        return $this->renderEvents($calendarRendererProvider, $publications, $fromDate, $toDate);
    }

    protected function getPublicationRepository(): PublicationRepository
    {
        return $this->getService(PublicationRepository::class);
    }

    /**
     * @throws \Symfony\Component\Cache\Exception\CacheException
     */
    public function getSharedEvents(
        CalendarRendererProvider $calendarRendererProvider, int $fromDate, int $toDate
    ): array
    {
        $repository = new CalendarEventDataProviderRepository();
        $recordRetrievesParameters = $repository->getSharedPublicationsRecordRetrievesParameters(
            $calendarRendererProvider->getDataUser()
        );

        return $this->getEventsByParameters($calendarRendererProvider, $recordRetrievesParameters, $fromDate, $toDate);
    }

    /**
     * @throws \Symfony\Component\Cache\Exception\CacheException
     */
    public function getUserEvents(
        CalendarRendererProvider $calendarRendererProvider, int $fromDate, int $toDate
    ): array
    {
        $repository = new CalendarEventDataProviderRepository();
        $dataClassRetrievesParameters = $repository->getPublicationsRecordRetrievesParameters(
            $calendarRendererProvider->getDataUser()
        );

        return $this->getEventsByParameters(
            $calendarRendererProvider, $dataClassRetrievesParameters, $fromDate, $toDate
        );
    }

    private function renderEvents(
        CalendarRendererProvider $calendarRendererProvider, array $publications, int $fromDate, int $toDate
    ): array
    {
        $events = [];

        foreach ($publications as $publication)
        {
            $eventParser = new EventParser($calendarRendererProvider, $publication, $fromDate, $toDate);
            $events = array_merge($events, $eventParser->getEvents());
        }

        return $events;
    }
}