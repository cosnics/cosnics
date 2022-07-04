<?php
namespace Chamilo\Libraries\Calendar\Service\View;

use Chamilo\Libraries\Architecture\Application\Routing\UrlGenerator;
use Chamilo\Libraries\Calendar\Architecture\Interfaces\ActionSupport;
use Chamilo\Libraries\Calendar\Architecture\Interfaces\CalendarRendererProviderInterface;
use Chamilo\Libraries\Calendar\Event\Event;
use Chamilo\Libraries\Calendar\Service\Event\EventListRenderer;
use Chamilo\Libraries\Calendar\Service\LegendRenderer;
use Chamilo\Libraries\Calendar\Service\View\Table\CalendarTable;
use Chamilo\Libraries\Format\Display;
use Chamilo\Libraries\Format\Structure\ActionBar\AbstractButton;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar;
use Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Utilities\StringUtilities;
use Symfony\Component\Translation\Translator;

/**
 *
 * @package Chamilo\Libraries\Calendar\Renderer\Type\View
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ListCalendarRenderer extends SidebarCalendarRenderer
{
    protected EventListRenderer $eventListRenderer;

    public function __construct(
        LegendRenderer $legendRenderer, UrlGenerator $urlGenerator, Translator $translator,
        MiniMonthCalendarRenderer $miniMonthCalendarRenderer, EventListRenderer $eventListRenderer
    )
    {
        parent::__construct($legendRenderer, $urlGenerator, $translator, $miniMonthCalendarRenderer);

        $this->eventListRenderer = $eventListRenderer;
    }

    /**
     * @return \Chamilo\Libraries\Format\Structure\ToolbarItem[]
     */
    public function getActions(CalendarRendererProviderInterface $dataProvider, Event $event): array
    {
        if ($dataProvider instanceof ActionSupport)
        {
            return $dataProvider->getEventActions($event);
        }

        return [];
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
     * @return \Chamilo\Libraries\Calendar\Event\Event[][]
     */
    public function getEvents(CalendarRendererProviderInterface $dataProvider, int $startTime, int $endTime): array
    {
        $events = parent::getEvents($dataProvider, $startTime, $endTime);

        $structuredEvents = [];

        foreach ($events as $event)
        {
            $startDate = $event->getStartDate();
            $dateKey = mktime(0, 0, 0, date('n', $startDate), date('j', $startDate), date('Y', $startDate));

            if (!isset($structuredEvents[$dateKey]))
            {
                $structuredEvents[$dateKey] = [];
            }

            $structuredEvents[$dateKey][] = $event;
        }

        ksort($structuredEvents);

        foreach ($structuredEvents as &$dateEvents)
        {
            usort($dateEvents, [$this, 'orderEvents']);
        }

        return $structuredEvents;
    }

    public function orderEvents(Event $eventLeft, Event $eventRight): int
    {
        return strcmp($eventLeft->getStartDate(), $eventRight->getStartDate());
    }

    /**
     * @throws \Exception
     */
    public function renderFullCalendar(CalendarRendererProviderInterface $dataProvider, int $displayTime): string
    {
        $events = $this->getEvents($dataProvider, $displayTime, $this->getEndTime($displayTime));

        $html = [];

        if (count($events) > 0)
        {
            $html[] = '<div class="table-calendar table-calendar-list">';

            foreach ($events as $dateKey => $dateEvents)
            {
                $hiddenEvents = 0;

                foreach ($dateEvents as $dateEvent)
                {
                    if (!$this->isSourceVisible($dataProvider, $dateEvent->getSource()))
                    {
                        $hiddenEvents ++;
                    }
                }

                $allEventsAreHidden = ($hiddenEvents == count($dateEvents));

                $html[] = '<div class="row' . ($allEventsAreHidden ? ' event-container-hidden' : '') . '">';

                $html[] = '<div class="col-xs-12 table-calendar-list-date">';
                $html[] = date('D, d M', $dateKey);
                $html[] = '</div>';

                $html[] = '<div class="col-xs-12 table-calendar-list-events">';
                $html[] = '<ul class="list-group">';

                foreach ($dateEvents as $dateEvent)
                {
                    $html[] = '<li class="list-group-item ">';
                    $html[] = $this->getEventListRenderer()->render(
                        $dateEvent, $this->isEventSourceVisible($dataProvider, $dateEvent),
                        $this->getActions($dataProvider, $dateEvent)
                    );
                    $html[] = '</li>';
                }

                $html[] = '</ul>';
                $html[] = '</div>';

                $html[] = '</div>';
            }

            $html[] = '</div>';
        }
        else
        {
            $html[] = Display::normal_message(
                $this->getTranslator()->trans('NoUpcomingEvents', [], 'Chamilo\Libraries\Calendar')
            );
        }

        return implode('', $html);
    }

    /**
     * @throws \ReflectionException
     */
    public function renderNavigation(CalendarRendererProviderInterface $dataProvider, int $displayTime): string
    {
        $urlFormat = $this->determineNavigationUrl($dataProvider);
        $todayUrl = str_replace(CalendarTable::TIME_PLACEHOLDER, time(), $urlFormat);

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

    public function renderTitle(int $displayTime): string
    {
        return date('d M Y', $displayTime) . ' - ' . date('d M Y', $this->getEndTime($displayTime));
    }
}
