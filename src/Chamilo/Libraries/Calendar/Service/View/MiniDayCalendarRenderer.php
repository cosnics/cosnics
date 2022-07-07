<?php
namespace Chamilo\Libraries\Calendar\Service\View;

use Chamilo\Libraries\Architecture\Application\Routing\UrlGenerator;
use Chamilo\Libraries\Calendar\Architecture\Interfaces\CalendarRendererProviderInterface;
use Chamilo\Libraries\Calendar\Service\Event\EventDayRenderer;
use Chamilo\Libraries\Calendar\Service\LegendRenderer;
use Chamilo\Libraries\Calendar\Service\View\TableBuilder\DayCalendarTableBuilder;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Libraries\Calendar\Service\View
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class MiniDayCalendarRenderer extends MiniCalendarRenderer
{
    protected DayCalendarTableBuilder $dayCalendarTableBuilder;

    protected EventDayRenderer $eventDayRenderer;

    public function __construct(
        LegendRenderer $legendRenderer, UrlGenerator $urlGenerator, Translator $translator,
        EventDayRenderer $eventDayRenderer, DayCalendarTableBuilder $dayCalendarTableBuilder
    )
    {
        parent::__construct($legendRenderer, $urlGenerator, $translator);

        $this->eventDayRenderer = $eventDayRenderer;
        $this->dayCalendarTableBuilder = $dayCalendarTableBuilder;
    }

    /**
     * @throws \Exception
     */
    public function render(CalendarRendererProviderInterface $dataProvider, int $displayTime, array $viewActions = []
    ): string
    {
        $html = [];
        $html[] = $this->renderFullCalendar($dataProvider, $displayTime);
        $html[] = $this->getLegendRenderer()->render($dataProvider);

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

    /**
     * @throws \ReflectionException
     * @throws \Exception
     */
    public function renderFullCalendar(CalendarRendererProviderInterface $dataProvider, int $displayTime): string
    {
        $calendarTableBuilder = $this->getDayCalendarTableBuilder();

        $startTime = $calendarTableBuilder->getTableStartTime($displayTime);
        $endTime = $calendarTableBuilder->getTableEndTime($displayTime);

        $events = $this->getEvents($dataProvider, $startTime, $endTime);

        $tableDate = $startTime;
        $eventsToShow = [];

        while ($tableDate <= $endTime)
        {
            $nextTableDate = strtotime('+' . $calendarTableBuilder->getHourStep() . ' Hours', $tableDate);

            foreach ($events as $event)
            {
                $startDate = $event->getStartDate();
                $endDate = $event->getEndDate();

                if ($tableDate < $startDate && $startDate < $nextTableDate ||
                    $tableDate < $endDate && $endDate < $nextTableDate ||
                    $startDate <= $tableDate && $nextTableDate <= $endDate)
                {
                    $eventsToShow[$tableDate][] = $this->getEventDayRenderer()->render(
                        $event, $tableDate, $nextTableDate, $this->isEventSourceVisible($dataProvider, $event)

                    );
                }
            }

            $tableDate = $nextTableDate;
        }

        return $calendarTableBuilder->render($displayTime, $eventsToShow, ['table-calendar-mini']);
    }
}