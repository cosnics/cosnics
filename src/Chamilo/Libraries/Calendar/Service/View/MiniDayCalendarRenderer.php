<?php
namespace Chamilo\Libraries\Calendar\Service\View;

use Chamilo\Libraries\Calendar\Service\Event\EventDayRenderer;
use Chamilo\Libraries\Calendar\Service\LegendRenderer;
use Chamilo\Libraries\Calendar\Service\TableBuilder\DayCalendarTableBuilder;
use Chamilo\Libraries\Service\Routing\UrlGenerator;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Service\ButtonToolBarRenderer;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Libraries\Calendar\Service\View
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class MiniDayCalendarRenderer extends MiniCalendarRenderer
{
    protected DayCalendarTableBuilder $dayCalendarTableBuilder;

    protected EventDayRenderer $eventDayRenderer;

    public function __construct(
        LegendRenderer $legendRenderer, UrlGenerator $urlGenerator, Translator $translator,
        EventDayRenderer $eventDayRenderer, DayCalendarTableBuilder $dayCalendarTableBuilder,
        ButtonToolBarRenderer $buttonToolBarRenderer
    )
    {
        parent::__construct($legendRenderer, $urlGenerator, $translator, $buttonToolBarRenderer);

        $this->eventDayRenderer = $eventDayRenderer;
        $this->dayCalendarTableBuilder = $dayCalendarTableBuilder;
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
        $html[] = $this->renderFullCalendar($events, $displayTime, $invisibleSources);
        $html[] = $this->getLegendRenderer()->render($invisibleSources, $invisibilityContext);

        return implode(PHP_EOL, $html);
    }

    public function getDayCalendarTableBuilder(): DayCalendarTableBuilder
    {
        return $this->dayCalendarTableBuilder;
    }

    public function getEventDayRenderer(): EventDayRenderer
    {
        return $this->eventDayRenderer;
    }

    public function getEventsEndTime(int $displayTime): int
    {
        return $this->getDayCalendarTableBuilder()->getTableEndTime($displayTime);
    }

    public function getEventsStartTime(int $displayTime): int
    {
        return $this->getDayCalendarTableBuilder()->getTableStartTime($displayTime);
    }

    /**
     * @param \Chamilo\Libraries\Calendar\Architecture\Domain\Event[] $events
     *
     * @throws \TableException
     * @throws \Exception
     */
    public function renderFullCalendar(
        array $events, int $displayTime, array $invisibleSources = []
    ): string
    {
        $calendarTableBuilder = $this->getDayCalendarTableBuilder();

        $startTime = $this->getEventsStartTime($displayTime);
        $endTime = $this->getEventsEndTime($displayTime);

        $events = $this->orderEvents($events);

        $tableDate = $startTime;
        $eventsToShow = [];

        while ($tableDate <= $endTime) {
            $nextTableDate = strtotime('+' . $calendarTableBuilder->getHourStep() . ' Hours', $tableDate);

            foreach ($events as $event) {
                $startDate = $event->getStartDate();
                $endDate = $event->getEndDate();

                if ($tableDate < $startDate && $startDate < $nextTableDate ||
                    $tableDate < $endDate && $endDate < $nextTableDate ||
                    $startDate <= $tableDate && $nextTableDate <= $endDate) {
                    $eventsToShow[$tableDate][] = $this->getEventDayRenderer()->render(
                        $event, $tableDate, $nextTableDate, $this->isEventSourceVisible($event, $invisibleSources)

                    );
                }
            }

            $tableDate = $nextTableDate;
        }

        return $calendarTableBuilder->render($displayTime, $eventsToShow, ['table-calendar-mini']);
    }
}