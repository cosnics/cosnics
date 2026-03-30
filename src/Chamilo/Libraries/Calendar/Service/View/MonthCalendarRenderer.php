<?php
namespace Chamilo\Libraries\Calendar\Service\View;

use Chamilo\Libraries\Calendar\Architecture\Domain\CalendarTableConfiguration;
use Chamilo\Libraries\Calendar\Architecture\Domain\Event;
use Chamilo\Libraries\Calendar\Architecture\Enum\HtmlCalendarRendererTypeEnum;
use Chamilo\Libraries\Calendar\Service\Event\EventMonthRenderer;
use Chamilo\Libraries\Calendar\Service\JumpBarRenderer;
use Chamilo\Libraries\Calendar\Service\LegendRenderer;
use Chamilo\Libraries\Calendar\Service\TableBuilder\MonthCalendarTableBuilder;
use Chamilo\Libraries\Filesystem\Service\WebPathBuilder;
use Chamilo\Libraries\Service\Resource\ResourceManager;
use Chamilo\Libraries\Service\Routing\UrlGenerator;
use Chamilo\Libraries\Service\Utilities\StringUtilities;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Service\ButtonToolBarRenderer;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Libraries\Calendar\Service\View
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class MonthCalendarRenderer extends SidebarTableCalendarRenderer
{
    public function __construct(
        LegendRenderer $legendRenderer, UrlGenerator $urlGenerator, Translator $translator,
        MiniMonthCalendarRenderer $miniMonthCalendarRenderer, WebPathBuilder $webPathBuilder,
        ResourceManager $resourceManager, JumpBarRenderer $jumpBarRenderer,
        ButtonToolBarRenderer $buttonToolBarRenderer, protected EventMonthRenderer $eventMonthRenderer,
        protected MonthCalendarTableBuilder $monthCalendarTableBuilder
    )
    {
        parent::__construct(
            $legendRenderer, $urlGenerator, $translator, $buttonToolBarRenderer, $jumpBarRenderer,
            $miniMonthCalendarRenderer, $resourceManager, $webPathBuilder
        );
    }

    public function getDayUrlTemplate(array $displayParameters): string
    {
        $displayParameters[self::PARAM_TIME] = MonthCalendarTableBuilder::TIME_PLACEHOLDER;
        $displayParameters[self::PARAM_TYPE] = HtmlCalendarRendererTypeEnum::DAY->value;

        return $this->urlGenerator->fromParameters($displayParameters);
    }

    public function getEventsEndTime(CalendarTableConfiguration $calendarTableConfiguration, int $displayTime): int
    {
        return $this->monthCalendarTableBuilder->getTableEndTime($calendarTableConfiguration, $displayTime);
    }

    public function getEventsStartTime(CalendarTableConfiguration $calendarTableConfiguration, int $displayTime): int
    {
        return $this->monthCalendarTableBuilder->getTableStartTime($calendarTableConfiguration, $displayTime);
    }

    public function getNextDisplayTime(int $displayTime): int
    {
        return strtotime('first day of next month', $displayTime);
    }

    public function getPreviousDisplayTime(int $displayTime): int
    {
        return strtotime('first day of previous month', $displayTime);
    }

    public function isFadedEvent(int $displayTime, Event $event): bool
    {
        $startDate = $event->getStartDate();

        $fromDate = strtotime(date('Y-m-1', $displayTime));
        $toDate = strtotime('-1 Second', strtotime('Next Month', $fromDate));

        return $startDate < $fromDate || $startDate > $toDate;
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
            $nextTableDate = strtotime('+1 Day', $tableDate);

            foreach ($events as $event) {
                $startDate = $event->getStartDate();
                $endDate = $event->getEndDate();

                if ($tableDate < $startDate && $startDate < $nextTableDate ||
                    $tableDate < $endDate && $endDate <= $nextTableDate ||
                    $startDate <= $tableDate && $nextTableDate <= $endDate) {
                    $eventsToShow[$tableDate][] = $this->eventMonthRenderer->render(
                        $event, $tableDate, $nextTableDate, $this->isEventSourceVisible($event, $invisibleSources),
                        $this->isFadedEvent($displayTime, $event)
                    );
                }
            }

            $tableDate = $nextTableDate;
        }

        $html = [];

        $html[] = '<div class="month-calendar">';
        $html[] = $this->monthCalendarTableBuilder->render(
            $calendarTableConfiguration, $displayTime, $eventsToShow, ['table-calendar-month'],
            $this->getDayUrlTemplate($displayParameters)
        );
        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }

    public function renderTitle(CalendarTableConfiguration $calendarTableConfiguration, int $displayTime): string
    {
        return $this->translator->trans(date('F', $displayTime) . 'Long', [], StringUtilities::LIBRARIES) . ' ' .
            date('Y', $displayTime);
    }
}
