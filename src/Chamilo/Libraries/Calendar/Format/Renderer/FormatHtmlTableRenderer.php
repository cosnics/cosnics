<?php
namespace Chamilo\Libraries\Calendar\Format\Renderer;

use Chamilo\Libraries\Calendar\CalendarConfiguration;
use Chamilo\Libraries\Calendar\CalendarSources;
use Chamilo\Libraries\Calendar\Event\Service\EventHtmlTableRendererFactory;
use Chamilo\Libraries\Calendar\Format\HtmlTable\Calendar;
use Chamilo\Libraries\Calendar\Interfaces\CalendarRendererProviderInterface;

/**
 *
 * @package Chamilo\Libraries\Calendar\Format\Renderer
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
abstract class FormatHtmlTableRenderer extends FormatHtmlRenderer
{

    /**
     *
     * @var \Chamilo\Libraries\Calendar\CalendarConfiguration
     */
    private $calendarConfiguration;

    /**
     *
     * @var \Chamilo\Libraries\Calendar\Format\HtmlTable\Calendar
     */
    private $calendar;

    /**
     *
     * @var \Chamilo\Libraries\Calendar\Event\Service\EventHtmlTableRendererFactory
     */
    private $eventHtmlTableRendererFactory;

    /**
     *
     * @param \Chamilo\Libraries\Calendar\Interfaces\CalendarRendererProviderInterface $dataProvider
     * @param \Chamilo\Libraries\Calendar\CalendarSources $calendarSources
     * @param \Chamilo\Libraries\Calendar\CalendarConfiguration $calendarConfiguration
     * @param \Chamilo\Libraries\Calendar\Format\HtmlTable\Calendar $calendar
     * @param \Chamilo\Libraries\Calendar\Event\Service\EventHtmlTableRendererFactory $eventHtmlTableRendererFactory
     */
    public function __construct(CalendarRendererProviderInterface $dataProvider, CalendarSources $calendarSources,
        CalendarConfiguration $calendarConfiguration, Calendar $calendar,
        EventHtmlTableRendererFactory $eventHtmlTableRendererFactory)
    {
        parent::__construct($dataProvider, $calendarSources);

        $this->calendarConfiguration = $calendarConfiguration;
        $this->calendar = $calendar;
        $this->eventHtmlTableRendererFactory = $eventHtmlTableRendererFactory;
    }

    /**
     *
     * @return \Chamilo\Libraries\Calendar\CalendarConfiguration
     */
    protected function getCalendarConfiguration()
    {
        return $this->calendarConfiguration;
    }

    /**
     *
     * @return \Chamilo\Libraries\Calendar\Format\HtmlTable\Calendar
     */
    protected function getCalendar()
    {
        return $this->calendar;
    }

    /**
     *
     * @return \Chamilo\Libraries\Calendar\Event\Service\HtmlTableRendererFactory
     */
    protected function getEventHtmlTableRendererFactory()
    {
        return $this->eventHtmlTableRendererFactory;
    }

    /**
     *
     * @see \Chamilo\Libraries\Calendar\Format\Renderer\FormatHtmlTableRenderer::getStartTime()
     */
    public function getStartTime()
    {
        return $this->getCalendar()->getStartTime();
    }

    /**
     *
     * @see \Chamilo\Libraries\Calendar\Format\Renderer\FormatHtmlTableRenderer::getEndTime()
     */
    public function getEndTime()
    {
        return $this->getCalendar()->getEndTime();
    }
}