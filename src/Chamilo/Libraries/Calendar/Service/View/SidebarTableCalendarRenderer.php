<?php
namespace Chamilo\Libraries\Calendar\Service\View;

use Chamilo\Libraries\Calendar\Architecture\Interface\CalendarRendererProviderInterface;
use Chamilo\Libraries\Calendar\Service\TableBuilder\CalendarTableBuilder;
use Chamilo\Libraries\Service\Utilities\StringUtilities;
use Chamilo\Libraries\UserInterface\ActionBar\Architecture\Domain\Button;
use Chamilo\Libraries\UserInterface\ActionBar\Architecture\Domain\ButtonGroup;
use Chamilo\Libraries\UserInterface\ActionBar\Architecture\Domain\ButtonToolBar;
use Chamilo\Libraries\UserInterface\ActionBar\Architecture\Interface\ButtonDisplayInterface;
use Chamilo\Libraries\UserInterface\ActionBar\Service\ButtonToolBarRenderer;
use Chamilo\Libraries\UserInterface\Glyph\Architecture\Domain\FontAwesomeGlyph;

/**
 * @package Chamilo\Libraries\Calendar\Service\View
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
abstract class SidebarTableCalendarRenderer extends SidebarCalendarRenderer
{

    abstract public function getNextDisplayTime(int $displayTime): int;

    abstract public function getPreviousDisplayTime(int $displayTime): int;

    /**
     * @throws \QuickformException
     */
    public function renderNavigation(CalendarRendererProviderInterface $dataProvider, int $displayTime): string
    {
        $urlFormat = $this->determineNavigationUrl($dataProvider);
        $translator = $this->getTranslator();

        $previousTime = $this->getPreviousDisplayTime($displayTime);
        $nextTime = $this->getNextDisplayTime($displayTime);

        $todayUrl = str_replace(CalendarTableBuilder::TIME_PLACEHOLDER, (string) time(), $urlFormat);
        $previousUrl = str_replace(CalendarTableBuilder::TIME_PLACEHOLDER, (string) $previousTime, $urlFormat);
        $nextUrl = str_replace(CalendarTableBuilder::TIME_PLACEHOLDER, (string) $nextTime, $urlFormat);

        $buttonToolBar = new ButtonToolBar();
        $buttonGroup = new ButtonGroup();

        $buttonToolBar->addButton(
            new Button(
                $translator->trans('Today', [], StringUtilities::LIBRARIES), new FontAwesomeGlyph('home'), $todayUrl,
                ButtonDisplayInterface::DISPLAY_ICON
            )
        );

        $buttonToolBar->addButton($buttonGroup);

        $buttonGroup->addGroupButton(
            new Button(
                $translator->trans('Previous', [], StringUtilities::LIBRARIES), new FontAwesomeGlyph('caret-left'),
                $previousUrl, ButtonDisplayInterface::DISPLAY_ICON
            )
        );
        $buttonGroup->addGroupButton(
            new Button(
                $translator->trans('Next', [], StringUtilities::LIBRARIES), new FontAwesomeGlyph('caret-right'),
                $nextUrl, ButtonDisplayInterface::DISPLAY_ICON
            )
        );

        $buttonToolbarRenderer = new ButtonToolBarRenderer($buttonToolBar);

        return $buttonToolbarRenderer->render();
    }
}