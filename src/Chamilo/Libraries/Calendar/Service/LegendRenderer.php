<?php
namespace Chamilo\Libraries\Calendar\Service;

use Chamilo\Libraries\Calendar\CalendarSources;
use Chamilo\Libraries\Calendar\Interfaces\CalendarRendererProviderInterface;

/**
 *
 * @package Chamilo\Libraries\Calendar\Service
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class LegendRenderer
{

    /**
     *
     * @var \Twig_Environment
     */
    private $twigEnvironment;

    /**
     *
     * @var \Chamilo\Libraries\Calendar\CalendarSources
     */
    private $calendarSources;

    /**
     *
     * @param \Twig_Environment $twigEnvironment
     * @param \Chamilo\Libraries\Calendar\CalendarSources $calendarSources
     */
    public function __construct(\Twig_Environment $twigEnvironment, CalendarSources $calendarSources)
    {
        $this->twigEnvironment = $twigEnvironment;
        $this->calendarSources = $calendarSources;
    }

    /**
     *
     * @return Twig_Environment
     */
    protected function getTwigEnvironment()
    {
        return $this->twigEnvironment;
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
     * Builds a color-based legend for the calendar to help users to see the origin of the the published events
     *
     * @param \Chamilo\Libraries\Calendar\Interfaces\CalendarRendererProviderInterface $dataProvider
     * @return string
     */
    public function render(CalendarRendererProviderInterface $dataProvider)
    {
        return $this->getTwigEnvironment()->render(
            'Chamilo\Libraries\Calendar:Legend.html.twig',
            ['calendarSources' => $this->getCalendarSources(), 'dataProvider' => $dataProvider]);
    }
}

