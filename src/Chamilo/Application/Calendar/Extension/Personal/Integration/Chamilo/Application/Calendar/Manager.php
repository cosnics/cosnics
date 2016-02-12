<?php
namespace Chamilo\Application\Calendar\Extension\Personal\Integration\Chamilo\Application\Calendar;

use Chamilo\Application\Calendar\Architecture\MixedCalendar;
use Chamilo\Application\Calendar\Extension\Personal\Integration\Chamilo\Libraries\Calendar\Event\EventParser;
use Chamilo\Application\Calendar\Extension\Personal\Storage\DataClass\Publication;
use Chamilo\Application\Calendar\Extension\Personal\Storage\DataClass\PublicationGroup;
use Chamilo\Application\Calendar\Extension\Personal\Storage\DataClass\PublicationUser;
use Chamilo\Application\Calendar\Extension\Personal\Storage\DataManager;
use Chamilo\Application\Calendar\Repository\AvailabilityRepository;
use Chamilo\Application\Calendar\Service\AvailabilityService;
use Chamilo\Application\Calendar\Storage\DataClass\AvailableCalendar;
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
class Manager extends MixedCalendar
{

    /**
     *
     * @see \Chamilo\Application\Calendar\CalendarInterface::getEvents()
     */
    public function getEvents(
        \Chamilo\Libraries\Calendar\Renderer\Service\CalendarRendererProvider $calendarRendererProvider,
        $requestedSourceType, $fromDate, $toDate)
    {
        $availabilityService = new AvailabilityService(new AvailabilityRepository());
        $package = ClassnameUtilities :: getInstance()->getNamespaceParent(__NAMESPACE__, 4);

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
     */
    public function getUserEvents(
        \Chamilo\Libraries\Calendar\Renderer\Service\CalendarRendererProvider $calendarRendererProvider, $fromDate,
        $toDate)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(Publication :: class_name(), Publication :: PROPERTY_PUBLISHER),
            new StaticConditionVariable($calendarRendererProvider->getDataUser()->getId()));
        $publications = DataManager :: retrieves(
            Publication :: class_name(),
            new DataClassRetrievesParameters($condition));

        return $this->renderEvents($calendarRendererProvider, $publications, $fromDate, $toDate);
    }

    /**
     *
     * @param \Chamilo\Libraries\Calendar\Renderer\Service\CalendarRendererProvider $calendarRendererProvider
     * @param integer $fromDate
     * @param integer $toDate
     */
    public function getSharedEvents(
        \Chamilo\Libraries\Calendar\Renderer\Service\CalendarRendererProvider $calendarRendererProvider, $fromDate,
        $toDate)
    {
        $events = array();
        $user_groups = $calendarRendererProvider->getDataUser()->get_groups(true);

        $conditions = array();
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(PublicationUser :: class_name(), PublicationUser :: PROPERTY_USER),
            new StaticConditionVariable($calendarRendererProvider->getDataUser()->getId()));

        if (count($user_groups) > 0)
        {
            $conditions[] = new InCondition(
                new PropertyConditionVariable(PublicationGroup :: class_name(), PublicationGroup :: PROPERTY_GROUP_ID),
                $user_groups);
        }

        $share_condition = new OrCondition($conditions);

        $publisher_condition = new NotCondition(
            new EqualityCondition(
                new PropertyConditionVariable(Publication :: class_name(), Publication :: PROPERTY_PUBLISHER),
                new StaticConditionVariable($calendarRendererProvider->getDataUser()->getId())));

        $condition = new AndCondition($share_condition, $publisher_condition);
        $publications = Datamanager :: retrieve_shared_personal_calendar_publications($condition);

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
        $fromDate, $toDate)
    {
        $events = array();

        while ($publication = $publications->next_result())
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
        $personalCalendar->setType(ClassnameUtilities :: getInstance()->getNamespaceParent(__NAMESPACE__, 4));
        $personalCalendar->setIdentifier('personal');
        $personalCalendar->setName(Translation :: get('PersonalCalendarName'));
        $personalCalendar->setDescription(Translation :: get('PersonalCalendarDescription'));

        $calendars[] = $personalCalendar;

        $personalCalendar = new AvailableCalendar();
        $personalCalendar->setType(ClassnameUtilities :: getInstance()->getNamespaceParent(__NAMESPACE__, 4));
        $personalCalendar->setIdentifier('shared');
        $personalCalendar->setName(Translation :: get('SharedCalendarName'));
        $personalCalendar->setDescription(Translation :: get('SharedCalendarDescription'));

        $calendars[] = $personalCalendar;

        return $calendars;
    }
}