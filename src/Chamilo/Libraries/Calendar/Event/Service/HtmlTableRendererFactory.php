<?php
namespace Chamilo\Libraries\Calendar\Event\Service;

use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Calendar\CalendarSources;
use Chamilo\Libraries\Calendar\Event\Event;
use Chamilo\Libraries\Calendar\Interfaces\VisibilitySupport;

/**
 *
 * @package Chamilo\Libraries\Calendar\Event\Service
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class HtmlTableRendererFactory
{

    /**
     *
     * @var \Chamilo\Libraries\Calendar\CalendarSources
     */
    private $calendarSources;

    /**
     *
     * @param \Chamilo\Libraries\Calendar\CalendarSources $calendarSources
     * @param integer $startDate
     */
    public function __construct(CalendarSources $calendarSources)
    {
        $this->calendarSources = $calendarSources;
    }

    /**
     *
     * @return \Chamilo\Libraries\Calendar\CalendarSources
     */
    protected function getCalendarSources()
    {
        return $this->calendarSources;
    }

    /**
     *
     * @param string $htmlTableRendererType
     * @param \Chamilo\Libraries\Calendar\Interfaces\VisibilitySupport $dataProvider
     * @param \Chamilo\Libraries\Calendar\Event\Event $event
     * @param integer $startDate
     * @return string
     */
    public function render($htmlTableRendererType, VisibilitySupport $dataProvider, Event $event, $startDate)
    {
        return $this->getEventRenderer($htmlTableRendererType, $event->getContext(), $dataProvider)->render(
            $event,
            $startDate);
    }

    /**
     *
     * @param string $htmlTableRendererType
     * @param \Chamilo\Libraries\Calendar\Interfaces\VisibilitySupport $dataProvider
     * @param string $eventContext
     * @return \Chamilo\Libraries\Calendar\Event\Renderer\HtmlTableRenderer
     */
    public function getEventRenderer($htmlTableRendererType, $eventContext, VisibilitySupport $dataProvider)
    {
        $eventRendererClassName = ClassnameUtilities::getInstance()->getNamespaceParent($eventContext) .
             '\Event\Renderer\Type\\' . $htmlTableRendererType;
        // $this->getRenderer()->class_name(false);

        if (! class_exists($eventRendererClassName))
        {
            $eventRendererClassName = 'Chamilo\Libraries\Calendar\Event\Renderer\Type\\' . $htmlTableRendererType;
            // $this->getRenderer()->class_name(false);
        }

        return new $eventRendererClassName($dataProvider, $this->getCalendarSources());
    }
}
