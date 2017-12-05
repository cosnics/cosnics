<?php
namespace Chamilo\Libraries\Calendar\View\Renderer;

use Chamilo\Libraries\Calendar\Format\HtmlTable\Calendar;
use Chamilo\Libraries\Calendar\Format\Renderer\FormatHtmlRenderer;
use Chamilo\Libraries\Calendar\Format\Renderer\FormatHtmlTableRenderer;
use Chamilo\Libraries\Calendar\Format\Renderer\Type\MiniMonthRenderer;
use Chamilo\Libraries\Calendar\Service\LegendRenderer;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonGroup;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar;
use Chamilo\Libraries\Format\Structure\ActionBar\DropdownButton;
use Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer;
use Chamilo\Libraries\Format\Structure\ActionBar\SubButton;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\DatetimeUtilities;
use Chamilo\Libraries\Calendar\Service\DateSelectionRenderer;

/**
 *
 * @package Chamilo\Libraries\Calendar\View\Renderer
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
abstract class ViewHtmlTableRenderer
{

    /**
     *
     * @var \Twig_Environment
     */
    private $twigEnvironment;

    /**
     *
     * @var \Chamilo\Libraries\Utilities\DatetimeUtilities
     */
    private $datetimeUtilities;

    /**
     *
     * @var \Chamilo\Libraries\Calendar\Format\Renderer\FormatHtmlTableRenderer
     */
    private $formatRenderer;

    /**
     *
     * @var \Chamilo\Libraries\Calendar\Format\Renderer\Type\MiniMonthRenderer
     */
    private $miniMonthRenderer;

    /**
     *
     * @var \Chamilo\Libraries\Calendar\Service\LegendRenderer
     */
    private $legendRenderer;

    /**
     *
     * @var \Chamilo\Libraries\Calendar\Service\DateSelectionRenderer
     */
    private $dateSelectionRenderer;

    /**
     *
     * @param \Twig_Environment $twigEnvironment
     * @param \Chamilo\Libraries\Utilities\DatetimeUtilities $datetimeUtilities
     * @param \Chamilo\Libraries\Calendar\Format\Renderer\FormatHtmlTableRenderer $formatRenderer
     * @param \Chamilo\Libraries\Calendar\Format\Renderer\Type\MiniMonthRenderer $miniMonthRenderer
     * @param \Chamilo\Libraries\Calendar\Service\LegendRenderer $legendRenderer
     * @param \Chamilo\Libraries\Calendar\Service\DateSelectionRenderer $dateSelectionRenderer
     */
    public function __construct(\Twig_Environment $twigEnvironment, DatetimeUtilities $datetimeUtilities,
        FormatHtmlTableRenderer $formatRenderer, MiniMonthRenderer $miniMonthRenderer, LegendRenderer $legendRenderer,
        DateSelectionRenderer $dateSelectionRenderer)

    {
        $this->twigEnvironment = $twigEnvironment;
        $this->datetimeUtilities = $datetimeUtilities;
        $this->formatRenderer = $formatRenderer;
        $this->miniMonthRenderer = $miniMonthRenderer;
        $this->legendRenderer = $legendRenderer;
        $this->dateSelectionRenderer = $dateSelectionRenderer;
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
     * @return \Chamilo\Libraries\Utilities\DatetimeUtilities
     */
    protected function getDatetimeUtilities()
    {
        return $this->datetimeUtilities;
    }

    /**
     *
     * @return \Chamilo\Libraries\Calendar\Format\Renderer\FormatHtmlTableRenderer
     */
    protected function getFormatRenderer()
    {
        return $this->formatRenderer;
    }

    /**
     *
     * @return \Chamilo\Libraries\Calendar\Format\Renderer\Type\MiniMonthRenderer
     */
    protected function getMiniMonthRenderer()
    {
        return $this->miniMonthRenderer;
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
     * @return \Chamilo\Libraries\Calendar\Service\DateSelectionRenderer
     */
    protected function getDateSelectionRenderer()
    {
        return $this->dateSelectionRenderer;
    }

    /**
     *
     * @param integer $currentRendererTime
     * @return string
     */
    abstract protected function getTitle($currentRendererTime);

    /**
     *
     * @param integer $currentRendererTime
     * @param string[] $displayParameters
     * @param \Chamilo\Libraries\Format\Structure\ActionBar\AbstractButtonToolBarItem[] $viewActions
     * @return string
     */
    public function render($currentRendererTime, $displayParameters, $viewActions)
    {
        return $this->getTwigEnvironment()->render(
            'Chamilo\Libraries\Calendar:HtmlTable.html.twig',
            [
                'mainCalendar' => $this->getFormatRenderer()->render(),
                'mainActions' => $this->renderViewActions($displayParameters, $viewActions),
                'mainNavigation' => $this->renderNavigation($displayParameters, $currentRendererTime),
                'miniCalendar' => $this->getMiniMonthRenderer()->render(),
                'miniPreviousUrl' => $this->getPreviousMonthUrl($displayParameters, $currentRendererTime),
                'miniNextUrl' => $this->getNextMonthUrl($displayParameters, $currentRendererTime),
                'title' => $this->getTitle($currentRendererTime),
                'legend' => $this->getLegendRenderer()->render($this->getFormatRenderer()->getDataProvider()),
                'dateSelection' => $this->getDateSelectionRenderer()->render(
                    $this->determineNavigationUrl($displayParameters),
                    $currentRendererTime)]);
    }

    /**
     *
     * @param integer $currentRendererTime
     * @param string[] $displayParameters
     * @return string
     */
    protected function getPreviousMonthUrl($displayParameters, $currentRendererTime)
    {
        $urlFormat = $this->determineNavigationUrl($displayParameters);
        $previousTime = strtotime('-1 Month', $currentRendererTime);
        return str_replace(Calendar::TIME_PLACEHOLDER, $previousTime, $urlFormat);
    }

    /**
     *
     * @param integer $currentRendererTime
     * @param string[] $displayParameters
     * @return string
     */
    protected function getNextMonthUrl($displayParameters, $currentRendererTime)
    {
        $urlFormat = $this->determineNavigationUrl($displayParameters);
        $nextTime = strtotime('+1 Month', $currentRendererTime);
        return str_replace(Calendar::TIME_PLACEHOLDER, $nextTime, $urlFormat);
    }

    /**
     *
     * @param string[] $displayParameters
     * @param \Chamilo\Libraries\Format\Structure\ActionBar\AbstractButtonToolBarItem[] $viewActions
     * @return string
     */
    protected function renderViewActions($displayParameters, $viewActions)
    {
        $buttonToolBar = new ButtonToolBar();
        $buttonGroup = new ButtonGroup();

        foreach ($viewActions as $viewAction)
        {
            $buttonToolBar->addItem($viewAction);
        }

        $buttonToolBar->addItem($this->renderTypeButton($displayParameters));

        $buttonToolbarRenderer = new ButtonToolBarRenderer($buttonToolBar);

        return $buttonToolbarRenderer->render();
    }

    /**
     *
     * @param string[] $displayParameters
     * @return \Chamilo\Libraries\Format\Structure\ActionBar\DropdownButton
     */
    protected function renderTypeButton($displayParameters)
    {
        $rendererTypes = array(
            FormatHtmlRenderer::TYPE_MONTH,
            FormatHtmlRenderer::TYPE_WEEK,
            FormatHtmlRenderer::TYPE_DAY);

        $currentRendererType = $displayParameters[FormatHtmlRenderer::PARAM_TYPE];

        $button = new DropdownButton(Translation::get($currentRendererType . 'View'), new FontAwesomeGlyph('calendar'));
        $button->setDropdownClasses('dropdown-menu-right');

        foreach ($rendererTypes as $rendererType)
        {
            $displayParameters[FormatHtmlRenderer::PARAM_TYPE] = $rendererType;
            $typeUrl = new Redirect($displayParameters);

            $button->addSubButton(
                new SubButton(
                    Translation::get($rendererType . 'View'),
                    null,
                    $typeUrl->getUrl(),
                    SubButton::DISPLAY_LABEL,
                    false,
                    $currentRendererType == $rendererType ? 'selected' : 'not-selected'));
        }

        return $button;
    }

    /**
     *
     * @param string[] $displayParameters
     * @param integer $currentRendererTime
     * @return string
     */
    protected function renderNavigation($displayParameters, $currentRendererTime)
    {
        $urlFormat = $this->determineNavigationUrl($displayParameters);

        $previousTime = $this->getPreviousDisplayTime($currentRendererTime);
        $nextTime = $this->getNextDisplayTime($currentRendererTime);

        $todayUrl = str_replace(Calendar::TIME_PLACEHOLDER, time(), $urlFormat);
        $previousUrl = str_replace(Calendar::TIME_PLACEHOLDER, $previousTime, $urlFormat);
        $nextUrl = str_replace(Calendar::TIME_PLACEHOLDER, $nextTime, $urlFormat);

        $buttonToolBar = new ButtonToolBar();
        $buttonGroup = new ButtonGroup();

        $buttonToolBar->addItem(
            new Button(Translation::get('Today'), new FontAwesomeGlyph('home'), $todayUrl, Button::DISPLAY_ICON));

        $buttonToolBar->addItem($buttonGroup);

        $buttonGroup->addButton(
            new Button(
                Translation::get('Previous'),
                new FontAwesomeGlyph('caret-left'),
                $previousUrl,
                Button::DISPLAY_ICON));
        $buttonGroup->addButton(
            new Button(Translation::get('Next'), new FontAwesomeGlyph('caret-right'), $nextUrl, Button::DISPLAY_ICON));

        $buttonToolbarRenderer = new ButtonToolBarRenderer($buttonToolBar);
        return $buttonToolbarRenderer->render();
    }

    /**
     *
     * @param string[] $displayParameters
     * @return string
     */
    protected function determineNavigationUrl($displayParameters)
    {
        $displayParameters[FormatHtmlRenderer::PARAM_TIME] = Calendar::TIME_PLACEHOLDER;

        $redirect = new Redirect($displayParameters);
        return $redirect->getUrl();
    }

    /**
     *
     * @param integer $currentRendererTime
     * @return integer
     */
    abstract protected function getPreviousDisplayTime($currentRendererTime);

    /**
     *
     * @param integer $currentRendererTime
     * @return integer
     */
    abstract protected function getNextDisplayTime($currentRendererTime);
}