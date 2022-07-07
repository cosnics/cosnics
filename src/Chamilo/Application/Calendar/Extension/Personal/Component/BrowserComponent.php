<?php
namespace Chamilo\Application\Calendar\Extension\Personal\Component;

use Chamilo\Application\Calendar\Extension\Personal\Manager;
use Chamilo\Application\Calendar\Extension\Personal\Service\CalendarRendererDataProvider;
use Chamilo\Libraries\Calendar\Architecture\Factory\HtmlCalendarRendererFactory;
use Chamilo\Libraries\Calendar\Architecture\Interfaces\CalendarRendererDataProviderInterface;
use Chamilo\Libraries\Calendar\Architecture\Traits\CurrentCalendarRendererTrait;
use Symfony\Component\HttpFoundation\Response;

/**
 * @package Chamilo\Application\Calendar\Extension\Personal\Component
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class BrowserComponent extends Manager
{
    use CurrentCalendarRendererTrait;

    /**
     * @throws \Exception
     */
    public function run()
    {
        $html = [];

        $html[] = $this->renderHeader();
        $html[] = '<div class="row">';
        $html[] = $this->renderCalendar();
        $html[] = '</div>';
        $html[] = $this->renderFooter();

        return new Response(implode(PHP_EOL, $html));
    }

    protected function getCalendarRendererDataProvider(): CalendarRendererDataProviderInterface
    {
        return $this->getService(CalendarRendererDataProvider::class);
    }

    protected function getCalendarRendererFactory(): HtmlCalendarRendererFactory
    {
        return $this->getService(HtmlCalendarRendererFactory::class);
    }

    /**
     * @throws \Exception
     */
    protected function renderCalendar(): string
    {
        $renderer = $this->getCalendarRendererFactory()->getRenderer($this->getCurrentCalendarRendererType());

        return $renderer->render(
            $this->getCalendarRendererDataProvider(), $this->getCurrentCalendartRendererTime()
        );
    }
}
