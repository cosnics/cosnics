<?php
namespace Chamilo\Libraries\Calendar\Factory;

use Chamilo\Libraries\Calendar\Service\View\HtmlCalendarRenderer;
use Chamilo\Libraries\DependencyInjection\Traits\DependencyInjectionContainerTrait;

/**
 * @package Chamilo\Libraries\Calendar\Factory
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class HtmlCalendarRendererFactory
{
    use DependencyInjectionContainerTrait;

    /**
     * @throws \Exception
     */
    public function getRenderer(string $rendererType): HtmlCalendarRenderer
    {
        /**
         * @var class-string<\Chamilo\Libraries\Calendar\Service\View\HtmlCalendarRenderer> $className
         */
        $className = 'Chamilo\Libraries\Calendar\Service\View\\' . $rendererType . 'CalendarRenderer';

        return $this->getService($className);
    }
}
