<?php
namespace Chamilo\Libraries\Calendar\Renderer\Type\View;

use Chamilo\Libraries\Calendar\Table\Calendar;
use Chamilo\Libraries\Format\Structure\ActionBar\AbstractButton;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonGroup;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar;
use Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Translation\Translation;

/**
 *
 * @package Chamilo\Libraries\Calendar\Renderer\Type\View
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
abstract class FullTableRenderer extends FullRenderer
{

    use TableRenderer;

    abstract public function getNextDisplayTime(): int;

    abstract public function getPreviousDisplayTime(): int;

    /**
     * @throws \ReflectionException
     */
    public function renderNavigation(): string
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
            new Button(Translation::get('Today'), new FontAwesomeGlyph('home'), $todayUrl, AbstractButton::DISPLAY_ICON)
        );

        $buttonToolBar->addItem($buttonGroup);

        $buttonGroup->addButton(
            new Button(
                Translation::get('Previous'), new FontAwesomeGlyph('caret-left'), $previousUrl,
                AbstractButton::DISPLAY_ICON
            )
        );
        $buttonGroup->addButton(
            new Button(
                Translation::get('Next'), new FontAwesomeGlyph('caret-right'), $nextUrl, AbstractButton::DISPLAY_ICON
            )
        );

        $buttonToolbarRenderer = new ButtonToolBarRenderer($buttonToolBar);

        return $buttonToolbarRenderer->render();
    }
}