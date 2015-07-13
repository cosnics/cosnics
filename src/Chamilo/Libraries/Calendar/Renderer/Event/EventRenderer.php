<?php
namespace Chamilo\Libraries\Calendar\Renderer\Event;

use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Calendar\Event\Event;
use Chamilo\Libraries\Calendar\Renderer\Renderer;

/**
 *
 * @package libraries\calendar\renderer
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
abstract class EventRenderer
{

    /**
     *
     * @var \libraries\calendar\renderer\Renderer
     */
    private $renderer;

    /**
     *
     * @var \libraries\calendar\event\Event $event
     */
    private $event;

    /**
     *
     * @param \libraries\calendar\renderer\Renderer $renderer
     * @param \libraries\calendar\event\Event $event
     * @param int $table_date
     */
    public function __construct(Renderer $renderer, Event $event)
    {
        $this->renderer = $renderer;
        $this->event = $event;
    }

    /**
     *
     * @return \Chamilo\Libraries\Calendar\Renderer\Renderer
     */
    public function get_renderer()
    {
        return $this->renderer;
    }

    /**
     *
     * @param \libraries\calendar\renderer\Renderer $renderer
     */
    public function set_renderer($renderer)
    {
        $this->renderer = $renderer;
    }

    /**
     *
     * @return \libraries\calendar\event\Event
     */
    public function get_event()
    {
        return $this->event;
    }

    /**
     *
     * @param \libraries\calendar\event\Event $event
     */
    public function set_event($event)
    {
        $this->event = $event;
    }

    public function getEventClasses()
    {
        $eventClasses = 'event';

        if (! $this->get_renderer()->isSourceVisible($this->get_event()->get_source()))
        {
            $eventClasses .= ' event-hidden';
        }

        return $eventClasses;
    }

    /**
     * Gets an html representation of an event for the renderer
     *
     * @return string
     */
    abstract public function run();

    /**
     *
     * @param Renderer $renderer
     * @param Event $event
     * @return EventRenderer
     */
    static public function factory(Renderer $renderer, Event $event)
    {
        $event_renderer_class_name = ClassnameUtilities :: getInstance()->getNamespaceParent($event :: context()) .
             '\Renderer\Event\Type\Event' . $renderer :: class_name(false);
        return new $event_renderer_class_name($renderer, $event);
    }
}
