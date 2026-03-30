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
    public function __construct(
        LegendRenderer $legendRenderer, UrlGenerator $urlGenerator, Translator $translator,
        ButtonToolBarRenderer $buttonToolBarRenderer, JumpBarRenderer $jumpBarRenderer,
        MiniMonthCalendarRenderer $miniMonthCalendarRenderer, ResourceManager $resourceManager,
        WebPathBuilder $webPathBuilder, protected EventDayRenderer $eventDayRenderer,
        protected DatetimeUtilities $datetimeUtilities, protected WeekCalendarTableBuilder $weekCalendarTableBuilder
    )
    {
        parent::__construct(
            $legendRenderer, $urlGenerator, $translator, $buttonToolBarRenderer, $jumpBarRenderer,
            $miniMonthCalendarRenderer, $resourceManager, $webPathBuilder
        );
    }

    public function getDayUrlTemplate(array $displayParameters): string
    {
        $displayParameters[self::PARAM_TIME] = WeekCalendarTableBuilder::TIME_PLACEHOLDER;
        $displayParameters[self::PARAM_TYPE] = HtmlCalendarRendererTypeEnum::DAY->value;

        return $this->urlGenerator->fromParameters($displayParameters);
    }

    public function getEventsEndTime(CalendarTableConfiguration $calendarTableConfiguration, int $displayTime): int
    {
        return $this->weekCalendarTableBuilder->getTableEndTime($calendarTableConfiguration, $displayTime);
    }

    public function getEventsStartTime(CalendarTableConfiguration $calendarTableConfiguration, int $displayTime): int
    {
        return $this->weekCalendarTableBuilder->getTableStartTime($calendarTableConfiguration, $displayTime);
    }

    public function getNextDisplayTime(int $displayTime): int
    {
        return strtotime('+1 Week', $displayTime);
    }

    public function getPreviousDisplayTime(int $displayTime): int
    {
        return strtotime('-1 Week', $displayTime);
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
                    $tableDate < $endDate && $endDate <= $nextTableDate ||
                    $startDate <= $tableDate && $nextTableDate <= $endDate) {
                    $eventsToShow[$tableDate][] = $this->eventDayRenderer->render(
                        $event, $tableDate, $nextTableDate, $this->isEventSourceVisible($event, $invisibleSources)
                    );
                }
            }

            $tableDate = $nextTableDate;
        }

        return $this->weekCalendarTableBuilder->render(
            $calendarTableConfiguration, $displayTime, $eventsToShow, ['table-calendar-week'],
            $this->getDayUrlTemplate($displayParameters)
        );
    }

    public function renderTitle(CalendarTableConfiguration $calendarTableConfiguration, int $displayTime): string
    {
        $weekNumber = date('W', $displayTime);

        $titleParts = [];

        $titleParts[] = $this->translator->trans('Week', [], StringUtilities::LIBRARIES);
        $titleParts[] = $weekNumber;
        $titleParts[] = ':';
        $titleParts[] = $this->datetimeUtilities->formatLocaleDate(
            $this->weekCalendarTableBuilder->getTableStartTime($calendarTableConfiguration, $displayTime),
            IntlDateFormatter::FULL, IntlDateFormatter::NONE
        );
        $titleParts[] = '-';
        $titleParts[] = $this->datetimeUtilities->formatLocaleDate(
            strtotime(
                '+6 Days', $this->weekCalendarTableBuilder->getTableStartTime($calendarTableConfiguration, $displayTime)
            ), IntlDateFormatter::FULL, IntlDateFormatter::NONE
        );

        return implode(' ', $titleParts);
    }
}
