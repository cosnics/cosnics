<?php
namespace Chamilo\Core\Notification\Component;

use Chamilo\Core\Notification\Manager;

/**
 * @package Chamilo\Core\Notification\Component
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class ViewerComponent extends Manager
{

    /**
     *
     * @return string
     *
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    function run()
    {
        return $this->getTwig()->render(
            Manager::CONTEXT . ':NotificationsPage.html.twig',
            ['HEADER' => $this->render_header(), 'FOOTER' => $this->render_footer()]
        );
    }
}