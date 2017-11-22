<?php
namespace Chamilo\Libraries\Format\Response;

/**
 * Extension of the response class to embed the chamilo header and footer.
 * Uses the application class (controller) temporary to display the header and the footer of Chamilo to avoid
 * duplication of code.
 *
 * @package Chamilo\Libraries\Format\Response
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class DefaultComponentResponse extends Response
{

    /**
     * The controller.
     * Used to call the existing functions to display the header and the footer
     *
     * @var \Chamilo\Libraries\Architecture\Application\Application
     */
    private $controller;

    /**
     * The constructor, adds the controller to the response to use the existing functions to display the header and
     * the footer
     *
     * @param \Chamilo\Libraries\Architecture\Application\Application $controller
     * @param string $content
     * @param integer $status
     * @param string[] $headers
     */
    public function __construct($controller, $content = '', $status = 200, $headers = array())
    {
        $this->controller = $controller;

        \Symfony\Component\HttpFoundation\Response::__construct($content, $status, $headers);
    }

    /**
     *
     * @see \Symfony\Component\HttpFoundation\Response::sendContent()
     */
    public function sendContent()
    {
        echo $this->controller->render_header();
        parent::sendContent();
        echo $this->controller->render_footer();
    }
}