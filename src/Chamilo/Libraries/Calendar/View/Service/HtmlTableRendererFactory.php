<?php
namespace Chamilo\Libraries\Calendar\View\Service;

use Chamilo\Libraries\Calendar\View\Renderer\HtmlTableRenderer;
use Chamilo\Libraries\Calendar\Interfaces\CalendarRendererProviderInterface;

/**
 *
 * @package Chamilo\Libraries\Calendar\View\Service
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class HtmlTableRendererFactory
{

    /**
     *
     * @var \Twig_Environment
     */
    private $twigEnvironment;

    /**
     *
     * @var \Chamilo\Libraries\Calendar\Format\Service\HtmlTableRendererFactory
     */
    private $htmlTableRendererFactory;

    /**
     *
     * @param \Twig_Environment $twigEnvironment
     * @param \Chamilo\Libraries\Calendar\Format\Service\HtmlTableRendererFactory $htmlTableRendererFactory
     */
    public function __construct(\Twig_Environment $twigEnvironment,
        \Chamilo\Libraries\Calendar\Format\Service\HtmlTableRendererFactory $htmlTableRendererFactory)
    {
        $this->twigEnvironment = $twigEnvironment;
        $this->htmlTableRendererFactory = $htmlTableRendererFactory;
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
     * @return \Chamilo\Libraries\Calendar\Format\Service\HtmlTableRendererFactory
     */
    protected function getHtmlTableRendererFactory()
    {
        return $this->htmlTableRendererFactory;
    }

    /**
     *
     * @param string $rendererType
     * @param CalendarRendererProviderInterface $calendarDataProvider
     * @param integer $rendererTime
     * @return \Chamilo\Libraries\Calendar\View\Renderer\HtmlTableRenderer
     */
    public function getHtmlTableRenderer($rendererType, CalendarRendererProviderInterface $calendarDataProvider,
        $rendererTime)
    {
        $formatHtmlTableRenderer = $this->getHtmlTableRendererFactory()->getHtmlTableRenderer(
            $rendererType,
            $calendarDataProvider,
            $rendererTime);

        $miniMonthFormatHtmlTableRenderer = $this->getHtmlTableRendererFactory()->getHtmlTableRenderer(
            'MiniMonth',
            $calendarDataProvider,
            $rendererTime);

        return new HtmlTableRenderer(
            $this->getTwigEnvironment(),
            $formatHtmlTableRenderer,
            $miniMonthFormatHtmlTableRenderer);
    }
}
