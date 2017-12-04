<?php
namespace Chamilo\Libraries\Calendar\View\Renderer;

/**
 *
 * @package Chamilo\Libraries\Calendar\View\Renderer
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class HtmlTableRenderer
{

    /**
     *
     * @var \Twig_Environment
     */
    private $twigEnvironment;

    /**
     *
     * @var \Chamilo\Libraries\Calendar\Format\Renderer\HtmlTableRenderer
     */
    private $formatHtmlTableRenderer;

    public function __construct(\Twig_Environment $twigEnvironment,
        \Chamilo\Libraries\Calendar\Format\Renderer\HtmlTableRenderer $formatHtmlTableRenderer)

    {
        $this->twigEnvironment = $twigEnvironment;
        $this->formatHtmlTableRenderer = $formatHtmlTableRenderer;
    }

    /**
     *
     * @return Twig_Environment
     */
    protected function getTwigEnvironment()
    {
        return $this->twigEnvironment;
    }

    /**
     *
     * @return \Chamilo\Libraries\Calendar\Format\Renderer\HtmlTableRenderer
     */
    protected function getFormatHtmlTableRenderer()
    {
        return $this->formatHtmlTableRenderer;
    }

    /**
     *
     * @return string
     */
    public function render()
    {
        return $this->getTwigEnvironment()->render(
            'Chamilo\Libraries\Calendar:HtmlTable.html.twig',
            ['baseCalendar' => $this->getFormatHtmlTableRenderer()->render()]);
    }
}

