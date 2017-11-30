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
     * @param \Chamilo\Libraries\Calendar\CalendarSources $calendarSources
     * @param \Chamilo\Libraries\Calendar\CalendarConfiguration $calendarConfiguration
     * @param \Chamilo\Libraries\Calendar\Format\Service\CalendarBuilderFactory $calendarBuilderFactory
     */
    public function __construct(CalendarSources $calendarSources, CalendarConfiguration $calendarConfiguration,
        CalendarBuilderFactory $calendarBuilderFactory)
    {
        $this->calendarSources = $calendarSources;
        $this->calendarConfiguration = $calendarConfiguration;
        $this->calendarBuilderFactory = $calendarBuilderFactory;
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
            $this->getCalendar($rendererType, $displayTime, $dataProvider->getDisplayParameters()));
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
