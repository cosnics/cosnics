<?php
namespace Chamilo\Application\Calendar\Extension\Office365\Integration\Chamilo\Libraries\Calendar\Event;

/**
 *
 * @package Chamilo\Application\Calendar\Extension\Office365\Integration\Chamilo\Libraries\Calendar\Event
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class Event extends \Chamilo\Libraries\Calendar\Event\Event
{

    /**
     *
     * @var \stdClass
     */
    private $office365CalendarEvent;

    /**
     *
     * @return \stdClass
     */
    public function getOffice365CalendarEvent()
    {
        return $this->office365CalendarEvent;
    }

    /**
     *
     * @param \stdClass $office365CalendarEvent
     */
    public function setOffice365CalendarEvent(\stdClass $office365CalendarEvent)
    {
        $this->office365CalendarEvent = $office365CalendarEvent;
    }
}
