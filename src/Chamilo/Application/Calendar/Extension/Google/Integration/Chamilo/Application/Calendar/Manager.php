<?php
namespace Chamilo\Application\Calendar\Extension\Google\Integration\Chamilo\Application\Calendar;

use Chamilo\Application\Calendar\CalendarInterface;
use Chamilo\Application\Calendar\Extension\Google\Integration\Chamilo\Libraries\Calendar\Event\EventParser;
use Chamilo\Application\Calendar\Extension\Google\Service\GoogleCalendarService;
use Chamilo\Application\Calendar\Extension\Google\Repository\GoogleCalendarRepository;
use Chamilo\Application\Calendar\Extension\Google\Service\VisibilityService;
use Chamilo\Application\Calendar\Extension\Google\Repository\VisibilityRepository;

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
        $visibilityService = new VisibilityService(new VisibilityRepository());
        $googleCalendarService = new GoogleCalendarService(GoogleCalendarRepository :: getInstance());

        $activeVisibilities = $visibilityService->getActiveVisibilitiesForUser(
            $renderer->getDataProvider()->getDataUser());

        $events = array();

        while ($activeVisibility = $activeVisibilities->next_result())
        {
            $eventResultSet = $googleCalendarService->getEventsForCalendarIdentifierAndBetweenDates(
                $activeVisibility->getCalendarId(),
                $fromDate,
                $toDate);

            while ($googleCalenderEvent = $eventResultSet->next_result())
            {
                $eventParser = new EventParser(
                    $renderer,
                    $eventResultSet->getCalendarProperties(),
                    $googleCalenderEvent,
                    $fromDate,
                    $toDate);
                $events = array_merge($events, $eventParser->getEvents());
            }
        }

        return $events;
    }
}