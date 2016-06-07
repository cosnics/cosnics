<?php
namespace Chamilo\Application\Calendar\Extension\Personal\Integration\Chamilo\Application\Calendar\Service;

use Chamilo\Application\Calendar\Architecture\MixedCalendar;
use Chamilo\Application\Calendar\Extension\Personal\Integration\Chamilo\Application\Calendar\Interfaces\PersonalCalendarEventDataProviderRepositoryInterface;
use Chamilo\Application\Calendar\Extension\Personal\Integration\Chamilo\Application\Calendar\Repository\CalendarEventDataProviderRepository;
use Chamilo\Application\Calendar\Extension\Personal\Integration\Chamilo\Libraries\Calendar\Event\EventParser;
use Chamilo\Application\Calendar\Extension\Personal\Storage\DataClass\Publication;
use Chamilo\Application\Calendar\Extension\Personal\Storage\DataClass\PublicationGroup;
use Chamilo\Application\Calendar\Extension\Personal\Storage\DataClass\PublicationUser;
use Chamilo\Application\Calendar\Extension\Personal\Storage\DataManager;
use Chamilo\Application\Calendar\Repository\AvailabilityRepository;
use Chamilo\Application\Calendar\Service\AvailabilityService;
use Chamilo\Application\Calendar\Storage\DataClass\AvailableCalendar;
use Chamilo\Configuration\Configuration;
use Chamilo\Configuration\Storage\DataClass\Registration;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\Condition\NotCondition;
use Chamilo\Libraries\Storage\Query\Condition\OrCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 *
 * @package Chamilo\Application\Calendar\Extension\Personal\Integration\Chamilo\Application\Calendar
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class CalendarEventDataProvider extends MixedCalendar
{

    /**
     *
     * @see \Chamilo\Application\Calendar\CalendarInterface::getEvents()
     */
    public function getEvents(
        \Chamilo\Libraries\Calendar\Renderer\Service\CalendarRendererProvider $calendarRendererProvider,
        $requestedSourceType, $fromDate, $toDate
    )
    {
        $availabilityService = new AvailabilityService(new AvailabilityRepository());
        $package = ClassnameUtilities:: getInstance()->getNamespaceParent(__NAMESPACE__, 4);

        if ($availabilityService->isAvailableForUserAndCalendarTypeAndCalendarIdentifier(
            $calendarRendererProvider->getDataUser(),
            $package,
            'personal'
        )
        )
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
            'shared'
        )
        )
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
     */
    public function getUserEvents(
        \Chamilo\Libraries\Calendar\Renderer\Service\CalendarRendererProvider $calendarRendererProvider, $fromDate,
        $toDate
    )
    {
        $repository = new CalendarEventDataProviderRepository();
        $dataClassRetrievesParameters = $repository->getPublicationsDataClassRetrievesParameters(
            $calendarRendererProvider->getDataUser()
        );

        return $this->getEventsByParameters(
            $calendarRendererProvider, $dataClassRetrievesParameters, $fromDate, $toDate
        );
    }

    /**
     *
     * @param \Chamilo\Libraries\Calendar\Renderer\Service\CalendarRendererProvider $calendarRendererProvider
     * @param integer $fromDate
     * @param integer $toDate
     */
    public function getSharedEvents(
        \Chamilo\Libraries\Calendar\Renderer\Service\CalendarRendererProvider $calendarRendererProvider, $fromDate,
        $toDate
    )
    {
        $repository = new CalendarEventDataProviderRepository();
        $dataClassRetrievesParameters = $repository->getSharedPublicatiosnDataClassRetrievesParameters(
            $calendarRendererProvider->getDataUser()
        );

        return $this->getEventsByParameters(
            $calendarRendererProvider, $dataClassRetrievesParameters, $fromDate, $toDate
        );
    }

    /**
     * @param \Chamilo\Libraries\Calendar\Renderer\Service\CalendarRendererProvider $calendarRendererProvider
     * @param $fromDate
     * @param $toDate
     *
     * @return array
     */
    protected function getEventsByParameters(
        \Chamilo\Libraries\Calendar\Renderer\Service\CalendarRendererProvider $calendarRendererProvider,
        DataClassRetrievesParameters $dataClassRetrievesParameters, $fromDate, $toDate
    )
    {
        $publications = array();

        $registrations = Configuration:: get_instance()->getIntegrationRegistrations(
            \Chamilo\Application\Calendar\Extension\Personal\Manager:: package()
        );

        foreach ($registrations as $registration)
        {
            if ($registration[Registration :: PROPERTY_STATUS])
            {
                $context = $registration[Registration :: PROPERTY_CONTEXT];
                $class_name = $context . '\Repository\CalendarEventDataProviderRepository';

                if (class_exists($class_name))
                {
                    $source = new $class_name();

                    if($source instanceof PersonalCalendarEventDataProviderRepositoryInterface)
                    {
                        $publications = array_merge(
                            $publications, $source->getPublications($dataClassRetrievesParameters, $fromDate, $toDate)
                        );
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
     */
    private function renderEvents(
        \Chamilo\Libraries\Calendar\Renderer\Service\CalendarRendererProvider $calendarRendererProvider, $publications,
        $fromDate, $toDate
    )
    {
        $events = array();

        foreach($publications as $publication)
        {
            $eventParser = new EventParser($calendarRendererProvider, $publication, $fromDate, $toDate);
            $events = array_merge($events, $eventParser->getEvents());
        }

        return $events;
    }

    /**
     *
     * @return \Chamilo\Application\Calendar\Storage\DataClass\AvailableCalendar[]
     */
    public function getCalendars()
    {
        $calendars = array();

        $personalCalendar = new AvailableCalendar();
        $personalCalendar->setType(ClassnameUtilities:: getInstance()->getNamespaceParent(__NAMESPACE__, 4));
        $personalCalendar->setIdentifier('personal');
        $personalCalendar->setName(Translation:: get('PersonalCalendarName'));
        $personalCalendar->setDescription(Translation:: get('PersonalCalendarDescription'));

        $calendars[] = $personalCalendar;

        $personalCalendar = new AvailableCalendar();
        $personalCalendar->setType(ClassnameUtilities:: getInstance()->getNamespaceParent(__NAMESPACE__, 4));
        $personalCalendar->setIdentifier('shared');
        $personalCalendar->setName(Translation:: get('SharedCalendarName'));
        $personalCalendar->setDescription(Translation:: get('SharedCalendarDescription'));

        $calendars[] = $personalCalendar;

        return $calendars;
    }

}