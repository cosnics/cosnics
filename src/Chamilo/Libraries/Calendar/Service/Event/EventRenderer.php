<?php
namespace Chamilo\Libraries\Calendar\Service\Event;

use Chamilo\Libraries\Calendar\Service\LegendRenderer;

/**
 *
 * @package Chamilo\Libraries\Calendar\Renderer\Event
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
abstract class EventRenderer
{
    private LegendRenderer $legendRenderer;

    public function __construct(LegendRenderer $legendRenderer)
    {
        $this->legendRenderer = $legendRenderer;
    }

    public function getEventClasses(bool $isEventSourceVisible = true): string
    {
        $eventClasses = 'event-container';

        if (!$isEventSourceVisible)
        {
            $eventClasses .= ' event-container-hidden';
        }

        return $eventClasses;
    }

    public function getLegendRenderer(): LegendRenderer
    {
        return $this->legendRenderer;
    }

    public function setLegendRenderer(LegendRenderer $legendRenderer): EventRenderer
    {
        $this->legendRenderer = $legendRenderer;

        return $this;
    }
}
