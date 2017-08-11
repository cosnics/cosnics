<?php

namespace Chamilo\Libraries\Format\Response;

use Chamilo\Libraries\Architecture\Application\Application;

/**
 * Extension of the response class to embed the chamilo header and footer.
 *
 * Uses the application class (controller) temporary to display the header and the footer of Chamilo to avoid
 * duplication of code.
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class DefaultComponentResponse extends Response
{
    /**
     * The controller. Used to call the existing functions to display the header and the footer
     *
     * @var Application
     */
    private $controller;

    /**
     * The constructor, adds the controller to the response to use the existing functions to display the header and
     * the footer
     *
     * @param Application $controller
     * @param string $content
     * @param int $status
     * @param array $headers
     */
    public function __construct($controller, $content = '', $status = 200, $headers = array())
    {
        $this->controller = $controller;

        \Symfony\Component\HttpFoundation\Response::__construct($content, $status, $headers);
    }

    /**
     * Sends content for the current web response.
     *
     * @return Response
     */
    public function sendContent()
    {
        echo $this->controller->render_header();
        parent::sendContent();
        echo $this->controller->render_footer();
    }
}