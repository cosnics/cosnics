<?php
namespace Chamilo\Libraries\Calendar\Renderer\Type\View;

use Chamilo\Libraries\Calendar\Table\Calendar;
use Chamilo\Libraries\Format\Structure\ActionBar\BootstrapGlyph;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonGroup;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar;
use Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer;
use Chamilo\Libraries\Platform\Translation;

/**
 *
 * @package Chamilo\Libraries\Calendar\Renderer\Type\View
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
abstract class FullTableRenderer extends TableRenderer
{

    /**
     *
     * @return string
     */
    public function renderViewActions()
    {
        $buttonToolBar = new ButtonToolBar();
        $buttonGroup = new ButtonGroup();

        $buttonToolBar->addItem($this->renderTypeButton());

        foreach ($this->getViewActions() as $viewAction)
        {
            $buttonToolBar->addItem($viewAction);
        }

        $buttonToolbarRenderer = new ButtonToolBarRenderer($buttonToolBar);

        return $buttonToolbarRenderer->render();
    }

    /**
     *
     * @return string
     */
    public function renderMiniMonth()
    {
        $renderer = new MiniMonthRenderer(
            $this->getDataProvider(),
            $this->getLegend(),
            $this->getDisplayTime(),
            null,
            null);

        return $renderer->render();
    }

    /**
     * Adds a navigation bar to the calendar
     *
     * @param string $urlFormat The *TIME* in this string will be replaced by a timestamp
     */
    public function renderNavigation()
    {
        $urlFormat = $this->determineNavigationUrl();
        $previousTime = strtotime('-1 Month', $this->getDisplayTime());
        $nextTime = strtotime('+1 Month', $this->getDisplayTime());

        $todayUrl = str_replace(Calendar :: TIME_PLACEHOLDER, time(), $urlFormat);
        $previousUrl = str_replace(Calendar :: TIME_PLACEHOLDER, $previousTime, $urlFormat);
        $nextUrl = str_replace(Calendar :: TIME_PLACEHOLDER, $nextTime, $urlFormat);

        $buttonToolBar = new ButtonToolBar();
        $buttonGroup = new ButtonGroup();

        $buttonToolBar->addItem(
            new Button(Translation :: get('Today'), new BootstrapGlyph('home'), $todayUrl, Button :: DISPLAY_ICON));

        $buttonToolBar->addItem($buttonGroup);

        $buttonGroup->addButton(
            new Button(
                Translation :: get('Previous'),
                new BootstrapGlyph('triangle-left'),
                $previousUrl,
                Button :: DISPLAY_ICON));
        $buttonGroup->addButton(
            new Button(
                Translation :: get('Next'),
                new BootstrapGlyph('triangle-right'),
                $nextUrl,
                Button :: DISPLAY_ICON));

        $buttonToolbarRenderer = new ButtonToolBarRenderer($buttonToolBar);
        return $buttonToolbarRenderer->render();
    }

    /**
     *
     * @return string
     */
    abstract public function renderFullCalendar();

    /**
     *
     * @return string
     */
    abstract public function renderTitle();

    /**
     *
     * @see \Chamilo\Libraries\Calendar\Renderer\Renderer::render()
     */
    public function render()
    {
        $html = array();

        $html[] = '<div class="col-md-9 col-lg-10 table-calendar-main">';

        $html[] = '<div class="row">';
        $html[] = '<div class="col-md-4">';
        $html[] = '<div class="pull-left">';
        $html[] = $this->renderNavigation();
        $html[] = '</div>';

        $html[] = '<div class="table-calendar-current-time pull-left">';
        $html[] = '<h4>';
        $html[] = $this->renderTitle();
        $html[] = '</h4>';
        $html[] = '</div>';
        $html[] = '</div>';

        $html[] = '<div class="col-md-8">';
        $html[] = '<div class="pull-right">';
        $html[] = $this->renderViewActions();
        $html[] = '</div>';
        $html[] = '</div>';
        $html[] = '</div>';

        $html[] = $this->renderFullCalendar();
        $html[] = '</div>';

        $html[] = '<div class="col-md-3 col-lg-2 table-calendar-sidebar">';
        $html[] = $this->renderMiniMonth();
        $html[] = $this->getLegend()->render();
        $html[] = '</div>';

        $html[] = '<div class="clearfix"></div>';

        return implode(PHP_EOL, $html);
    }
}