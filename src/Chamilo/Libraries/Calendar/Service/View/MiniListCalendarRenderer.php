<?php
namespace Chamilo\Libraries\Calendar\Service\View;

use Chamilo\Libraries\Architecture\Application\Routing\UrlGenerator;
use Chamilo\Libraries\Calendar\Architecture\Interfaces\CalendarRendererProviderInterface;
use Chamilo\Libraries\Calendar\Architecture\Traits\AgendaCalendarTrait;
use Chamilo\Libraries\Calendar\Service\Event\EventListRenderer;
use Chamilo\Libraries\Calendar\Service\LegendRenderer;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Libraries\Calendar\Service\View
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class MiniListCalendarRenderer extends MiniCalendarRenderer
{
    use AgendaCalendarTrait;

    protected EventListRenderer $eventListRenderer;

    public function __construct(
        LegendRenderer $legendRenderer, UrlGenerator $urlGenerator, Translator $translator,
        EventListRenderer $eventListRenderer
    )
    {
        parent::__construct($legendRenderer, $urlGenerator, $translator);
        $this->eventListRenderer = $eventListRenderer;
    }

    /**
     * @throws \Exception
     */
    public function render(CalendarRendererProviderInterface $dataProvider, int $displayTime, array $viewActions = []
    ): string
    {
        $html = [];

        $html[] = '<h4>';
        $html[] = $this->renderTitle($dataProvider, $displayTime);
        $html[] = '</h4>';

        $html[] = $this->renderFullCalendar($dataProvider, $displayTime);
        $html[] = $this->getLegendRenderer()->render($dataProvider);

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
}
