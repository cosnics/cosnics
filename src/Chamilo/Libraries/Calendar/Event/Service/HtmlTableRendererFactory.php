<?php
namespace Chamilo\Libraries\Calendar\Event\Service;

use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Calendar\Event\Event;
use Chamilo\Libraries\Calendar\Format\Renderer\Renderer;

/**
 *
 * @package Chamilo\Libraries\Calendar\Event\Renderer
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class HtmlTableRendererFactory
{

    /**
     *
     * @var \Chamilo\Libraries\Calendar\Renderer\Renderer
     */
    private $renderer;

    /**
     *
     * @param \Chamilo\Libraries\Calendar\Renderer\Renderer $renderer
     * @param \Chamilo\Libraries\Calendar\Event\Event $event
     * @param integer $startDate
     */
    public function __construct(Renderer $renderer)
    {
        $this->renderer = $renderer;
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
     * @param \Chamilo\Libraries\Calendar\Event\Event $event
     * @param integer $startDate
     * @return string
     */
    public function render(Event $event, $startDate)
    {
        return $this->getEventRenderer($event->getContext())->render($event, $startDate);
    }

    /**
     *
     * @param string $eventContext
     * @return \Chamilo\Libraries\Calendar\Event\Renderer\HtmlTableRenderer
     */
    public function getEventRenderer($eventContext)
    {
        $eventRendererClassName = ClassnameUtilities::getInstance()->getNamespaceParent($eventContext) .
             '\Event\Renderer\Type\\' . $this->getRenderer()->class_name(false);

        if (! class_exists($eventRendererClassName))
        {
            $eventRendererClassName = 'Chamilo\Libraries\Calendar\Event\Renderer\Type\\' .
                 $this->getRenderer()->class_name(false);
        }

        return new $eventRendererClassName($this->getRenderer());
    }
}
