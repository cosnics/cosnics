<?php
namespace Chamilo\Libraries\Calendar\Renderer\Type\View;

use Chamilo\Libraries\Calendar\Table\Calendar;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonGroup;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar;
use Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Platform\Translation;

/**
 *
 * @package Chamilo\Libraries\Calendar\Renderer\Type\View
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
abstract class FullTableRenderer extends FullRenderer
{

    use \Chamilo\Libraries\Calendar\Renderer\Type\View\TableRenderer;

    /**
     * Adds a navigation bar to the calendar
     *
     * @param string $urlFormat The *TIME* in this string will be replaced by a timestamp
     */
    public function renderNavigation()
    {
        $urlFormat = $this->determineNavigationUrl();

        $previousTime = $this->getPreviousDisplayTime();
        $nextTime = $this->getNextDisplayTime();

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
     * @return integer
     */
    abstract public function getPreviousDisplayTime();

    /**
     *
     * @return integer
     */
    abstract public function getNextDisplayTime();
}