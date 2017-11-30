<?php
namespace Chamilo\Libraries\Calendar\Event\Renderer;

use Chamilo\Libraries\Calendar\Event\Event;

/**
 *
 * @package Chamilo\Libraries\Calendar\Event\Renderer
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
abstract class ViewRenderer
{

    /**
     *
     * @var \Chamilo\Libraries\Calendar\Format\Renderer\ViewRenderer
     */
    private $viewRenderer;

    /**
     *
     * @param \Chamilo\Libraries\Calendar\Format\Renderer\ViewRenderer $viewRenderer
     * @param \Chamilo\Libraries\Calendar\Event\Event $event
     * @param integer $startDate
     */
    public function __construct(\Chamilo\Libraries\Calendar\Format\Renderer\ViewRenderer $viewRenderer)
    {
        $this->viewRenderer = $viewRenderer;
    }

    /**
     *
     * @return \Chamilo\Libraries\Calendar\Format\Renderer\ViewRenderer
     */
    public function getViewRenderer()
    {
        return $this->viewRenderer;
    }

    /**
     *
     * @return string
     */
    public function getEventClasses(Event $event)
    {
        $eventClasses = 'event-container';

        if (! $this->getViewRenderer()->isSourceVisible($event->getSource()))
        {
            $eventClasses .= ' event-container-hidden';
        }

        return $eventClasses;
    }

    /**
     * Gets an html representation of an event for the renderer
     *
     * @param \Chamilo\Libraries\Calendar\Event\Event $event
     * @param integer $startDate
     * @return string
     */
    abstract public function render(Event $event, $startDate);
}
