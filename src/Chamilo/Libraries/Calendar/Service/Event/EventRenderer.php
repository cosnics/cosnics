<?php
namespace Chamilo\Libraries\Calendar\Service\Event;

use Chamilo\Libraries\Calendar\Event\Event;
use Chamilo\Libraries\Calendar\Service\View\CalendarRenderer;

/**
 *
 * @package Chamilo\Libraries\Calendar\Renderer\Event
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
abstract class EventRenderer
{

    /**
     *
     * @var \Chamilo\Libraries\Calendar\Service\View\HtmlCalendarRenderer
     */
    private $renderer;

    /**
     *
     * @var \Chamilo\Libraries\Calendar\Event\Event
     */
    private $event;

    /**
     *
     * @var \Chamilo\Libraries\Calendar\Service\Event\Configuration
     */
    private $configuration;

    /**
     *
     * @param \Chamilo\Libraries\Calendar\Service\View\CalendarRenderer $renderer
     * @param \Chamilo\Libraries\Calendar\Event\Event $event
     * @param \Chamilo\Libraries\Calendar\Service\Event\Configuration $configuration
     */
    public function __construct(CalendarRenderer $renderer, Event $event, Configuration $configuration = null)
    {
        $this->renderer = $renderer;
        $this->event = $event;
        $this->configuration = $configuration ?: new Configuration();
    }

    /**
     * Gets an html representation of an event for the renderer
     *
     * @return string
     */
    abstract public function render();

    /**
     *
     * @return \Chamilo\Libraries\Calendar\Service\Event\Configuration
     */
    public function getConfiguration()
    {
        return $this->configuration;
    }

    /**
     *
     * @param \Chamilo\Libraries\Calendar\Service\Event\Configuration $configuration
     */
    public function setConfiguration(Configuration $configuration)
    {
        $this->configuration = $configuration;
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
     * @return string
     */
    public function getEventClasses()
    {
        $eventClasses = 'event-container';

        if (!$this->getRenderer()->isSourceVisible($this->getEvent()->getSource()))
        {
            $eventClasses .= ' event-container-hidden';
        }

        return $eventClasses;
    }

    /**
     *
     * @return \Chamilo\Libraries\Calendar\Service\View\HtmlCalendarRenderer
     */
    public function getRenderer()
    {
        return $this->renderer;
    }

    /**
     *
     * @param \Chamilo\Libraries\Calendar\Service\View\CalendarRenderer $renderer
     */
    public function setRenderer(CalendarRenderer $renderer)
    {
        $this->renderer = $renderer;
    }
}
