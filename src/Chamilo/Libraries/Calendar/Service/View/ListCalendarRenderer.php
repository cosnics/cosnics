<?php
namespace Chamilo\Libraries\Calendar\Service\View;

use Chamilo\Libraries\Calendar\Event\Event;
use Chamilo\Libraries\Calendar\Service\Event\EventListRenderer;
use Chamilo\Libraries\Calendar\Service\View\Table\CalendarTable;
use Chamilo\Libraries\Format\Display;
use Chamilo\Libraries\Format\Structure\ActionBar\AbstractButton;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar;
use Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Translation\Translation;

/**
 *
 * @package Chamilo\Libraries\Calendar\Renderer\Type\View
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ListCalendarRenderer extends SidebarCalendarRenderer
{

    protected function getEndTime(): int
    {
        return strtotime('+6 Months', $this->getStartTime());
    }

    /**
     * @return \Chamilo\Libraries\Calendar\Event\Event[][]
     */
    public function getEvents(int $startTime, int $endTime): array
    {
        $events = parent::getEvents($startTime, $endTime);

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

    protected function getStartTime(): int
    {
        return $this->getDisplayTime();
    }

    public function orderEvents(Event $eventLeft, Event $eventRight): int
    {
        return strcmp($eventLeft->getStartDate(), $eventRight->getStartDate());
    }

    /**
     * @throws \ReflectionException
     * @throws \Exception
     */
    public function renderFullCalendar(): string
    {
        $events = $this->getEvents($this->getStartTime(), $this->getEndTime());

        $html = [];

        if (count($events) > 0)
        {
            $html[] = '<div class="table-calendar table-calendar-list">';

            foreach ($events as $dateKey => $dateEvents)
            {
                $hiddenEvents = 0;

                foreach ($dateEvents as $dateEvent)
                {
                    if (!$this->isSourceVisible($dateEvent->getSource()))
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
                    $eventRendererFactory = new EventListRenderer($this, $dateEvent);

                    $html[] = '<li class="list-group-item ">';
                    $html[] = $eventRendererFactory->render();
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
            $html[] = Display::normal_message(Translation::get('NoUpcomingEvents'));
        }

        return implode('', $html);
    }

    /**
     * @throws \ReflectionException
     */
    public function renderNavigation(): string
    {
        $urlFormat = $this->determineNavigationUrl();
        $todayUrl = str_replace(CalendarTable::TIME_PLACEHOLDER, time(), $urlFormat);

        $buttonToolBar = new ButtonToolBar();

        $buttonToolBar->addItem(
            new Button(Translation::get('Today'), new FontAwesomeGlyph('home'), $todayUrl, AbstractButton::DISPLAY_ICON)
        );

        $buttonToolbarRenderer = new ButtonToolBarRenderer($buttonToolBar);

        return $buttonToolbarRenderer->render();
    }

    public function renderTitle(): string
    {
        return date('d M Y', $this->getStartTime()) . ' - ' . date('d M Y', $this->getEndTime());
    }
}
