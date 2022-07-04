<?php
namespace Chamilo\Libraries\Calendar\Service\View;

use Chamilo\Libraries\Architecture\Application\Routing\UrlGenerator;
use Chamilo\Libraries\Calendar\Architecture\Interfaces\CalendarRendererProviderInterface;
use Chamilo\Libraries\Calendar\Event\Event;
use Chamilo\Libraries\Calendar\Service\Event\Configuration;
use Chamilo\Libraries\Calendar\Service\Event\EventMonthRenderer;
use Chamilo\Libraries\Calendar\Service\LegendRenderer;
use Chamilo\Libraries\Calendar\Service\View\Table\CalendarTable;
use Chamilo\Libraries\Calendar\Service\View\Table\MonthCalendarTable;
use Chamilo\Libraries\Utilities\StringUtilities;
use Symfony\Component\Translation\Translator;

/**
 *
 * @package Chamilo\Libraries\Calendar\Renderer\Type\View
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class MonthCalendarRenderer extends TableCalendarRenderer
{
    protected EventMonthRenderer $eventMonthRenderer;

    public function __construct(
        LegendRenderer $legendRenderer, UrlGenerator $urlGenerator, Translator $translator,
        MiniMonthCalendarRenderer $miniMonthCalendarRenderer, EventMonthRenderer $eventMonthRenderer
    )
    {
        parent::__construct($legendRenderer, $urlGenerator, $translator, $miniMonthCalendarRenderer);

        $this->eventMonthRenderer = $eventMonthRenderer;
    }

    public function getEventMonthRenderer(): EventMonthRenderer
    {
        return $this->eventMonthRenderer;
    }

    public function getNextDisplayTime(int $displayTime): int
    {
        return strtotime('first day of next month', $displayTime);
    }

    public function getPreviousDisplayTime(int $displayTime): int
    {
        return strtotime('first day of previous month', $displayTime);
    }

    /**
     * @throws \ReflectionException
     */
    public function initializeCalendar(CalendarRendererProviderInterface $dataProvider, int $displayTime): CalendarTable
    {
        $displayParameters = $dataProvider->getDisplayParameters();
        $displayParameters[self::PARAM_TIME] = MonthCalendarTable::TIME_PLACEHOLDER;
        $displayParameters[self::PARAM_TYPE] = self::TYPE_DAY;

        return new MonthCalendarTable(
            $displayTime, $this->getUrlGenerator()->fromParameters($displayParameters), ['table-calendar-month']
        );
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
        $calendar = $this->getCalendar($dataProvider, $displayTime);

        $startTime = $calendar->getStartTime();
        $endTime = $calendar->getEndTime();

        $events = $this->getEvents($dataProvider, $startTime, $endTime);
        $tableDate = $startTime;

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
                    $configuration = new Configuration();
                    $configuration->setStartDate($tableDate);

                    $calendar->addEvent(
                        $tableDate, $this->getEventMonthRenderer()->render(
                        $event, $tableDate, $nextTableDate, $this->isFadedEvent($displayTime, $event),
                        $this->isEventSourceVisible($dataProvider, $event)
                    )
                    );
                }
            }

            $tableDate = $nextTableDate;
        }

        return '<div class="month-calendar">' . $calendar->render() . '</div>';
    }

    public function renderTitle(int $displayTime): string
    {
        return $this->getTranslator()->trans(date('F', $displayTime) . 'Long', [], StringUtilities::LIBRARIES) . ' ' .
            date('Y', $displayTime);
    }
}
