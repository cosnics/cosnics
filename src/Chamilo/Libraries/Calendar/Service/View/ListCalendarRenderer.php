<?php
namespace Chamilo\Libraries\Calendar\Service\View;

use Chamilo\Libraries\Calendar\Architecture\Trait\AgendaCalendarTrait;
use Chamilo\Libraries\Calendar\Service\Event\EventListRenderer;
use Chamilo\Libraries\Calendar\Service\JumpBarRenderer;
use Chamilo\Libraries\Calendar\Service\LegendRenderer;
use Chamilo\Libraries\Calendar\Service\TableBuilder\CalendarTableBuilder;
use Chamilo\Libraries\Filesystem\Service\WebPathBuilder;
use Chamilo\Libraries\Service\Resource\ResourceManager;
use Chamilo\Libraries\Service\Routing\UrlGenerator;
use Chamilo\Libraries\Service\Utilities\StringUtilities;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Domain\Button;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Domain\ButtonToolBar;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Interface\ButtonDisplayInterface;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Service\ButtonToolBarRenderer;
use Chamilo\Libraries\UserInterface\Glyph\Architecture\Domain\FontAwesomeGlyph;
use Chamilo\Libraries\UserInterface\NotificationMessage\Service\NotificationMessageRenderer;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Libraries\Calendar\Service\View
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ListCalendarRenderer extends SidebarCalendarRenderer
{
    use AgendaCalendarTrait;

    protected EventListRenderer $eventListRenderer;

    protected NotificationMessageRenderer $notificationMessageRenderer;

    public function __construct(
        LegendRenderer $legendRenderer, UrlGenerator $urlGenerator, Translator $translator,
        MiniMonthCalendarRenderer $miniMonthCalendarRenderer, EventListRenderer $eventListRenderer,
        WebPathBuilder $webPathBuilder, ResourceManager $resourceManager, JumpBarRenderer $jumpBarRenderer,
        NotificationMessageRenderer $notificationMessageRenderer, ButtonToolBarRenderer $buttonToolBarRenderer
    )
    {
        parent::__construct(
            $legendRenderer, $urlGenerator, $translator, $miniMonthCalendarRenderer, $webPathBuilder, $resourceManager,
            $jumpBarRenderer, $buttonToolBarRenderer
        );

        $this->eventListRenderer = $eventListRenderer;
        $this->notificationMessageRenderer = $notificationMessageRenderer;
    }

    protected function getEndTime(int $displayTime): int
    {
        return strtotime('+6 Months', $displayTime);
    }

    public function getEventListRenderer(): EventListRenderer
    {
        return $this->eventListRenderer;
    }

    public function getNotificationMessageRenderer(): NotificationMessageRenderer
    {
        return $this->notificationMessageRenderer;
    }

    /**
     * @throws \QuickformException
     * @throws \Chamilo\Libraries\Architecture\Exception\ClassNotExistException
     */
    public function renderNavigation(array $displayParameters, int $displayTime): string
    {
        $urlFormat = $this->determineNavigationUrl($displayParameters);
        $todayUrl = str_replace(CalendarTableBuilder::TIME_PLACEHOLDER, (string) time(), $urlFormat);

        $buttonToolBar = new ButtonToolBar();

        $buttonToolBar->addButton(
            new Button(
                $this->getTranslator()->trans('Today', [], StringUtilities::LIBRARIES), new FontAwesomeGlyph('home'),
                $todayUrl, ButtonDisplayInterface::DISPLAY_ICON
            )
        );

        return $this->getButtonToolBarRenderer()->render($buttonToolBar);
    }
}
