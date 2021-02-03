<?php

namespace Chamilo\Core\Repository\ContentObject\ExternalTool\Display\Component;

use Chamilo\Core\Repository\ContentObject\ExternalTool\Display\Manager;
use Chamilo\Core\Repository\ContentObject\ExternalTool\Display\Service\LaunchGenerator;

/**
 * @package Chamilo\Core\Repository\ContentObject\ExternalTool\Display\Component
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class ViewerComponent extends Manager
{

    /**
     * @return string
     *
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    function run()
    {
        $html = array();
        $html[] = $this->render_header();

        $html[] = $this->getLaunchGenerator()->generateLaunchHtml(
            $this->getExternalToolServiceBridge()->getExternalTool(), $this->getUser(),
            $this->getExternalToolServiceBridge()
        );

        $html[] = $this->render_footer();

        return implode(PHP_EOL, $html);
    }

    /**
     * @return \Chamilo\Core\Repository\ContentObject\ExternalTool\Display\Service\LaunchGenerator
     */
    protected function getLaunchGenerator()
    {
        return $this->getService(LaunchGenerator::class);
    }
}