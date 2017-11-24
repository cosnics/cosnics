<?php
namespace Chamilo\Libraries\Calendar\Renderer\Service;

/**
 *
 * @package Chamilo\Libraries\Calendar\Renderer\Service
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
abstract class FullCalendarRendererProvider implements
    \Chamilo\Libraries\Calendar\Renderer\Interfaces\FullCalendarRendererProviderInterface
{

    abstract public function getEventSources();
}