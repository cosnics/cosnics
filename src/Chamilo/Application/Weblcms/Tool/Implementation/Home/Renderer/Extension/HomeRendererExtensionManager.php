<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\Home\Renderer\Extension;

use Chamilo\Application\Weblcms\Tool\Implementation\Home\Renderer\HomeRenderer;

/**
 * Class HomeRendererExtensionManager
 * @package Chamilo\Application\Weblcms\Tool\Implementation\Home\Renderer\Extension
 *
 * @author - Sven Vanpoucke - Hogeschool Gent
 */
class HomeRendererExtensionManager
{
    /**
     * @var HomeRendererExtensionInterface[]
     */
    protected $homeRendererExtensions;

    /**
     * HomeRendererExtensionManager constructor.
     */
    public function __construct()
    {
        $this->homeRendererExtensions = [];
    }

    /**
     * @param HomeRendererExtensionInterface $homeRendererExtension
     */
    public function addHomeRendererExtension(HomeRendererExtensionInterface $homeRendererExtension)
    {
        $this->homeRendererExtensions[] = $homeRendererExtension;
    }

    /**
     * @param HomeRenderer $homeRenderer
     *
     * @return string
     */
    public function renderTopLevelInformation(HomeRenderer $homeRenderer)
    {
        $html = [];

        foreach($this->homeRendererExtensions as $homeRendererExtension)
        {
            $html[] = $homeRendererExtension->renderTopLevelInformation($homeRenderer);
        }

        return implode(PHP_EOL, $html);
    }

}
