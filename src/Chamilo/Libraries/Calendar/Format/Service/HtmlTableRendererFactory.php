<?php
namespace Chamilo\Libraries\Calendar\Format\Service;

use Chamilo\Libraries\Calendar\CalendarSources;
use Chamilo\Libraries\Calendar\Interfaces\CalendarRendererProviderInterface;
use Chamilo\Libraries\Calendar\CalendarConfiguration;

/**
 *
 * @package Chamilo\Libraries\Calendar\Format\Service
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
     * @var \Chamilo\Libraries\Calendar\CalendarConfiguration
     */
    private $calendarConfiguration;

    /**
     *
     * @var \Chamilo\Libraries\Calendar\Format\Service\CalendarBuilderFactory
     */
    private $calendarBuilderFactory;

    /**
     *
     * @var \Chamilo\Libraries\Calendar\Event\Service\HtmlTableRendererFactory
     */
    private $eventHtmlTableRendererFactory;

    /**
     *
     * @param \Chamilo\Libraries\Calendar\CalendarSources $calendarSources
     * @param \Chamilo\Libraries\Calendar\CalendarConfiguration $calendarConfiguration
     * @param \Chamilo\Libraries\Calendar\Format\Service\CalendarBuilderFactory $calendarBuilderFactory
     * @param \Chamilo\Libraries\Calendar\Event\Service\HtmlTableRendererFactory $eventHtmlTableRendererFactory
     */
    public function __construct(CalendarSources $calendarSources, CalendarConfiguration $calendarConfiguration,
        CalendarBuilderFactory $calendarBuilderFactory,
        \Chamilo\Libraries\Calendar\Event\Service\HtmlTableRendererFactory $eventHtmlTableRendererFactory)
    {
        $this->calendarSources = $calendarSources;
        $this->calendarConfiguration = $calendarConfiguration;
        $this->calendarBuilderFactory = $calendarBuilderFactory;
        $this->eventHtmlTableRendererFactory = $eventHtmlTableRendererFactory;
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
     * @return \Chamilo\Libraries\Calendar\CalendarConfiguration
     */
    protected function getCalendarConfiguration()
    {
        return $this->calendarConfiguration;
    }

    /**
     *
     * @return \Chamilo\Libraries\Calendar\Format\Service\CalendarBuilderFactory
     */
    protected function getCalendarBuilderFactory()
    {
        return $this->calendarBuilderFactory;
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
     * @param string $rendererType
     * @param \Chamilo\Libraries\Calendar\Interfaces\CalendarRendererProviderInterface $dataProvider
     * @param integer $displayTime
     * @throws \Exception
     * @return \Chamilo\Libraries\Calendar\Format\Renderer\HtmlTableRenderer
     */
    public function getHtmlTableRenderer($rendererType, CalendarRendererProviderInterface $dataProvider, $displayTime)
    {
        $className = 'Chamilo\Libraries\Calendar\Format\Renderer\Type\\' . $rendererType . 'Renderer';

        return new $className(
            $dataProvider,
            $this->getCalendarSources(),
            $this->getCalendarConfiguration(),
            $this->getCalendar($rendererType, $displayTime, $dataProvider->getDisplayParameters()),
            $this->getEventHtmlTableRendererFactory());
    }

    /**
     *
     * @param string $calendarType
     * @param integer $displayTime
     * @param string[] $displayParameters
     * @return \Chamilo\Libraries\Calendar\Format\HtmlTable\Calendar
     */
    protected function getCalendar($calendarType, $displayTime, $displayParameters)
    {
        return $this->getCalendarBuilderFactory()->getCalendarBuilder($calendarType)->buildCalendar(
            $displayTime,
            $displayParameters);
    }
}
