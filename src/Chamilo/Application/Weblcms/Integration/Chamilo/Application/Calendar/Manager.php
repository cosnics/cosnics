<?php
namespace Chamilo\Application\Weblcms\Integration\Chamilo\Application\Calendar;

use Chamilo\Application\Weblcms\Rights\WeblcmsRights;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Core\Repository\ContentObject\CalendarEvent\Storage\DataClass\CalendarEvent;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\Condition\SubselectCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Application\Weblcms\Integration\Chamilo\Libraries\Calendar\Event\EventParser;
use Chamilo\Application\Calendar\Storage\DataClass\AvailableCalendar;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Application\Calendar\Service\AvailabilityService;
use Chamilo\Application\Calendar\Repository\AvailabilityRepository;
use Chamilo\Application\Calendar\Storage\DataClass\Availability;
use Chamilo\Application\Calendar\Architecture\InternalCalendar;

/**
 *
 * @package Chamilo\Application\Weblcms\Integration\Chamilo\Application\Calendar
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class Manager extends InternalCalendar
{

    /**
     *
     * @see \Chamilo\Application\Calendar\CalendarInterface::getEvents()
     */
    public function getEvents(
        \Chamilo\Libraries\Calendar\Renderer\Service\CalendarRendererProvider $calendarRendererProvider,
        $requestedSourceType, $fromDate, $toDate)
    {
        $events = array();

        $availabilityService = new AvailabilityService(new AvailabilityRepository());
        $packageContext = ClassnameUtilities :: getInstance()->getNamespaceParent(__NAMESPACE__, 4);
        $packageName = ClassnameUtilities :: getInstance()->getPackageNameFromNamespace($packageContext);

        $activeAvailability = $availabilityService->getAvailabilityByUserAndCalendarTypeAndCalendarIdentifier(
            $calendarRendererProvider->getDataUser(),
            $packageContext,
            $packageName);

        if ($activeAvailability instanceof Availability && $activeAvailability->getAvailability() == 1)
        {
            $condition = $this->getConditions($calendarRendererProvider->getDataUser());
            $publications = \Chamilo\Application\Weblcms\Storage\DataManager :: retrieves(
                ContentObjectPublication :: class_name(),
                $condition);

            while ($publication = $publications->next_result())
            {
                if (! WeblcmsRights :: get_instance()->is_allowed_in_courses_subtree(
                    WeblcmsRights :: VIEW_RIGHT,
                    $publication->get_id(),
                    WeblcmsRights :: TYPE_PUBLICATION,
                    $publication->get_course_id()))
                {
                    continue;
                }

                $eventParser = new EventParser($publication, $fromDate, $toDate);
                $events = array_merge($events, $eventParser->getEvents());
            }
        }

        return $events;
    }

    /**
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @return \Chamilo\Libraries\Storage\Query\Condition\AndCondition
     */
    public function getConditions($user)
    {
        $user_courses = \Chamilo\Application\Weblcms\Course\Storage\DataManager :: retrieve_all_courses_from_user($user);
        $course_ids = array();

        while ($course = $user_courses->next_result())
        {
            $course_ids[] = $course->get_id();
        }

        $conditions = array();
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                ContentObjectPublication :: class_name(),
                ContentObjectPublication :: PROPERTY_TOOL),
            new StaticConditionVariable('calendar'));
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                ContentObjectPublication :: class_name(),
                ContentObjectPublication :: PROPERTY_HIDDEN),
            new StaticConditionVariable(0));
        $conditions[] = new InCondition(
            new PropertyConditionVariable(
                ContentObjectPublication :: class_name(),
                ContentObjectPublication :: PROPERTY_COURSE_ID),
            $course_ids);
        $subselect_condition = new EqualityCondition(
            new PropertyConditionVariable(ContentObject :: class_name(), ContentObject :: PROPERTY_TYPE),
            new StaticConditionVariable(CalendarEvent :: class_name()));
        $conditions[] = new SubselectCondition(
            new PropertyConditionVariable(
                ContentObjectPublication :: class_name(),
                ContentObjectPublication :: PROPERTY_CONTENT_OBJECT_ID),
            new PropertyConditionVariable(ContentObject :: class_name(), ContentObject :: PROPERTY_ID),
            ContentObject :: get_table_name(),
            $subselect_condition,
            null,
            \Chamilo\Core\Repository\Storage\DataManager :: get_instance());
        return new AndCondition($conditions);
    }

    /**
     *
     * @return \Chamilo\Application\Calendar\Storage\DataClass\AvailableCalendar[]
     */
    public function getCalendars()
    {
        $package = \Chamilo\Application\Weblcms\Manager :: package();

        $calendar = new AvailableCalendar();
        $calendar->setIdentifier(ClassnameUtilities :: getInstance()->getPackageNameFromNamespace($package));
        $calendar->setType($package);
        $calendar->setName(Translation :: get('TypeName', null, $package));

        return array($calendar);
    }
}
