<?php
namespace Chamilo\Libraries\Calendar\Architecture\Traits;

use Chamilo\Libraries\Calendar\Architecture\Interfaces\ActionSupport;
use Chamilo\Libraries\Calendar\Architecture\Interfaces\CalendarRendererProviderInterface;
use Chamilo\Libraries\Calendar\Event\Event;
use Chamilo\Libraries\Calendar\Service\Event\EventListRenderer;
use Chamilo\Libraries\Format\Display;

/**
 * @package Chamilo\Libraries\Calendar\Architecture\Traits
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
trait AgendaCalendarTrait
{

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

    /**
     * @return \Chamilo\Libraries\Calendar\Event\Event[][]
     */
    public function getAgendaEvents(CalendarRendererProviderInterface $dataProvider, int $startTime, int $endTime
    ): array
    {
        $events = $this->getEvents($dataProvider, $startTime, $endTime);

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

    abstract protected function getEndTime(int $displayTime): int;

    abstract public function getEventListRenderer(): EventListRenderer;

    /**
     * @return \Chamilo\Libraries\Calendar\Event\Event[]
     */
    abstract public function getEvents(CalendarRendererProviderInterface $dataProvider, int $startTime, int $endTime
    ): array;

    abstract public function isEventSourceVisible(CalendarRendererProviderInterface $dataProvider, Event $event): bool;

    abstract public function isSourceVisible(
        CalendarRendererProviderInterface $dataProvider, string $source, ?int $userIdentifier = null
    ): bool;

    public function orderEvents(Event $eventLeft, Event $eventRight): int
    {
        return strcmp($eventLeft->getStartDate(), $eventRight->getStartDate());
    }

    /**
     * @throws \Exception
     */
    public function renderFullCalendar(CalendarRendererProviderInterface $dataProvider, int $displayTime): string
    {
        $events = $this->getAgendaEvents($dataProvider, $displayTime, $this->getEndTime($displayTime));

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

    public function renderTitle(CalendarRendererProviderInterface $dataProvider, int $displayTime): string
    {
        return date('d M Y', $displayTime) . ' - ' . date('d M Y', $this->getEndTime($displayTime));
    }
}
