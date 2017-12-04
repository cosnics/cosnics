<?php
namespace Chamilo\Libraries\Calendar\View\Renderer;

use Chamilo\Libraries\Calendar\Format\HtmlTable\Calendar;
use Chamilo\Libraries\Calendar\Format\Renderer\FormatHtmlRenderer;
use Chamilo\Libraries\Calendar\Format\Renderer\FormatHtmlTableRenderer;
use Chamilo\Libraries\Calendar\Format\Renderer\Type\MiniMonthRenderer;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonGroup;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar;
use Chamilo\Libraries\Format\Structure\ActionBar\DropdownButton;
use Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer;
use Chamilo\Libraries\Format\Structure\ActionBar\SubButton;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 *
 * @package Chamilo\Libraries\Calendar\View\Renderer
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ViewHtmlTableRenderer
{

    /**
     *
     * @var \Twig_Environment
     */
    private $twigEnvironment;

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
     * @param \Twig_Environment $twigEnvironment
     * @param \Chamilo\Libraries\Calendar\Format\Renderer\FormatHtmlTableRenderer $formatRenderer
     * @param \Chamilo\Libraries\Calendar\Format\Renderer\Type\MiniMonthRenderer $miniMonthRenderer
     */
    public function __construct(\Twig_Environment $twigEnvironment, FormatHtmlTableRenderer $formatRenderer,
        MiniMonthRenderer $miniMonthRenderer)

    {
        $this->twigEnvironment = $twigEnvironment;
        $this->formatRenderer = $formatRenderer;
        $this->miniMonthRenderer = $miniMonthRenderer;
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
     * @return string
     */
    public function render($viewActions)
    {
        return $this->getTwigEnvironment()->render(
            'Chamilo\Libraries\Calendar:HtmlTable.html.twig',
            [
                'baseCalendar' => $this->getFormatRenderer()->render(),
                'miniMonthCalendar' => $this->getMiniMonthRenderer()->render(),
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
        $rendererTypes = array(
            FormatHtmlRenderer::TYPE_MONTH,
            FormatHtmlRenderer::TYPE_WEEK,
            FormatHtmlRenderer::TYPE_DAY);

        // $displayParameters = $this->getDataProvider()->getDisplayParameters();
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
        $parameters[FormatHtmlRenderer::PARAM_TIME] = Calendar::TIME_PLACEHOLDER;

        $redirect = new Redirect($parameters);
        return $redirect->getUrl();
    }
}

