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
    public function __construct(
        LegendRenderer $legendRenderer, UrlGenerator $urlGenerator, Translator $translator,
        ButtonToolBarRenderer $buttonToolBarRenderer, JumpBarRenderer $jumpBarRenderer,
        MiniMonthCalendarRenderer $miniMonthCalendarRenderer, ResourceManager $resourceManager,
        WebPathBuilder $webPathBuilder, protected DatetimeUtilities $datetimeUtilities,
        protected DayCalendarTableBuilder $dayCalendarTableBuilder, protected EventDayRenderer $eventDayRenderer
    )
    {
        parent::__construct(
            $legendRenderer, $urlGenerator, $translator, $buttonToolBarRenderer, $jumpBarRenderer,
            $miniMonthCalendarRenderer, $resourceManager, $webPathBuilder
        );
    }

    public function getEventsEndTime(CalendarTableConfiguration $calendarTableConfiguration, int $displayTime): int
    {
        return $this->dayCalendarTableBuilder->getTableEndTime($calendarTableConfiguration, $displayTime);
    }

    public function getEventsStartTime(CalendarTableConfiguration $calendarTableConfiguration, int $displayTime): int
    {
        return $this->dayCalendarTableBuilder->getTableStartTime($calendarTableConfiguration, $displayTime);
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
                    $eventsToShow[$tableDate][] = $this->eventDayRenderer->render(
                        $event, $tableDate, $nextTableDate, $this->isEventSourceVisible($event, $invisibleSources)

                    );
                }
            }

            $tableDate = $nextTableDate;
        }

        return $this->dayCalendarTableBuilder->render(
            $calendarTableConfiguration, $displayTime, $eventsToShow, ['table-calendar-day']
        );
    }

    public function renderTitle(CalendarTableConfiguration $calendarTableConfiguration, int $displayTime): string
    {
        return $this->datetimeUtilities->formatLocaleDate(
            $displayTime, IntlDateFormatter::FULL, IntlDateFormatter::NONE
        );
    }
}
