<?php
namespace Chamilo\Application\Weblcms\Integration\Chamilo\Application\Calendar\Service;

use Chamilo\Application\Calendar\Architecture\InternalCalendar;
use Chamilo\Application\Calendar\Repository\AvailabilityRepository;
use Chamilo\Application\Calendar\Service\AvailabilityService;
use Chamilo\Application\Calendar\Storage\DataClass\AvailableCalendar;
use Chamilo\Application\Weblcms\Course\Storage\DataClass\Course;
use Chamilo\Application\Weblcms\Integration\Chamilo\Libraries\Calendar\Event\EventParser;
use Chamilo\Application\Weblcms\Service\ServiceFactory;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Calendar\Renderer\Service\CalendarRendererProvider;

/**
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 * @author Sven Vanpoucke - Hogeschool Gent
 */
abstract class CalendarEventDataProvider extends InternalCalendar
{

    /**
     *
     * @see \Chamilo\Application\Calendar\CalendarInterface::getEvents()
     *
     * @param CalendarRendererProvider $calendarRendererProvider
     * @param int $requestedSourceType
     * @param int $fromDate
     * @param int $toDate
     *
     * @return array|\Chamilo\Libraries\Calendar\Event\Event[]
     */
    public function getEvents(CalendarRendererProvider $calendarRendererProvider, $requestedSourceType, $fromDate,
        $toDate)
    {
        $rightsService = ServiceFactory::getInstance()->getRightsService();

        $events = array();

        $availabilityService = new AvailabilityService(new AvailabilityRepository());
        $packageContext = $this->getCalendarContext();
        $packageName = ClassnameUtilities::getInstance()->getPackageNameFromNamespace($packageContext);

        if ($availabilityService->isAvailableForUserAndCalendarTypeAndCalendarIdentifier(
            $calendarRendererProvider->getDataUser(),
            $packageContext,
            $packageName))
        {
            $publications = $this->getPublications($calendarRendererProvider->getDataUser(), $fromDate, $toDate);

            foreach ($publications as $publication)
            {
                $course = new Course();
                $course->setId($publication->get_course_id());

                if (! $rightsService->canUserViewPublication(
                    $calendarRendererProvider->getDataUser(),
                    $publication,
                    $course))
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
     * @see \Chamilo\Application\Calendar\Architecture\CalendarInterface::getCalendars()
     */
    public function getCalendars(User $user)
    {
        $package = $this->getCalendarContext();

        $calendar = new AvailableCalendar();
        $calendar->setIdentifier(ClassnameUtilities::getInstance()->getPackageNameFromNamespace($package));
        $calendar->setType($package);
        $calendar->setName($this->getCalendarName());

        return array($calendar);
    }

    /**
     * Retrieves the valid publications for the user
     *
     * @param User $user
     * @param int $fromData
     * @param int $toDate
     *
     * @return ContentObjectPublication[]
     */
    abstract protected function getPublications(User $user, $fromData, $toDate);

    /**
     * Returns the context for the calendar
     *
     * @return string
     */
    abstract protected function getCalendarContext();

    /**
     * Returns the name for the calendar
     *
     * @return string
     */
    abstract protected function getCalendarName();
}
