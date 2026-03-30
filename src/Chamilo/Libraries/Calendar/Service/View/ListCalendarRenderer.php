<?php
namespace Chamilo\Libraries\Calendar\Service\View;

use Chamilo\Libraries\Architecture\Enum\DisplayTypeEnum;
use Chamilo\Libraries\Calendar\Architecture\Trait\AgendaCalendarTrait;
use Chamilo\Libraries\Calendar\Service\Event\EventListRenderer;
use Chamilo\Libraries\Calendar\Service\JumpBarRenderer;
use Chamilo\Libraries\Calendar\Service\LegendRenderer;
use Chamilo\Libraries\Calendar\Service\TableBuilder\CalendarTableBuilder;
use Chamilo\Libraries\Filesystem\Service\WebPathBuilder;
use Chamilo\Libraries\Service\Resource\ResourceManager;
use Chamilo\Libraries\Service\Routing\UrlGenerator;
use Chamilo\Libraries\Service\Utilities\StringUtilities;
use Chamilo\Libraries\UserInterface\Alert\Service\AlertRenderer;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Domain\Button;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Domain\ButtonToolBar;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Service\ButtonToolBarRenderer;
use Chamilo\Libraries\UserInterface\Glyph\Architecture\Domain\FontAwesomeGlyph;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Libraries\Calendar\Service\View
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ListCalendarRenderer extends SidebarCalendarRenderer
{
    use AgendaCalendarTrait;

    public function __construct(
        LegendRenderer $legendRenderer, UrlGenerator $urlGenerator, Translator $translator,
        ButtonToolBarRenderer $buttonToolBarRenderer, JumpBarRenderer $jumpBarRenderer,
        MiniMonthCalendarRenderer $miniMonthCalendarRenderer, ResourceManager $resourceManager,
        WebPathBuilder $webPathBuilder, protected AlertRenderer $alertRenderer,
        protected EventListRenderer $eventListRenderer
    )
    {
        parent::__construct(
            $legendRenderer, $urlGenerator, $translator, $buttonToolBarRenderer, $jumpBarRenderer,
            $miniMonthCalendarRenderer, $resourceManager, $webPathBuilder
        );
    }

    protected function getEndTime(int $displayTime): int
    {
        return strtotime('+6 Months', $displayTime);
    }

    /**
     * @throws \QuickformException
     * @throws \Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchClassException
     */
    public function renderNavigation(array $displayParameters, int $displayTime): string
    {
        $urlFormat = $this->determineNavigationUrl($displayParameters);
        $todayUrl = str_replace(CalendarTableBuilder::TIME_PLACEHOLDER, (string) time(), $urlFormat);

        $buttonToolBar = new ButtonToolBar();

        $buttonToolBar->addButton(
            new Button(
                $this->translator->trans('Today', [], StringUtilities::LIBRARIES), new FontAwesomeGlyph('home'),
                $todayUrl, DisplayTypeEnum::ICON
            )
        );

        return $this->buttonToolBarRenderer->render($buttonToolBar);
    }
}
