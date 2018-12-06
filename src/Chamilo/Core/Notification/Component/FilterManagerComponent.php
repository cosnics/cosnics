<?php

namespace Chamilo\Core\Notification\Component;

use Chamilo\Core\Notification\Manager;

/**
 * @package Chamilo\Core\Notification\Component
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class FilterManagerComponent extends Manager
{

    /**
     *
     * @return string
     *
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    function run()
    {
        return $this->getTwig()->render(
            Manager::context() . ':FilterManager.html.twig',
            ['HEADER' => $this->render_header(), 'FOOTER' => $this->render_footer()]
        );
    }
}