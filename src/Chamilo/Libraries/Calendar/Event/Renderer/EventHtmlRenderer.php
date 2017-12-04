<?php
namespace Chamilo\Libraries\Calendar\Event\Renderer;

use Chamilo\Libraries\Calendar\CalendarSources;
use Chamilo\Libraries\Calendar\Event\Event;
use Chamilo\Libraries\Calendar\Interfaces\VisibilitySupport;

/**
 *
 * @package Chamilo\Libraries\Calendar\Event\Renderer
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
abstract class EventHtmlRenderer
{

    /**
     *
     * @var \Chamilo\Libraries\Calendar\CalendarSources
     */
    private $calendarSources;

    /**
     *
     * @param \Chamilo\Libraries\Calendar\Interfaces\VisibilitySupport $dataProvider
     * @param \Chamilo\Libraries\Calendar\CalendarSources $calendarSources
     * @param integer $startDate
     */
    public function __construct(VisibilitySupport $dataProvider, CalendarSources $calendarSources)
    {
        if (! $dataProvider instanceof VisibilitySupport)
        {
            throw new \Exception('Please implement the CalendarRendererProviderInterface in ' . get_class($dataProvider));
        }

        $this->dataProvider = $dataProvider;
        $this->calendarSources = $calendarSources;
    }

    /**
     *
     * @return \Chamilo\Libraries\Calendar\Interfaces\VisibilitySupport
     */
    public function getDataProvider()
    {
        return $this->dataProvider;
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
     * Gets an html representation of an event for the renderer
     *
     * @param \Chamilo\Libraries\Calendar\Event\Event $event
     * @param integer $startDate
     * @return string
     */
    abstract public function render(Event $event, $startDate);
}
