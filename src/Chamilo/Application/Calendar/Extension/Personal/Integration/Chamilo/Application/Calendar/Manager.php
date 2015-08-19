<?php
namespace Chamilo\Application\Calendar\Extension\Personal\Integration\Chamilo\Application\Calendar;

use Chamilo\Application\Calendar\CalendarInterface;
use Chamilo\Application\Calendar\Extension\Personal\Integration\Chamilo\Libraries\Calendar\Event\EventParser;
use Chamilo\Application\Calendar\Extension\Personal\Storage\DataClass\Publication;
use Chamilo\Application\Calendar\Extension\Personal\Storage\DataClass\PublicationGroup;
use Chamilo\Application\Calendar\Extension\Personal\Storage\DataClass\PublicationUser;
use Chamilo\Application\Calendar\Extension\Personal\Storage\DataManager;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\Condition\NotCondition;
use Chamilo\Libraries\Storage\Query\Condition\OrCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Application\Calendar\Storage\DataClass\AvailableCalendar;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Application\Calendar\Service\AvailabilityService;
use Chamilo\Application\Calendar\Repository\AvailabilityRepository;
use Chamilo\Application\Calendar\Storage\DataClass\Availability;
use Chamilo\Core\User\Storage\DataClass\User;

/**
 *
 * @package Chamilo\Application\Calendar\Extension\Personal\Integration\Chamilo\Application\Calendar
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class Manager implements CalendarInterface
{

    /**
     *
     * @see \Chamilo\Application\Calendar\CalendarInterface::getEvents()
     */
    public function getEvents(\Chamilo\Libraries\Calendar\Renderer\Renderer $renderer, $fromDate, $toDate)
    {
        $availabilityService = new AvailabilityService(new AvailabilityRepository());
        $package = ClassnameUtilities :: getInstance()->getNamespaceParent(__NAMESPACE__, 4);

        if ($availabilityService->isAvailableForUserAndCalendarTypeAndCalendarIdentifier(
            $renderer->getDataProvider()->getDataUser(),
            $package,
            'personal'))
        {
            $userEvents = $this->getUserEvents($renderer, $fromDate, $toDate);
        }
        else
        {
            $userEvents = array();
        }

        if ($availabilityService->isAvailableForUserAndCalendarTypeAndCalendarIdentifier(
            $renderer->getDataProvider()->getDataUser(),
            $package,
            'shared'))
        {
            $sharedEvents = $this->getSharedEvents($renderer, $fromDate, $toDate);
        }
        else
        {
            $sharedEvents = array();
        }

        return array_merge($userEvents, $sharedEvents);
    }

    /**
     *
     * @param \Chamilo\Libraries\Calendar\Renderer\Renderer $renderer
     * @param integer $fromDate
     * @param integer $toDate
     */
    public function getUserEvents(\Chamilo\Libraries\Calendar\Renderer\Renderer $renderer, $fromDate, $toDate)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(Publication :: class_name(), Publication :: PROPERTY_PUBLISHER),
            new StaticConditionVariable($renderer->getDataProvider()->getDataUser()->getId()));
        $publications = DataManager :: retrieves(
            Publication :: class_name(),
            new DataClassRetrievesParameters($condition));

        return $this->renderEvents($renderer, $publications, $fromDate, $toDate);
    }

    /**
     *
     * @param \Chamilo\Libraries\Calendar\Renderer\Renderer $renderer
     * @param integer $fromDate
     * @param integer $toDate
     */
    public function getSharedEvents(\Chamilo\Libraries\Calendar\Renderer\Renderer $renderer, $fromDate, $toDate)
    {
        $events = array();
        $user_groups = $renderer->getDataProvider()->getDataUser()->get_groups(true);

        $conditions = array();
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(PublicationUser :: class_name(), PublicationUser :: PROPERTY_USER),
            new StaticConditionVariable($renderer->getDataProvider()->getDataUser()->getId()));

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
                new StaticConditionVariable($renderer->getDataProvider()->getDataUser()->getId())));

        $condition = new AndCondition($share_condition, $publisher_condition);
        $publications = Datamanager :: retrieve_shared_personal_calendar_publications($condition);

        return $this->renderEvents($renderer, $publications, $fromDate, $toDate);
    }

    /**
     *
     * @param \Chamilo\Libraries\Calendar\Renderer\Renderer $renderer
     * @param \Chamilo\Application\Calendar\Extension\Personal\Storage\DataClass\Publication[] $publications
     * @param integer $fromDate
     * @param integer $toDate
     */
    private function renderEvents(\Chamilo\Libraries\Calendar\Renderer\Renderer $renderer, $publications, $fromDate,
        $toDate)
    {
        $events = array();

        while ($publication = $publications->next_result())
        {
            $eventParser = new EventParser($renderer, $publication, $fromDate, $toDate);
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