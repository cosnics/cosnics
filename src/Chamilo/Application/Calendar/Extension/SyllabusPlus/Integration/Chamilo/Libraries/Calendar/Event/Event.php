<?php
namespace Chamilo\Application\Calendar\Extension\SyllabusPlus\Integration\Chamilo\Libraries\Calendar\Event;

/**
 *
 * @package Chamilo\Application\Calendar\Extension\SyllabusPlus\Integration\Chamilo\Libraries\Calendar\Event
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class Event extends \Chamilo\Libraries\Calendar\Event\Event
{

    /**
     *
     * @var string[]
     */
    private $calendarEvent;

    /**
     *
     * @return \stdClass
     */
    public function getCalendarEvent()
    {
        return $this->calendarEvent;
    }

    /**
     *
     * @param string[] $calendarEvent
     */
    public function setCalendarEvent(array $calendarEvent)
    {
        $this->calendarEvent = $calendarEvent;
    }
}
