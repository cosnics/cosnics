<?php
namespace Chamilo\Application\Calendar\Extension\Office365\Integration\Chamilo\Application\Calendar;

use Chamilo\Application\Calendar\CalendarInterface;
use Chamilo\Application\Calendar\Extension\Office365\Integration\Chamilo\Libraries\Calendar\Event\EventParser;
use Chamilo\Application\Calendar\Extension\Office365\Service\Office365CalendarService;
use Chamilo\Application\Calendar\Extension\Office365\Repository\Office365CalendarRepository;

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
        $office365CalendarService = new Office365CalendarService(Office365CalendarRepository :: getInstance());

        $events = array();

        $eventResultSet = $office365CalendarService->getEventsBetweenDates($fromDate, $toDate);

        while ($office365CalenderEvent = $eventResultSet->next_result())
        {
            $eventParser = new EventParser(
                $renderer,
                $office365CalenderEvent,
                $fromDate,
                $toDate);
            $events = array_merge($events, $eventParser->getEvents());
        }

        return $events;
    }
}