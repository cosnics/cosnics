<?php
namespace Chamilo\Libraries\Calendar\Architecture\Factory;

use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Calendar\Event\Event;
use Chamilo\Libraries\Calendar\Service\Event\Configuration;
use Chamilo\Libraries\Calendar\Service\View\CalendarRenderer;

/**
 *
 * @package Chamilo\Libraries\Calendar\Renderer\Event
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 * @deprecated Use the EventRenderers directly as they are directly linked to a particular view anyway
 */
class EventRendererFactory
{

    /**
     *
     * @var \Chamilo\Libraries\Calendar\Service\Event\Configuration
     */
    private $configuration;

    /**
     *
     * @var \Chamilo\Libraries\Calendar\Event\Event
     */
    private $event;

    /**
     *
     * @var \Chamilo\Libraries\Calendar\Service\View\CalendarRenderer
     */
    private $renderer;

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
     *
     * @return string
     * @throws \Exception
     */
    public function render()
    {
        return $this->getEventRenderer()->render();
    }

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
    public function setConfiguration(Configuration $configuration = null)
    {
        $this->configuration = $configuration ?: new Configuration();
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
    public function setEvent(Event $event)
    {
        $this->event = $event;
    }

    /**
     *
     * @return \Chamilo\Libraries\Calendar\Service\View\CalendarRenderer
     * @throws \Exception
     */
    public function getEventRenderer()
    {
        $classNameUtilities = ClassnameUtilities::getInstance();

        $rendererClassName = $classNameUtilities->getClassNameFromNamespace(get_class($this->getRenderer()));

        $eventRendererClassName =
            $classNameUtilities->getNamespaceParent($this->getEvent()->context()) . '\Renderer\Event\Type\Event' .
            $rendererClassName;

        return new $eventRendererClassName($this->getRenderer(), $this->getEvent(), $this->getConfiguration());
    }

    /**
     *
     * @return \Chamilo\Libraries\Calendar\Service\View\CalendarRenderer
     */
    public function getRenderer()
    {
        return $this->renderer;
    }

    /**
     *
     * @param \Chamilo\Libraries\Calendar\Service\View\CalendarRenderer $renderer
     */
    public function setRenderer($renderer)
    {
        $this->renderer = $renderer;
    }
}
