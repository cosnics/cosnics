<?php
namespace Chamilo\Libraries\Calendar\Service\View;

use Chamilo\Libraries\Calendar\Architecture\Domain\CalendarTableConfiguration;
use Chamilo\Libraries\Calendar\Architecture\Trait\AgendaCalendarTrait;
use Chamilo\Libraries\Calendar\Service\Event\EventListRenderer;
use Chamilo\Libraries\Calendar\Service\LegendRenderer;
use Chamilo\Libraries\Service\Routing\UrlGenerator;
use Chamilo\Libraries\UserInterface\Alert\Service\AlertRenderer;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Service\ButtonToolBarRenderer;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Libraries\Calendar\Service\View
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class MiniListCalendarRenderer extends MiniCalendarRenderer
{
    use AgendaCalendarTrait;

    public function __construct(
        LegendRenderer $legendRenderer, UrlGenerator $urlGenerator, Translator $translator,
        ButtonToolBarRenderer $buttonToolBarRenderer, protected EventListRenderer $eventListRenderer,
        protected AlertRenderer $alertRenderer
    )
    {
        parent::__construct($legendRenderer, $urlGenerator, $translator, $buttonToolBarRenderer);
    }

    /**
     * @param \Chamilo\Libraries\Calendar\Architecture\Domain\Event[] $events
     * @param \Chamilo\Libraries\Calendar\Architecture\Domain\Visibility[] $invisibleSources
     *
     * @throws \Exception
     */
    public function render(
        array $events, CalendarTableConfiguration $calendarTableConfiguration, array $displayParameters,
        int $displayTime, array $viewActions = [], array $invisibleSources = [], ?string $invisibilityContext = null
    ): string
    {
        $html = [];

        $html[] = '<h4>';
        $html[] = $this->renderTitle($calendarTableConfiguration, $displayTime);
        $html[] = '</h4>';

        $html[] = $this->renderFullCalendar($calendarTableConfiguration, $events, $displayParameters, $displayTime);
        $html[] = $this->legendRenderer->render($invisibleSources, $invisibilityContext);

        $html[] = '<div class="clearfix"></div>';

        return implode(PHP_EOL, $html);
    }

    protected function getEndTime(int $displayTime): int
    {
        return strtotime('+3 Days', $displayTime);
    }
}
