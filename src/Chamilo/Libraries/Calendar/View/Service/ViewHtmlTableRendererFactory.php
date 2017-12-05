<?php
namespace Chamilo\Libraries\Calendar\View\Service;

use Chamilo\Libraries\Calendar\Format\Service\FormatHtmlTableRendererFactory;
use Chamilo\Libraries\Calendar\Interfaces\CalendarRendererProviderInterface;
use Chamilo\Libraries\Calendar\Service\LegendRenderer;
use Chamilo\Libraries\Utilities\DatetimeUtilities;
use Symfony\Component\Translation\Translator;

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
     * @var \Symfony\Component\Translation\Translator
     */
    private $translator;

    /**
     *
     * @var \Chamilo\Libraries\Utilities\DatetimeUtilities
     */
    private $datetimeUtilities;

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
     * @param \Symfony\Component\Translation\Translator $translator
     * @param \Chamilo\Libraries\Utilities\DatetimeUtilities $datetimeUtilities
     * @param \Chamilo\Libraries\Calendar\Format\Service\HtmlTableRendererFactory $formatHtmlTableRendererFactory
     * @param \Chamilo\Libraries\Calendar\Service\LegendRenderer $legendRenderer
     */
    public function __construct(\Twig_Environment $twigEnvironment, Translator $translator,
        DatetimeUtilities $datetimeUtilities, FormatHtmlTableRendererFactory $formatHtmlTableRendererFactory,
        LegendRenderer $legendRenderer)
    {
        $this->twigEnvironment = $twigEnvironment;
        $this->translator = $translator;
        $this->datetimeUtilities = $datetimeUtilities;
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
     * @return \Symfony\Component\Translation\Translator
     */
    protected function getTranslator()
    {
        return $this->translator;
    }

    /**
     *
     * @return \Chamilo\Libraries\Utilities\DatetimeUtilities
     */
    protected function getDatetimeUtilities()
    {
        return $this->datetimeUtilities;
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
     * @return \Chamilo\Libraries\Calendar\View\Renderer\ViewHtmlTableRenderer
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

        $className = 'Chamilo\Libraries\Calendar\View\Renderer\Type\\' . $rendererType . 'Renderer';

        return new $className(
            $this->getTwigEnvironment(),
            $this->getTranslator(),
            $this->getDatetimeUtilities(),
            $formatRenderer,
            $miniMontRenderer,
            $this->getLegendRenderer());
    }
}
