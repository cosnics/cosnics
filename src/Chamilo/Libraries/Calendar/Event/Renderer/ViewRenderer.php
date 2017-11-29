<?php
namespace Chamilo\Libraries\Calendar\Event\Renderer;

use Chamilo\Libraries\Calendar\Event\Event;
use Chamilo\Libraries\Calendar\Renderer\Renderer;

/**
 *
 * @package Chamilo\Libraries\Calendar\Event\Renderer
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
abstract class ViewRenderer
{

    /**
     *
     * @var \Chamilo\Libraries\Calendar\Renderer\Renderer
     */
    private $renderer;

    /**
     *
     * @var \Chamilo\Libraries\Calendar\Event\Event
     */
    private $event;

    /**
     *
     * @var integer
     */
    private $startDate;

    /**
     *
     * @param \Chamilo\Libraries\Calendar\Renderer\Renderer $renderer
     * @param \Chamilo\Libraries\Calendar\Event\Event $event
     * @param integer $startDate
     */
    public function __construct(Renderer $renderer, Event $event, $startDate)
    {
        $this->renderer = $renderer;
        $this->event = $event;
        $this->startDate = $startDate;
    }

    /**
     *
     * @return \Chamilo\Libraries\Calendar\Renderer\Type\ViewRenderer
     */
    public function getRenderer()
    {
        return $this->renderer;
    }

    /**
     *
     * @param \Chamilo\Libraries\Calendar\Renderer\Renderer $renderer
     */
    public function setRenderer(Renderer $renderer)
    {
        $this->renderer = $renderer;
    }

    /**
     *
     * @return \Chamilo\Libraries\Calendar\Event\Event
     */
    public function getEvent()
    {
        return $this->event;
    }

    /**
     *
     * @param \Chamilo\Libraries\Calendar\Event\Event $event
     */
    public function setEvent($event)
    {
        $this->event = $event;
    }

    /**
     *
     * @return integer
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     *
     * @param integer $startDate
     */
    public function setStartDate($startDate)
    {
        $this->startDate = $startDate;
    }

    /**
     *
     * @return string
     */
    public function getEventClasses()
    {
        $eventClasses = 'event-container';

        if (! $this->getRenderer()->isSourceVisible(
            $this->getEvent()->getSource(),
            $this->getRenderer()->getDataProvider()->getUser()->getId()))
        {
            $eventClasses .= ' event-container-hidden';
        }

        return $eventClasses;
    }

    /**
     * Gets an html representation of an event for the renderer
     *
     * @return string
     */
    abstract public function render();
}
