<?php
namespace Chamilo\Libraries\Calendar\Service;

use Chamilo\Libraries\Calendar\Interfaces\FullCalendarRendererProviderInterface;

/**
 *
 * @package Chamilo\Libraries\Calendar\Renderer\Service
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
abstract class FullCalendarRendererProvider implements FullCalendarRendererProviderInterface
{

    abstract public function getEventSources();
}