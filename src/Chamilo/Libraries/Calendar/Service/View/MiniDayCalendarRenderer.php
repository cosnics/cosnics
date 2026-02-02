<?php
namespace Chamilo\Libraries\Calendar\Service\View;

use Chamilo\Libraries\Calendar\Architecture\Interface\CalendarRendererProviderInterface;
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
        EventDayRenderer $eventDayRenderer, DayCalendarTableBuilder $dayCalendarTableBuilder, ButtonToolBarRenderer $buttonToolBarRenderer
    )
    {
        parent::__construct($legendRenderer, $urlGenerator, $translator, $buttonToolBarRenderer);

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