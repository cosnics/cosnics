<?php
namespace Chamilo\Libraries\Calendar\Service\View;

use Chamilo\Libraries\Calendar\Architecture\Interfaces\CalendarRendererProviderInterface;
use Chamilo\Libraries\Calendar\Service\View\TableBuilder\CalendarTableBuilder;
use Chamilo\Libraries\Format\Structure\ActionBar\AbstractButton;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonGroup;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar;
use Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 * @package Chamilo\Libraries\Calendar\Service\View
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
abstract class SidebarTableCalendarRenderer extends SidebarCalendarRenderer
{

    abstract public function getNextDisplayTime(int $displayTime): int;

    abstract public function getPreviousDisplayTime(int $displayTime): int;

    public function renderNavigation(CalendarRendererProviderInterface $dataProvider, int $displayTime): string
    {
        $urlFormat = $this->determineNavigationUrl($dataProvider);
        $translator = $this->getTranslator();

        $previousTime = $this->getPreviousDisplayTime($displayTime);
        $nextTime = $this->getNextDisplayTime($displayTime);

        $todayUrl = str_replace(CalendarTableBuilder::TIME_PLACEHOLDER, time(), $urlFormat);
        $previousUrl = str_replace(CalendarTableBuilder::TIME_PLACEHOLDER, $previousTime, $urlFormat);
        $nextUrl = str_replace(CalendarTableBuilder::TIME_PLACEHOLDER, $nextTime, $urlFormat);

        $buttonToolBar = new ButtonToolBar();
        $buttonGroup = new ButtonGroup();

        $buttonToolBar->addItem(
            new Button(
                $translator->trans('Today', [], StringUtilities::LIBRARIES), new FontAwesomeGlyph('home'), $todayUrl,
                AbstractButton::DISPLAY_ICON
            )
        );

        $buttonToolBar->addItem($buttonGroup);

        $buttonGroup->addButton(
            new Button(
                $translator->trans('Previous', [], StringUtilities::LIBRARIES), new FontAwesomeGlyph('caret-left'),
                $previousUrl, AbstractButton::DISPLAY_ICON
            )
        );
        $buttonGroup->addButton(
            new Button(
                $translator->trans('Next', [], StringUtilities::LIBRARIES), new FontAwesomeGlyph('caret-right'),
                $nextUrl, AbstractButton::DISPLAY_ICON
            )
        );

        $buttonToolbarRenderer = new ButtonToolBarRenderer($buttonToolBar);

        return $buttonToolbarRenderer->render();
    }
}