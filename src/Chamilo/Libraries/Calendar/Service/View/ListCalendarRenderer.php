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

    protected AlertRenderer $alertRenderer;

    protected EventListRenderer $eventListRenderer;

    public function __construct(
        LegendRenderer $legendRenderer, UrlGenerator $urlGenerator, Translator $translator,
        MiniMonthCalendarRenderer $miniMonthCalendarRenderer, EventListRenderer $eventListRenderer,
        WebPathBuilder $webPathBuilder, ResourceManager $resourceManager, JumpBarRenderer $jumpBarRenderer,
        AlertRenderer $alertRenderer, ButtonToolBarRenderer $buttonToolBarRenderer
    )
    {
        parent::__construct(
            $legendRenderer, $urlGenerator, $translator, $miniMonthCalendarRenderer, $webPathBuilder, $resourceManager,
            $jumpBarRenderer, $buttonToolBarRenderer
        );

        $this->eventListRenderer = $eventListRenderer;
        $this->alertRenderer = $alertRenderer;
    }

    public function getAlertRenderer(): AlertRenderer
    {
        return $this->alertRenderer;
    }

    protected function getEndTime(int $displayTime): int
    {
        return strtotime('+6 Months', $displayTime);
    }

    public function getEventListRenderer(): EventListRenderer
    {
        return $this->eventListRenderer;
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
                $this->getTranslator()->trans('Today', [], StringUtilities::LIBRARIES), new FontAwesomeGlyph('home'),
                $todayUrl, DisplayTypeEnum::ICON
            )
        );

        return $this->getButtonToolBarRenderer()->render($buttonToolBar);
    }
}
