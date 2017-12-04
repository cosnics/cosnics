<?php
namespace Chamilo\Libraries\Calendar\View\Service;

use Chamilo\Libraries\Calendar\Format\Service\FormatHtmlTableRendererFactory;
use Chamilo\Libraries\Calendar\Interfaces\CalendarRendererProviderInterface;
use Chamilo\Libraries\Calendar\Service\LegendRenderer;
use Chamilo\Libraries\Calendar\View\Renderer\ViewHtmlTableRenderer;

/**
 *
 * @package Chamilo\Libraries\Calendar\View\Service
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ViewHtmlTableRendererFactory
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
    private $formatHtmlTableRendererFactory;

    /**
     *
     * @var \Chamilo\Libraries\Calendar\Service\LegendRenderer
     */
    private $legendRenderer;

    /**
     *
     * @param \Twig_Environment $twigEnvironment
     * @param \Chamilo\Libraries\Calendar\Format\Service\HtmlTableRendererFactory $formatHtmlTableRendererFactory
     * @param \Chamilo\Libraries\Calendar\Service\LegendRenderer $legendRenderer
     */
    public function __construct(\Twig_Environment $twigEnvironment,
        FormatHtmlTableRendererFactory $formatHtmlTableRendererFactory, LegendRenderer $legendRenderer)
    {
        $this->twigEnvironment = $twigEnvironment;
        $this->formatHtmlTableRendererFactory = $formatHtmlTableRendererFactory;
        $this->legendRenderer = $legendRenderer;
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
     * @return \Chamilo\Libraries\Calendar\Format\Service\FormatHtmlTableRendererFactory
     */
    protected function getFormatHtmlTableRendererFactory()
    {
        return $this->formatHtmlTableRendererFactory;
    }

    /**
     *
     * @return \Chamilo\Libraries\Calendar\Service\LegendRenderer
     */
    protected function getLegendRenderer()
    {
        return $this->legendRenderer;
    }

    /**
     *
     * @param string $rendererType
     * @param \Chamilo\Libraries\Calendar\Interfaces\CalendarRendererProviderInterface $calendarDataProvider
     * @param integer $rendererTime
     * @return \Chamilo\Libraries\Calendar\View\Renderer\HtmlTableRenderer
     */
    public function getViewHtmlTableRenderer($rendererType, CalendarRendererProviderInterface $calendarDataProvider,
        $rendererTime)
    {
        $formatHtmlTableRendererFactory = $this->getFormatHtmlTableRendererFactory();

        $formatRenderer = $formatHtmlTableRendererFactory->getFormatHtmlTableRenderer(
            $rendererType,
            $calendarDataProvider,
            $rendererTime);

        $miniMontRenderer = $formatHtmlTableRendererFactory->getFormatHtmlTableRenderer(
            'MiniMonth',
            $calendarDataProvider,
            $rendererTime);

        return new ViewHtmlTableRenderer(
            $this->getTwigEnvironment(),
            $formatRenderer,
            $miniMontRenderer,
            $this->getLegendRenderer());
    }
}
