<?php
namespace Chamilo\Libraries\Calendar\Renderer\Event;

use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Calendar\Event\Event;
use Chamilo\Libraries\Calendar\Renderer\Renderer;

/**
 *
 * @package Chamilo\Libraries\Calendar\Renderer\Event
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class EventRendererFactory
{

    /**
     *
     * @var \Chamilo\Libraries\Calendar\Renderer\Event\Configuration
     */
    private $configuration;

    /**
     *
     * @var \Chamilo\Libraries\Calendar\Event\Event
     */
    private $event;

    /**
     *
     * @var \Chamilo\Libraries\Calendar\Renderer\Renderer
     */
    private $renderer;

    /**
     *
     * @param \Chamilo\Libraries\Calendar\Renderer\Renderer $renderer
     * @param \Chamilo\Libraries\Calendar\Event\Event $event
     * @param \Chamilo\Libraries\Calendar\Renderer\Event\Configuration $configuration
     */
    public function __construct(Renderer $renderer, Event $event, Configuration $configuration = null)
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
     * @return \Chamilo\Libraries\Calendar\Renderer\Event\Configuration
     */
    public function getConfiguration()
    {
        return $this->configuration;
    }

    /**
     *
     * @param \Chamilo\Libraries\Calendar\Renderer\Event\Configuration $configuration
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
     * @return \Chamilo\Libraries\Calendar\Renderer\Renderer
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
     * @return \Chamilo\Libraries\Calendar\Renderer\Renderer
     */
    public function getRenderer()
    {
        return $this->renderer;
    }

    /**
     *
     * @param \Chamilo\Libraries\Calendar\Renderer\Renderer $renderer
     */
    public function setRenderer($renderer)
    {
        $this->renderer = $renderer;
    }
}
