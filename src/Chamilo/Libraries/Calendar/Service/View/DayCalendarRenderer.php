<?php
namespace Chamilo\Libraries\Calendar\Service\View;

use Chamilo\Libraries\Calendar\Architecture\Domain\CalendarTableConfiguration;
use Chamilo\Libraries\Calendar\Service\Event\EventDayRenderer;
use Chamilo\Libraries\Calendar\Service\JumpBarRenderer;
use Chamilo\Libraries\Calendar\Service\LegendRenderer;
use Chamilo\Libraries\Calendar\Service\TableBuilder\DayCalendarTableBuilder;
use Chamilo\Libraries\Filesystem\Service\WebPathBuilder;
use Chamilo\Libraries\Service\Resource\ResourceManager;
use Chamilo\Libraries\Service\Routing\UrlGenerator;
use Chamilo\Libraries\Service\Utilities\DatetimeUtilities;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Service\ButtonToolBarRenderer;
use IntlDateFormatter;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Libraries\Calendar\Service\View
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class DayCalendarRenderer extends SidebarTableCalendarRenderer
{
    protected DatetimeUtilities $datetimeUtilities;

    protected DayCalendarTableBuilder $dayCalendarTableBuilder;

    protected EventDayRenderer $eventDayRenderer;

    public function __construct(
        LegendRenderer $legendRenderer, UrlGenerator $urlGenerator, Translator $translator,
        MiniMonthCalendarRenderer $miniMonthCalendarRenderer, DatetimeUtilities $datetimeUtilities,
        EventDayRenderer $eventDayRenderer, DayCalendarTableBuilder $dayCalendarTableBuilder,
        WebPathBuilder $webPathBuilder, ResourceManager $resourceManager, JumpBarRenderer $jumpBarRenderer,
        ButtonToolBarRenderer $buttonToolBarRenderer
    )
    {
        parent::__construct(
            $legendRenderer, $urlGenerator, $translator, $miniMonthCalendarRenderer, $webPathBuilder, $resourceManager,
            $jumpBarRenderer, $buttonToolBarRenderer
        );

        $this->eventDayRenderer = $eventDayRenderer;
        $this->datetimeUtilities = $datetimeUtilities;
        $this->dayCalendarTableBuilder = $dayCalendarTableBuilder;
    }

    public function getDatetimeUtilities(): DatetimeUtilities
    {
        return $this->datetimeUtilities;
    }

    public function getDayCalendarTableBuilder(): DayCalendarTableBuilder
    {
        return $this->dayCalendarTableBuilder;
    }

    public function getEventDayRenderer(): EventDayRenderer
    {
        return $this->eventDayRenderer;
    }

    public function getEventsEndTime(CalendarTableConfiguration $calendarTableConfiguration, int $displayTime): int
    {
        return $this->getDayCalendarTableBuilder()->getTableEndTime($calendarTableConfiguration, $displayTime);
    }

    public function getEventsStartTime(CalendarTableConfiguration $calendarTableConfiguration, int $displayTime): int
    {
        return $this->getDayCalendarTableBuilder()->getTableStartTime($calendarTableConfiguration, $displayTime);
    }

    public function getNextDisplayTime(int $displayTime): int
    {
        return strtotime('+1 Day', $displayTime);
    }

    public function getPreviousDisplayTime(int $displayTime): int
    {
        return strtotime('-1 Day', $displayTime);
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
        $calendarTableBuilder = $this->getDayCalendarTableBuilder();

        $startTime = $this->getEventsStartTime($calendarTableConfiguration, $displayTime);
        $endTime = $this->getEventsEndTime($calendarTableConfiguration, $displayTime);

        $events = $this->orderEvents($events);

        $tableDate = $startTime;
        $eventsToShow = [];

        while ($tableDate <= $endTime) {
            $nextTableDate = strtotime('+' . $calendarTableConfiguration->getHourStep() . ' Hours', $tableDate);

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

        return $calendarTableBuilder->render(
            $calendarTableConfiguration, $displayTime, $eventsToShow, ['table-calendar-day']
        );
    }

    public function renderTitle(CalendarTableConfiguration $calendarTableConfiguration, int $displayTime): string
    {
        return $this->getDatetimeUtilities()->formatLocaleDate(
            $displayTime, IntlDateFormatter::FULL, IntlDateFormatter::NONE
        );
    }
}
