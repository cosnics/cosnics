<?php
namespace Chamilo\Libraries\Calendar\View\Renderer;

use Chamilo\Libraries\Format\Structure\ActionBar\ButtonGroup;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar;
use Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer;
use Chamilo\Libraries\Calendar\Format\Renderer\ViewRenderer;
use Chamilo\Libraries\Format\Structure\ActionBar\DropdownButton;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Format\Structure\ActionBar\SubButton;
use Chamilo\Libraries\Calendar\Format\HtmlTable\Calendar;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Utilities\Utilities;

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

    /**
     *
     * @var \Chamilo\Libraries\Calendar\Format\Renderer\Type\MiniMonthRenderer
     */
    private $miniMonthFormatHtmlTableRenderer;

    /**
     *
     * @param \Twig_Environment $twigEnvironment
     * @param \Chamilo\Libraries\Calendar\Format\Renderer\HtmlTableRenderer $formatHtmlTableRenderer
     * @param \Chamilo\Libraries\Calendar\Format\Renderer\Type\MiniMonthRenderer $miniMonthFormatHtmlTableRenderer
     */
    public function __construct(\Twig_Environment $twigEnvironment,
        \Chamilo\Libraries\Calendar\Format\Renderer\HtmlTableRenderer $formatHtmlTableRenderer,
        \Chamilo\Libraries\Calendar\Format\Renderer\Type\MiniMonthRenderer $miniMonthFormatHtmlTableRenderer)

    {
        $this->twigEnvironment = $twigEnvironment;
        $this->formatHtmlTableRenderer = $formatHtmlTableRenderer;
        $this->miniMonthFormatHtmlTableRenderer = $miniMonthFormatHtmlTableRenderer;
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
     * @return \Chamilo\Libraries\Calendar\Format\Renderer\Type\MiniMonthRenderer
     */
    protected function getMiniMonthFormatHtmlTableRenderer()
    {
        return $this->miniMonthFormatHtmlTableRenderer;
    }

    /**
     *
     * @return string
     */
    public function render($viewActions)
    {
        return $this->getTwigEnvironment()->render(
            'Chamilo\Libraries\Calendar:HtmlTable.html.twig',
            [
                'baseCalendar' => $this->getFormatHtmlTableRenderer()->render(),
                'miniMonthCalendar' => $this->getMiniMonthFormatHtmlTableRenderer()->render(),
                'actions' => $this->renderViewActions($viewActions),
                'navigation' => $this->renderNavigation(),
                'title' => Translation::get(date('F', time()) . 'Long', null, Utilities::COMMON_LIBRARIES) . ' ' .
                     date('Y', time())]);
    }

    /**
     *
     * @return string
     */
    protected function renderViewActions($viewActions)
    {
        $buttonToolBar = new ButtonToolBar();
        $buttonGroup = new ButtonGroup();

        foreach ($viewActions as $viewAction)
        {
            $buttonToolBar->addItem($viewAction);
        }

        $buttonToolBar->addItem($this->renderTypeButton());

        $buttonToolbarRenderer = new ButtonToolBarRenderer($buttonToolBar);

        return $buttonToolbarRenderer->render();
    }

    /**
     *
     * @return \Chamilo\Libraries\Format\Structure\ActionBar\DropdownButton
     */
    protected function renderTypeButton()
    {
        $rendererTypes = array(ViewRenderer::TYPE_MONTH, ViewRenderer::TYPE_WEEK, ViewRenderer::TYPE_DAY);

        // $displayParameters = $this->getDataProvider()->getDisplayParameters();
        $currentRendererType = $displayParameters[ViewRenderer::PARAM_TYPE];

        $button = new DropdownButton(Translation::get($currentRendererType . 'View'), new FontAwesomeGlyph('calendar'));
        $button->setDropdownClasses('dropdown-menu-right');

        foreach ($rendererTypes as $rendererType)
        {
            $displayParameters[ViewRenderer::PARAM_TYPE] = $rendererType;
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
     * @return string
     */
    protected function renderNavigation()
    {
        $urlFormat = $this->determineNavigationUrl();

        // $previousTime = $this->getPreviousDisplayTime();
        // $nextTime = $this->getNextDisplayTime();

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
     * @return string
     */
    protected function determineNavigationUrl()
    {
        // $parameters = $this->getDataProvider()->getDisplayParameters();
        $parameters[ViewRenderer::PARAM_TIME] = Calendar::TIME_PLACEHOLDER;

        $redirect = new Redirect($parameters);
        return $redirect->getUrl();
    }
}

