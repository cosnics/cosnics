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
     * @var \Microsoft\Graph\Model\Event
     */
    private $office365CalendarEvent;

    /**
     *
     * @return \Microsoft\Graph\Model\Event
     */
    public function getOffice365CalendarEvent()
    {
        return $this->office365CalendarEvent;
    }

    /**
     *
     * @param \Microsoft\Graph\Model\Event $office365CalendarEvent
     */
    public function setOffice365CalendarEvent(\Microsoft\Graph\Model\Event $office365CalendarEvent)
    {
        $this->office365CalendarEvent = $office365CalendarEvent;
    }
}
