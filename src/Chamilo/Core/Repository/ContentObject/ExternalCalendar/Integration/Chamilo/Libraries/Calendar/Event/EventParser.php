<?php
namespace Chamilo\Core\Repository\ContentObject\ExternalCalendar\Integration\Chamilo\Libraries\Calendar\Event;

/**
 *
 * @package Chamilo\Core\Repository\ContentObject\ExternalCalendar\Integration\Chamilo\Libraries\Calendar\Event
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class EventParser extends \Chamilo\Core\Repository\Integration\Chamilo\Libraries\Calendar\Event\EventParser
{

    public function getEvents()
    {
        $events = array();
        $fromDate = $this->getStartDate();
        $toDate = $this->getEndDate();
        $object = $this->getContentObject();

        $calendarEvents = $object->get_occurences($fromDate, $toDate);

        foreach ($calendarEvents as $calendarEvent)
        {
            $event = $this->getEventInstance();

            $event->setStartDate($calendarEvent->DTSTART->getDateTime()->getTimeStamp());
            $event->setEndDate($calendarEvent->DTEND->getDateTime()->getTimeStamp());

            if (! is_null($calendarEvent->SUMMARY))
            {
                $event->setTitle($calendarEvent->SUMMARY->getValue());
            }

            if (! is_null($calendarEvent->DESCRIPTION))
            {
                $event->setContent($calendarEvent->DESCRIPTION->getValue());
            }

            $event->setSource($object->get_title());
            $event->setContentObject($object);

            $events[] = $event;
        }

        return $events;
    }
}
