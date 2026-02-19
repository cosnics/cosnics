<?php
namespace Chamilo\Libraries\Calendar\Service\View;

use Chamilo\Libraries\Calendar\Architecture\Trait\AgendaCalendarTrait;
use Chamilo\Libraries\Calendar\Service\Event\EventListRenderer;
use Chamilo\Libraries\Calendar\Service\LegendRenderer;
use Chamilo\Libraries\Service\Routing\UrlGenerator;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Service\ButtonToolBarRenderer;
use Chamilo\Libraries\UserInterface\NotificationMessage\Service\NotificationMessageRenderer;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Libraries\Calendar\Service\View
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class MiniListCalendarRenderer extends MiniCalendarRenderer
{
    use AgendaCalendarTrait;

    protected EventListRenderer $eventListRenderer;

    protected NotificationMessageRenderer $notificationMessageRenderer;

    public function __construct(
        LegendRenderer $legendRenderer, UrlGenerator $urlGenerator, Translator $translator,
        EventListRenderer $eventListRenderer, NotificationMessageRenderer $notificationMessageRenderer,
        ButtonToolBarRenderer $buttonToolBarRenderer
    )
    {
        parent::__construct($legendRenderer, $urlGenerator, $translator, $buttonToolBarRenderer);
        $this->eventListRenderer = $eventListRenderer;
        $this->notificationMessageRenderer = $notificationMessageRenderer;
    }

    /**
     * @param \Chamilo\Libraries\Calendar\Architecture\Domain\Event[] $events
     * @param \Chamilo\Libraries\Calendar\Architecture\Domain\Visibility[] $invisibleSources
     *
     * @throws \Exception
     */
    public function render(
        array $events, array $displayParameters, int $displayTime, array $viewActions = [],
        array $invisibleSources = [], ?string $invisibilityContext = null
    ): string
    {
        $html = [];

        $html[] = '<h4>';
        $html[] = $this->renderTitle($displayTime);
        $html[] = '</h4>';

        $html[] = $this->renderFullCalendar($events, $displayParameters, $displayTime);
        $html[] = $this->getLegendRenderer()->render($invisibleSources, $invisibilityContext);

        $html[] = '<div class="clearfix"></div>';

        return implode(PHP_EOL, $html);
    }

    protected function getEndTime(int $displayTime): int
    {
        return strtotime('+3 Days', $displayTime);
    }

    public function getEventListRenderer(): EventListRenderer
    {
        return $this->eventListRenderer;
    }

    public function getNotificationMessageRenderer(): NotificationMessageRenderer
    {
        return $this->notificationMessageRenderer;
    }
}
