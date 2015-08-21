<?php
namespace Chamilo\Application\Calendar\Extension\SyllabusPlus\Integration\Chamilo\Application\Calendar;

use Chamilo\Application\Calendar\CalendarInterface;
use Chamilo\Application\Calendar\Extension\SyllabusPlus\Integration\Chamilo\Libraries\Calendar\Event\EventParser;
use Chamilo\Application\Calendar\Extension\SyllabusPlus\Service\CalendarService;
use Chamilo\Application\Calendar\Extension\SyllabusPlus\Repository\CalendarRepository;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Application\Calendar\Service\AvailabilityService;
use Chamilo\Application\Calendar\Repository\AvailabilityRepository;
use Chamilo\Application\Calendar\Storage\DataClass\AvailableCalendar;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Application\Calendar\Storage\DataClass\Availability;

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
        $calendarService = new CalendarService(CalendarRepository :: getInstance());
        $events = array();

        $availabilityService = new AvailabilityService(new AvailabilityRepository());
        $packageContext = ClassnameUtilities :: getInstance()->getNamespaceParent(__NAMESPACE__, 4);
        $packageName = ClassnameUtilities :: getInstance()->getPackageNameFromNamespace($packageContext);

        $activeAvailability = $availabilityService->getAvailabilityByUserAndCalendarTypeAndCalendarIdentifier(
            $renderer->getDataProvider()->getDataUser(),
            $packageContext,
            $packageName);

        $weekLabels = $calendarService->getWeekLabels();

        if ($activeAvailability instanceof Availability && $activeAvailability->getAvailability() == 1)
        {
            $eventResultSet = $calendarService->getEventsForUserAndBetweenDates(
                $renderer->getDataProvider()->getDataUser(),
                $fromDate,
                $toDate);

            while ($calenderEvent = $eventResultSet->next_result())
            {
                $eventParser = new EventParser($renderer, $weekLabels, $calenderEvent, $fromDate, $toDate);
                $events = array_merge($events, $eventParser->getEvents());
            }
        }

        return $events;
    }

    /**
     *
     * @return \Chamilo\Application\Calendar\Storage\DataClass\AvailableCalendar[]
     */
    public function getCalendars()
    {
        $package = \Chamilo\Application\Calendar\Extension\SyllabusPlus\Manager :: package();

        $calendar = new AvailableCalendar();
        $calendar->setIdentifier(ClassnameUtilities :: getInstance()->getPackageNameFromNamespace($package));
        $calendar->setType($package);
        $calendar->setName(Translation :: get('TypeName', null, $package));

        return array($calendar);
    }
}