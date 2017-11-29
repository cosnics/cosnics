<?php
namespace Chamilo\Libraries\Calendar\Event\Service;

use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Calendar\Event\Event;
use Chamilo\Libraries\Calendar\Renderer\Renderer;

/**
 *
 * @package Chamilo\Libraries\Calendar\Event\Renderer
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ViewRendererFactory
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
     * @return \Chamilo\Libraries\Calendar\Event\Event
     */
    public function getEvent()
    {
        return $this->event;
    }

    /**
     *
     * @return integer
     */
    public function getStartDate()
    {
        return $this->configuration;
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
     * @return string
     */
    public function render()
    {
        return $this->getEventRenderer()->render();
    }

    /**
     *
     * @throws \Exception
     * @return \Chamilo\Libraries\Calendar\Renderer\Renderer
     */
    public function getEventRenderer()
    {
        $eventRendererClassName = ClassnameUtilities::getInstance()->getNamespaceParent($this->getEvent()->context()) .
             '\Event\Renderer\Type\\' . $this->getRenderer()->class_name(false);

        if (! class_exists($eventRendererClassName))
        {
            $eventRendererClassName = 'Chamilo\Libraries\Calendar\Event\Renderer\Type\\' .
                 $this->getRenderer()->class_name(false);
        }

        return new $eventRendererClassName($this->getRenderer(), $this->getEvent(), $this->getStartDate());
    }
}
