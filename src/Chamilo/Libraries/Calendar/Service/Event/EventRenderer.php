<?php
namespace Chamilo\Libraries\Calendar\Service\Event;

use Chamilo\Libraries\Calendar\Service\LegendRenderer;

/**
 * @package Chamilo\Libraries\Calendar\Service\Event
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
abstract class EventRenderer
{
    public function __construct(protected LegendRenderer $legendRenderer)
    {
    }

    public function getEventClasses(bool $isEventSourceVisible = true): string
    {
        $eventClasses = 'event-container';

        if (!$isEventSourceVisible) {
            $eventClasses .= ' event-container-hidden';
        }

        return $eventClasses;
    }
}
