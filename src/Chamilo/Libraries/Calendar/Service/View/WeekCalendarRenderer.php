<?php
namespace Chamilo\Libraries\Calendar\Service\View;

use Chamilo\Libraries\Calendar\Architecture\Domain\CalendarTableConfiguration;
use Chamilo\Libraries\Calendar\Architecture\Enum\HtmlCalendarRendererTypeEnum;
use Chamilo\Libraries\Calendar\Service\Event\EventDayRenderer;
use Chamilo\Libraries\Calendar\Service\JumpBarRenderer;
use Chamilo\Libraries\Calendar\Service\LegendRenderer;
use Chamilo\Libraries\Calendar\Service\TableBuilder\WeekCalendarTableBuilder;
use Chamilo\Libraries\Filesystem\Service\WebPathBuilder;
use Chamilo\Libraries\Service\Resource\ResourceManager;
use Chamilo\Libraries\Service\Routing\UrlGenerator;
use Chamilo\Libraries\Service\Utilities\DatetimeUtilities;
use Chamilo\Libraries\Service\Utilities\StringUtilities;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Service\ButtonToolBarRenderer;
use IntlDateFormatter;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Libraries\Calendar\Service\View
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class WeekCalendarRenderer extends SidebarTableCalendarRenderer
{
    protected DatetimeUtilities $datetimeUtilities;

    protected EventDayRenderer $eventDayRenderer;

    protected WeekCalendarTableBuilder $weekCalendarTableBuilder;

    public function __construct(
        LegendRenderer $legendRenderer, UrlGenerator $urlGenerator, Translator $translator,
        MiniMonthCalendarRenderer $miniMonthCalendarRenderer, DatetimeUtilities $datetimeUtilities,
        EventDayRenderer $eventDayRenderer, WeekCalendarTableBuilder $weekCalendarTableBuilder,
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
        $this->weekCalendarTableBuilder = $weekCalendarTableBuilder;
    }

    public function getDatetimeUtilities(): DatetimeUtilities
    {
        return $this->datetimeUtilities;
    }

    public function getDayUrlTemplate(array $displayParameters): string
    {
        $displayParameters[self::PARAM_TIME] = WeekCalendarTableBuilder::TIME_PLACEHOLDER;
        $displayParameters[self::PARAM_TYPE] = HtmlCalendarRendererTypeEnum::DAY->value;

        return $this->getUrlGenerator()->fromParameters($displayParameters);
    }

    public function getEventDayRenderer(): EventDayRenderer
    {
        return $this->eventDayRenderer;
    }

    public function getEventsEndTime(CalendarTableConfiguration $calendarTableConfiguration, int $displayTime): int
    {
        return $this->getWeekCalendarTableBuilder()->getTableEndTime($calendarTableConfiguration, $displayTime);
    }

    public function getEventsStartTime(CalendarTableConfiguration $calendarTableConfiguration, int $displayTime): int
    {
        return $this->getWeekCalendarTableBuilder()->getTableStartTime($calendarTableConfiguration, $displayTime);
    }

    public function getNextDisplayTime(int $displayTime): int
    {
        return strtotime('+1 Week', $displayTime);
    }

    public function getPreviousDisplayTime(int $displayTime): int
    {
        return strtotime('-1 Week', $displayTime);
    }

    public function getWeekCalendarTableBuilder(): WeekCalendarTableBuilder
    {
        return $this->weekCalendarTableBuilder;
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
        $calendarTableBuilder = $this->getWeekCalendarTableBuilder();

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
                    $tableDate < $endDate && $endDate <= $nextTableDate ||
                    $startDate <= $tableDate && $nextTableDate <= $endDate) {
                    $eventsToShow[$tableDate][] = $this->getEventDayRenderer()->render(
                        $event, $tableDate, $nextTableDate, $this->isEventSourceVisible($event, $invisibleSources)
                    );
                }
            }

            $tableDate = $nextTableDate;
        }

        return $calendarTableBuilder->render(
            $calendarTableConfiguration, $displayTime, $eventsToShow, ['table-calendar-week'],
            $this->getDayUrlTemplate($displayParameters)
        );
    }

    public function renderTitle(CalendarTableConfiguration $calendarTableConfiguration, int $displayTime): string
    {
        $weekNumber = date('W', $displayTime);
        $dateTimeUtilities = $this->getDatetimeUtilities();
        $calendarTableBuilder = $this->getWeekCalendarTableBuilder();

        $titleParts = [];

        $titleParts[] = $this->getTranslator()->trans('Week', [], StringUtilities::LIBRARIES);
        $titleParts[] = $weekNumber;
        $titleParts[] = ':';
        $titleParts[] = $dateTimeUtilities->formatLocaleDate(
            $calendarTableBuilder->getTableStartTime($calendarTableConfiguration, $displayTime),
            IntlDateFormatter::FULL, IntlDateFormatter::NONE
        );
        $titleParts[] = '-';
        $titleParts[] = $dateTimeUtilities->formatLocaleDate(
            strtotime('+6 Days', $calendarTableBuilder->getTableStartTime($calendarTableConfiguration, $displayTime)),
            IntlDateFormatter::FULL, IntlDateFormatter::NONE
        );

        return implode(' ', $titleParts);
    }
}
