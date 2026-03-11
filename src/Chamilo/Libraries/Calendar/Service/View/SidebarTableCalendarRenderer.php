<?php
namespace Chamilo\Libraries\Calendar\Service\View;

use Chamilo\Libraries\Architecture\Enum\DisplayTypeEnum;
use Chamilo\Libraries\Calendar\Service\TableBuilder\CalendarTableBuilder;
use Chamilo\Libraries\Service\Utilities\StringUtilities;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Domain\Button;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Domain\ButtonGroup;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Domain\ButtonToolBar;
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
     * @throws \Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchClassException
     */
    public function renderNavigation(array $displayParameters, int $displayTime): string
    {
        $urlFormat = $this->determineNavigationUrl($displayParameters);
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
                DisplayTypeEnum::ICON
            )
        );

        $buttonToolBar->addButton($buttonGroup);

        $buttonGroup->addButton(
            new Button(
                $translator->trans('Previous', [], StringUtilities::LIBRARIES), new FontAwesomeGlyph('caret-left'),
                $previousUrl, DisplayTypeEnum::ICON
            )
        );
        $buttonGroup->addButton(
            new Button(
                $translator->trans('Next', [], StringUtilities::LIBRARIES), new FontAwesomeGlyph('caret-right'),
                $nextUrl, DisplayTypeEnum::ICON
            )
        );

        return $this->getButtonToolBarRenderer()->render($buttonToolBar);
    }
}