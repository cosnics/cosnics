<?php
namespace Chamilo\Libraries\Calendar\View\Renderer;

/**
 *
 * @package Chamilo\Libraries\Calendar\View\Renderer
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ViewRenderer
{

    /**
     *
     * @var \Twig_Environment
     */
    private $twigEnvironment;

    public function __construct(\Twig_Environment $twigEnvironment)
    {
        $this->twigEnvironment = $twigEnvironment;
    }

    /**
     *
     * @return Twig_Environment
     */
    protected function getTwigEnvironment()
    {
        return $this->twigEnvironment;
    }

    public function render()
    {
        $this->getTwigEnvironment()->render('Chamilo\Libraries\Calendar:HtmlTable.html.twig', []);
    }
}

