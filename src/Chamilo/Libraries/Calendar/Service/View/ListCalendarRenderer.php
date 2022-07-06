<?php
namespace Chamilo\Libraries\Calendar\Service\View;

use Chamilo\Libraries\Architecture\Application\Routing\UrlGenerator;
use Chamilo\Libraries\Calendar\Architecture\Interfaces\CalendarRendererProviderInterface;
use Chamilo\Libraries\Calendar\Architecture\Traits\AgendaCalendarTrait;
use Chamilo\Libraries\Calendar\Service\Event\EventListRenderer;
use Chamilo\Libraries\Calendar\Service\LegendRenderer;
use Chamilo\Libraries\Calendar\Service\View\TableBuilder\CalendarTableBuilder;
use Chamilo\Libraries\Format\Structure\ActionBar\AbstractButton;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar;
use Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Utilities\StringUtilities;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Libraries\Calendar\Service\View
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ListCalendarRenderer extends SidebarCalendarRenderer
{
    use AgendaCalendarTrait;

    protected EventListRenderer $eventListRenderer;

    public function __construct(
        LegendRenderer $legendRenderer, UrlGenerator $urlGenerator, Translator $translator,
        MiniMonthCalendarRenderer $miniMonthCalendarRenderer, EventListRenderer $eventListRenderer
    )
    {
        parent::__construct($legendRenderer, $urlGenerator, $translator, $miniMonthCalendarRenderer);

        $this->eventListRenderer = $eventListRenderer;
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
     * @throws \ReflectionException
     */
    public function renderNavigation(CalendarRendererProviderInterface $dataProvider, int $displayTime): string
    {
        $urlFormat = $this->determineNavigationUrl($dataProvider);
        $todayUrl = str_replace(CalendarTableBuilder::TIME_PLACEHOLDER, time(), $urlFormat);

        $buttonToolBar = new ButtonToolBar();

        $buttonToolBar->addItem(
            new Button(
                $this->getTranslator()->trans('Today', [], StringUtilities::LIBRARIES), new FontAwesomeGlyph('home'),
                $todayUrl, AbstractButton::DISPLAY_ICON
            )
        );

        $buttonToolbarRenderer = new ButtonToolBarRenderer($buttonToolBar);

        return $buttonToolbarRenderer->render();
    }

}
