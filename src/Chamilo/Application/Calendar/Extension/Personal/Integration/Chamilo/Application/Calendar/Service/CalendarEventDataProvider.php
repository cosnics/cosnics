<?php
namespace Chamilo\Application\Calendar\Extension\Personal\Integration\Chamilo\Application\Calendar\Service;

use Chamilo\Application\Calendar\Architecture\MixedCalendar;
use Chamilo\Application\Calendar\Extension\Personal\Integration\Chamilo\Application\Calendar\Interfaces\PersonalCalendarEventDataProviderRepositoryInterface;
use Chamilo\Application\Calendar\Extension\Personal\Integration\Chamilo\Application\Calendar\Repository\CalendarEventDataProviderRepository;
use Chamilo\Application\Calendar\Extension\Personal\Integration\Chamilo\Libraries\Calendar\Event\EventParser;
use Chamilo\Application\Calendar\Storage\DataClass\AvailableCalendar;
use Chamilo\Configuration\Configuration;
use Chamilo\Configuration\Storage\DataClass\Registration;
use Chamilo\Core\Repository\Publication\Storage\Repository\PublicationRepository;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Storage\Parameters\RecordRetrievesParameters;
use Chamilo\Libraries\Translation\Translation;

/**
 *
 * @package Chamilo\Application\Calendar\Extension\Personal\Integration\Chamilo\Application\Calendar
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class CalendarEventDataProvider extends MixedCalendar
{
    use \Chamilo\Libraries\Architecture\Traits\DependencyInjectionContainerTrait;

    public function __construct()
    {
        $this->initializeContainer();
    }

    /**
     *
     * @return \Chamilo\Application\Calendar\Service\AvailabilityService
     */
    protected function getAvailabilityService()
    {
        return $this->getService('chamilo.application.calendar.service.availability_service');
    }

    /**
     *
     * @see \Chamilo\Application\Calendar\CalendarInterface::getEvents()
     */
    public function getEvents(
        \Chamilo\Libraries\Calendar\Renderer\Service\CalendarRendererProvider $calendarRendererProvider,
        $requestedSourceType, $fromDate, $toDate)
    {
        $package = ClassnameUtilities::getInstance()->getNamespaceParent(__NAMESPACE__, 5);
        $availabilityService = $this->getAvailabilityService();

        if ($availabilityService->isAvailableForUserAndCalendarTypeAndCalendarIdentifier(
            $calendarRendererProvider->getDataUser(),
            $package,
            'personal'))
        {
            $userEvents = $this->getUserEvents($calendarRendererProvider, $fromDate, $toDate);
        }
        else
        {
            $userEvents = array();
        }

        if ($availabilityService->isAvailableForUserAndCalendarTypeAndCalendarIdentifier(
            $calendarRendererProvider->getDataUser(),
            $package,
            'shared'))
        {
            $sharedEvents = $this->getSharedEvents($calendarRendererProvider, $fromDate, $toDate);
        }
        else
        {
            $sharedEvents = array();
        }

        return array_merge($userEvents, $sharedEvents);
    }

    /**
     *
     * @param \Chamilo\Libraries\Calendar\Renderer\Service\CalendarRendererProvider $calendarRendererProvider
     * @param integer $fromDate
     * @param integer $toDate
     *
     * @return array
     */
    public function getUserEvents(
        \Chamilo\Libraries\Calendar\Renderer\Service\CalendarRendererProvider $calendarRendererProvider, $fromDate,
        $toDate)
    {
        $repository = new CalendarEventDataProviderRepository();
        $dataClassRetrievesParameters = $repository->getPublicationsRecordRetrievesParameters(
            $calendarRendererProvider->getDataUser());

        return $this->getEventsByParameters(
            $calendarRendererProvider,
            $dataClassRetrievesParameters,
            $fromDate,
            $toDate);
    }

    /**
     *
     * @param \Chamilo\Libraries\Calendar\Renderer\Service\CalendarRendererProvider $calendarRendererProvider
     * @param integer $fromDate
     * @param integer $toDate
     *
     * @return array
     */
    public function getSharedEvents(
        \Chamilo\Libraries\Calendar\Renderer\Service\CalendarRendererProvider $calendarRendererProvider, $fromDate,
        $toDate)
    {
        $repository = new CalendarEventDataProviderRepository();
        $recordRetrievesParameters = $repository->getSharedPublicationsRecordRetrievesParameters(
            $calendarRendererProvider->getDataUser());

        return $this->getEventsByParameters($calendarRendererProvider, $recordRetrievesParameters, $fromDate, $toDate);
    }

    /**
     *
     * @param \Chamilo\Libraries\Calendar\Renderer\Service\CalendarRendererProvider $calendarRendererProvider
     * @param RecordRetrievesParameters $recordRetrievesParameters
     * @param int $fromDate
     * @param int $toDate
     *
     * @return array
     */
    protected function getEventsByParameters(
        \Chamilo\Libraries\Calendar\Renderer\Service\CalendarRendererProvider $calendarRendererProvider,
        RecordRetrievesParameters $recordRetrievesParameters, $fromDate, $toDate)
    {
        $publications = array();

        $registrations = Configuration::getInstance()->getIntegrationRegistrations(
            \Chamilo\Application\Calendar\Extension\Personal\Manager::package());

        $publicationRepository = new PublicationRepository();

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
                            $publications,
                            $source->getPublications($recordRetrievesParameters, $fromDate, $toDate));
                    }
                }
            }
        }

        return $this->renderEvents($calendarRendererProvider, $publications, $fromDate, $toDate);
    }

    /**
     *
     * @param \Chamilo\Libraries\Calendar\Renderer\Service\CalendarRendererProvider $calendarRendererProvider
     * @param \Chamilo\Application\Calendar\Extension\Personal\Storage\DataClass\Publication[] $publications
     * @param integer $fromDate
     * @param integer $toDate
     *
     * @return array
     */
    private function renderEvents(
        \Chamilo\Libraries\Calendar\Renderer\Service\CalendarRendererProvider $calendarRendererProvider, $publications,
        $fromDate, $toDate)
    {
        $events = array();

        foreach ($publications as $publication)
        {
            $eventParser = new EventParser($calendarRendererProvider, $publication, $fromDate, $toDate);
            $events = array_merge($events, $eventParser->getEvents());
        }

        return $events;
    }

    /**
     *
     * @see \Chamilo\Application\Calendar\Architecture\CalendarInterface::getCalendars()
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User|null $user
     *
     * @return array|\Chamilo\Application\Calendar\Storage\DataClass\AvailableCalendar[]
     */
    public function getCalendars(User $user = null)
    {
        $calendars = array();

        $personalCalendar = new AvailableCalendar();
        $personalCalendar->setType(ClassnameUtilities::getInstance()->getNamespaceParent(__NAMESPACE__, 4));
        $personalCalendar->setIdentifier('personal');
        $personalCalendar->setName(Translation::get('PersonalCalendarName'));
        $personalCalendar->setDescription(Translation::get('PersonalCalendarDescription'));

        $calendars[] = $personalCalendar;

        $personalCalendar = new AvailableCalendar();
        $personalCalendar->setType(ClassnameUtilities::getInstance()->getNamespaceParent(__NAMESPACE__, 4));
        $personalCalendar->setIdentifier('shared');
        $personalCalendar->setName(Translation::get('SharedCalendarName'));
        $personalCalendar->setDescription(Translation::get('SharedCalendarDescription'));

        $calendars[] = $personalCalendar;

        return $calendars;
    }
}