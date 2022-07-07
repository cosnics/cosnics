<?php
namespace Chamilo\Libraries\Calendar\Service\View;

use Chamilo\Libraries\Architecture\Application\Routing\UrlGenerator;
use Chamilo\Libraries\Calendar\Architecture\Interfaces\CalendarRendererProviderInterface;
use Chamilo\Libraries\Calendar\Event\Event;
use Chamilo\Libraries\Calendar\Service\Event\EventMonthRenderer;
use Chamilo\Libraries\Calendar\Service\LegendRenderer;
use Chamilo\Libraries\Calendar\Service\View\TableBuilder\MonthCalendarTableBuilder;
use Chamilo\Libraries\Utilities\StringUtilities;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Libraries\Calendar\Service\View
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class MonthCalendarRenderer extends SidebarTableCalendarRenderer
{
    protected EventMonthRenderer $eventMonthRenderer;

    protected MonthCalendarTableBuilder $monthCalendarTableBuilder;

    public function __construct(
        LegendRenderer $legendRenderer, UrlGenerator $urlGenerator, Translator $translator,
        MiniMonthCalendarRenderer $miniMonthCalendarRenderer, EventMonthRenderer $eventMonthRenderer,
        MonthCalendarTableBuilder $monthCalendarTableBuilder
    )
    {
        parent::__construct($legendRenderer, $urlGenerator, $translator, $miniMonthCalendarRenderer);

        $this->eventMonthRenderer = $eventMonthRenderer;
        $this->monthCalendarTableBuilder = $monthCalendarTableBuilder;
    }

    public function getDayUrlTemplate(CalendarRendererProviderInterface $dataProvider): string
    {
        $displayParameters = $dataProvider->getDisplayParameters();
        $displayParameters[self::PARAM_TIME] = MonthCalendarTableBuilder::TIME_PLACEHOLDER;
        $displayParameters[self::PARAM_TYPE] = self::TYPE_DAY;

        return $this->getUrlGenerator()->fromParameters($displayParameters);
    }

    public function getEventMonthRenderer(): EventMonthRenderer
    {
        return $this->eventMonthRenderer;
    }

    public function getMonthCalendarTableBuilder(): MonthCalendarTableBuilder
    {
        return $this->monthCalendarTableBuilder;
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
     * @throws \ReflectionException
     * @throws \Exception
     */
    public function renderFullCalendar(CalendarRendererProviderInterface $dataProvider, int $displayTime): string
    {
        $calendarTableBuilder = $this->getMonthCalendarTableBuilder();
        $startTime = $calendarTableBuilder->getTableStartTime($displayTime);
        $endTime = $calendarTableBuilder->getTableEndTime($displayTime);

        $events = $this->getEvents($dataProvider, $startTime, $endTime);
        $tableDate = $startTime;
        $eventsToShow = [];

        while ($tableDate <= $endTime)
        {
            $nextTableDate = strtotime('+1 Day', $tableDate);

            foreach ($events as $event)
            {
                $startDate = $event->getStartDate();
                $endDate = $event->getEndDate();

                if ($tableDate < $startDate && $startDate < $nextTableDate ||
                    $tableDate < $endDate && $endDate <= $nextTableDate ||
                    $startDate <= $tableDate && $nextTableDate <= $endDate)
                {
                    $eventsToShow[$tableDate][] = $this->getEventMonthRenderer()->render(
                        $event, $tableDate, $nextTableDate, $this->isEventSourceVisible($dataProvider, $event),
                        $this->isFadedEvent($displayTime, $event)
                    );
                }
            }

            $tableDate = $nextTableDate;
        }

        $html = [];

        $html[] = '<div class="month-calendar">';
        $html[] = $calendarTableBuilder->render($displayTime, $eventsToShow, ['table-calendar-month'],
            $this->getDayUrlTemplate($dataProvider));
        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }

    public function renderTitle(CalendarRendererProviderInterface $dataProvider, int $displayTime): string
    {
        return $this->getTranslator()->trans(date('F', $displayTime) . 'Long', [], StringUtilities::LIBRARIES) . ' ' .
            date('Y', $displayTime);
    }
}
