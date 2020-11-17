<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\Home\Renderer\Extension;

use Chamilo\Application\Weblcms\Tool\Implementation\Home\Renderer\HomeRenderer;

/**
 * Class HomeRendererExtensionManager
 * @package Chamilo\Application\Weblcms\Tool\Implementation\Home\Renderer\Extension
 *
 * @author - Sven Vanpoucke - Hogeschool Gent
 */
interface HomeRendererExtensionInterface
{
    /**
     * @param HomeRenderer $homeRenderer
     *
     * @return string
     */
    public function renderTopLevelInformation(HomeRenderer $homeRenderer);
}
