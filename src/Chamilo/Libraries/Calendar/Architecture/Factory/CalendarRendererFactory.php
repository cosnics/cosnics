<?php
namespace Chamilo\Libraries\Calendar\Architecture\Factory;

use Chamilo\Libraries\Architecture\Traits\DependencyInjectionContainerTrait;
use Chamilo\Libraries\Calendar\Service\View\CalendarRenderer;

/**
 * @package Chamilo\Libraries\Calendar\Factory
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class CalendarRendererFactory
{
    use DependencyInjectionContainerTrait;

    /**
     * @throws \Exception
     */
    public function __construct()
    {
        $this->initializeContainer();
    }

    /**
     * @throws \Exception
     */
    public function getRenderer(string $rendererType): CalendarRenderer
    {
        $className = 'Chamilo\Libraries\Calendar\Service\View\\' . $rendererType . 'CalendarRenderer';

        return $this->getService($className);
    }
}
