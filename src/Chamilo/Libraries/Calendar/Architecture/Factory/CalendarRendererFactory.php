<?php
namespace Chamilo\Libraries\Calendar\Architecture\Factory;

use Chamilo\Libraries\Calendar\Service\View\CalendarRenderer;

/**
 * @package Chamilo\Libraries\Calendar\Factory
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class CalendarRendererFactory
{
    /**
     * @throws \Exception
     */
    public function getRenderer(string $rendererType): CalendarRenderer
    {
        $className = __NAMESPACE__ . '\View\\' . $rendererType . 'Renderer';

        return new $className();
    }
}
