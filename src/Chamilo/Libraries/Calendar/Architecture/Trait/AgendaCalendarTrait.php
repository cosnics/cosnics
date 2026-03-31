<?php
namespace Chamilo\Libraries\Calendar\Architecture\Trait;

use Chamilo\Libraries\Calendar\Architecture\Domain\CalendarTableConfiguration;
use Chamilo\Libraries\Calendar\Architecture\Domain\Event;
use Chamilo\Libraries\Calendar\Service\Event\EventListRenderer;
use Chamilo\Libraries\UserInterface\Alert\Architecture\Domain\Alert;
use Chamilo\Libraries\UserInterface\Alert\Service\AlertRenderer;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Libraries\Calendar\Architecture\Trait
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
trait AgendaCalendarTrait
{
    protected AlertRenderer $alertRenderer;

    protected EventListRenderer $eventListRenderer;

    protected Translator $translator;

    /**
     * @param \Chamilo\Libraries\Calendar\Architecture\Domain\Event[] $events
     *
     * @return \Chamilo\Libraries\Calendar\Architecture\Domain\Event[][]
     */
    public function getAgendaEvents(array $events): array
    {
        $events = $this->orderEvents($events);

        $structuredEvents = [];

        foreach ($events as $event) {
            $startDate = $event->getStartDate();
            $dateKey =
                mktime(0, 0, 0, (int) date('n', $startDate), (int) date('j', $startDate), (int) date('Y', $startDate));

            if (!isset($structuredEvents[$dateKey])) {
                $structuredEvents[$dateKey] = [];
            }

            $structuredEvents[$dateKey][] = $event;
        }

        ksort($structuredEvents);

        foreach ($structuredEvents as &$dateEvents) {
            usort($dateEvents, [$this, 'orderStructuredEvents']);
        }

        return $structuredEvents;
    }

    abstract protected function getEndTime(int $displayTime): int;

    public function getEventsEndTime(CalendarTableConfiguration $calendarTableConfiguration, int $displayTime): int
    {
        return $this->getEndTime($displayTime);
    }

    public function getEventsStartTime(CalendarTableConfiguration $calendarTableConfiguration, int $displayTime): int
    {
        return $displayTime;
    }

    abstract public function isEventSourceVisible(Event $event, array $invisibleSources = []): bool;

    abstract public function isSourceVisible(string $source, array $invisibleSources = []): bool;

    public function orderStructuredEvents(Event $eventLeft, Event $eventRight): int
    {
        return strcmp((string) $eventLeft->getStartDate(), (string) $eventRight->getStartDate());
    }

    /**
     * @param \Chamilo\Libraries\Calendar\Architecture\Domain\Event[] $events
     *
     * @throws \Exception
     */
    public function renderFullCalendar(
        CalendarTableConfiguration $calendarTableConfiguration, array $events, array $displayParameters,
        int $displayTime, array $invisibleSources = [], ?string $invisibilityContext = null
    ): string
    {
        $events = $this->getAgendaEvents($events);

        $html = [];

        if (count($events) > 0) {
            $html[] = '<div class="table-calendar table-calendar-list">';

            foreach ($events as $dateKey => $dateEvents) {
                $hiddenEvents = 0;

                foreach ($dateEvents as $dateEvent) {
                    if (!$this->isSourceVisible($dateEvent->getSource(), $invisibleSources)) {
                        $hiddenEvents ++;
                    }
                }

                $allEventsAreHidden = ($hiddenEvents == count($dateEvents));

                $html[] = '<div class="row' . ($allEventsAreHidden ? ' event-container-hidden' : '') . '">';

                $html[] = '<div class="col-12 table-calendar-list-date">';
                $html[] = date('D, d M', $dateKey);
                $html[] = '</div>';

                $html[] = '<div class="col-12 table-calendar-list-events">';
                $html[] = '<ul class="list-group">';

                foreach ($dateEvents as $dateEvent) {
                    $html[] = '<li class="list-group-item ">';
                    $html[] = $this->eventListRenderer->render(
                        $dateEvent, $this->isEventSourceVisible($dateEvent, $invisibleSources), $dateEvent->getActions()
                    );
                    $html[] = '</li>';
                }

                $html[] = '</ul>';
                $html[] = '</div>';

                $html[] = '</div>';
            }

            $html[] = '</div>';
        }
        else {
            $html[] = $this->alertRenderer->render(
                new Alert(
                    $this->translator->trans('NoUpcomingEvents', [], 'Chamilo\Libraries')
                )
            );
        }

        return implode('', $html);
    }

    public function renderTitle(CalendarTableConfiguration $calendarTableConfiguration, int $displayTime): string
    {
        return date('d M Y', $displayTime) . ' - ' . date('d M Y', $this->getEndTime($displayTime));
    }
}
