<?php
namespace Chamilo\Libraries\Calendar\Format\Service;

use Chamilo\Libraries\Calendar\CalendarSources;
use Chamilo\Libraries\Calendar\Renderer\Interfaces\CalendarRendererProviderInterface;

/**
 *
 * @package Chamilo\Libraries\Calendar\Format\Service
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ViewRendererFactory
{

    /**
     *
     * @param string $rendererType
     * @param \Chamilo\Libraries\Calendar\Renderer\Interfaces\CalendarRendererProviderInterface $dataProvider
     * @param \Chamilo\Libraries\Calendar\CalendarSources $calendarSources
     * @param integer $displayTime
     *
     * @return string
     */
    public function renderView($rendererType, CalendarRendererProviderInterface $dataProvider,
        CalendarSources $calendarSources, $displayTime)
    {
        return $this->getViewRenderer($rendererType, $dataProvider, $calendarSources, $displayTime)->render();
    }

    /**
     *
     * @param string $rendererType
     * @param \Chamilo\Libraries\Calendar\Renderer\Interfaces\CalendarRendererProviderInterface $dataProvider
     * @param \Chamilo\Libraries\Calendar\CalendarSources $calendarSources
     * @param integer $displayTime
     * @throws \Exception
     * @return \Chamilo\Libraries\Calendar\Format\Renderer\ViewRenderer
     */
    public function getViewRenderer($rendererType, CalendarRendererProviderInterface $dataProvider,
        CalendarSources $calendarSources, $displayTime)
    {
        $className = 'Chamilo\Libraries\Calendar\Format\Renderer\Type\\' . $rendererType . 'Renderer';

        return new $className($dataProvider, $calendarSources, $displayTime);
    }
}
