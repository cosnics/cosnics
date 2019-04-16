<?php
namespace Chamilo\Core\Repository\ContentObject\ExternalTool\Display\Component;

use Chamilo\Core\Repository\ContentObject\ExternalTool\Display\Manager;

/**
 * @package Chamilo\Core\Repository\ContentObject\ExternalTool\Display\Component
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class ViewerComponent extends Manager
{

    /**
     *
     * @return string
     */
    function run()
    {
        $html = array();

        $html[] = $this->render_header();
        $html[] = $this->render_footer();

        return implode(PHP_EOL, $html);
    }
}