<?php
namespace Chamilo\Libraries\Calendar\Service;

use Chamilo\Libraries\Calendar\Interfaces\FullCalendarRendererProviderInterface;

/**
 *
 * @package Chamilo\Libraries\Calendar\Service$FullCalendarRendererProvider
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
abstract class FullCalendarRendererProvider implements FullCalendarRendererProviderInterface
{

    /**
     *
     * @see \Chamilo\Libraries\Calendar\Interfaces\FullCalendarRendererProviderInterface::getEventSources()
     */
    abstract public function getEventSources();
}