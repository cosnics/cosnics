<?php
namespace Chamilo\Libraries\Calendar\Service\View;

use Chamilo\Libraries\Architecture\Application\Routing\UrlGenerator;
use Chamilo\Libraries\Calendar\Architecture\Interfaces\CalendarRendererProviderInterface;
use Chamilo\Libraries\Calendar\Architecture\Traits\HourBasedCalendarTrait;
use Chamilo\Libraries\Calendar\Service\Event\EventWeekRenderer;
use Chamilo\Libraries\Calendar\Service\LegendRenderer;
use Chamilo\Libraries\Calendar\Service\View\Table\CalendarTable;
use Chamilo\Libraries\Calendar\Service\View\Table\WeekCalendarTable;
use Chamilo\Libraries\Utilities\DatetimeUtilities;
use Chamilo\Libraries\Utilities\StringUtilities;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Libraries\Calendar\Service\View
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class WeekCalendarRenderer extends TableCalendarRenderer
{
    use HourBasedCalendarTrait;

    protected EventWeekRenderer $eventWeekRenderer;

    public function __construct(
        LegendRenderer $legendRenderer, UrlGenerator $urlGenerator, Translator $translator,
        MiniMonthCalendarRenderer $miniMonthCalendarRenderer, DatetimeUtilities $datetimeUtilities,
        EventWeekRenderer $eventWeekRenderer
    )
    {
        parent::__construct(
            $legendRenderer, $urlGenerator, $translator, $miniMonthCalendarRenderer
        );

        $this->eventWeekRenderer = $eventWeekRenderer;
        $this->datetimeUtilities = $datetimeUtilities;
    }

    public function getEventWeekRenderer(): EventWeekRenderer
    {
        return $this->eventWeekRenderer;
    }

    public function getNextDisplayTime(int $displayTime): int
    {
        return strtotime('+1 Week', $displayTime);
    }

    public function getPreviousDisplayTime(int $displayTime): int
    {
        return strtotime('-1 Week', $displayTime);
    }

    public function initializeCalendar(CalendarRendererProviderInterface $dataProvider, int $displayTime): CalendarTable
    {
        $displayParameters = $dataProvider->getDisplayParameters();
        $displayParameters[self::PARAM_TIME] = WeekCalendarTable::TIME_PLACEHOLDER;
        $displayParameters[self::PARAM_TYPE] = self::TYPE_DAY;

        return new WeekCalendarTable(
            $displayTime, $this->getUrlGenerator()->fromParameters($displayParameters), $this->getHourStep(),
            $this->getStartHour(), $this->getEndHour(), $this->getHideOtherHours(), ['table-calendar-week']
        );
    }

    /**
     * @throws \ReflectionException
     * @throws \Exception
     */
    public function renderFullCalendar(CalendarRendererProviderInterface $dataProvider, int $displayTime): string
    {
        $calendar = $this->getCalendar($dataProvider, $displayTime);
        $fromDate = strtotime('Last Monday', strtotime('+1 Day', strtotime(date('Y-m-d', $displayTime))));
        $toDate = strtotime('-1 Second', strtotime('Next Week', $fromDate));

        $events = $this->getEvents($dataProvider, $fromDate, $toDate);

        $startTime = $calendar->getStartTime();
        $endTime = $toDate;

        $tableDate = $startTime;

        while ($tableDate <= $endTime)
        {
            $nextTableDate = strtotime('+' . $calendar->getHourStep() . ' Hours', $tableDate);

            foreach ($events as $event)
            {
                $startDate = $event->getStartDate();
                $endDate = $event->getEndDate();

                if ($tableDate < $startDate && $startDate < $nextTableDate ||
                    $tableDate < $endDate && $endDate <= $nextTableDate ||
                    $startDate <= $tableDate && $nextTableDate <= $endDate)
                {
                    $calendar->addEvent(
                        $tableDate, $this->getEventWeekRenderer()->render(
                        $event, $tableDate, $nextTableDate, $this->isEventSourceVisible($dataProvider, $event)
                    )
                    );
                }
            }

            $tableDate = $nextTableDate;
        }

        return $calendar->render();
    }

    /**
     * @throws \ReflectionException
     */
    public function renderTitle(CalendarRendererProviderInterface $dataProvider, int $displayTime): string
    {
        $weekNumber = date('W', $displayTime);
        $dateTimeUtilities = $this->getDatetimeUtilities();
        $calendar = $this->getCalendar($dataProvider, $displayTime);

        $titleParts = [];

        $titleParts[] = $this->getTranslator()->trans('Week', [], StringUtilities::LIBRARIES);
        $titleParts[] = $weekNumber;
        $titleParts[] = ':';
        $titleParts[] = $dateTimeUtilities->formatLocaleDate('%A %d %B %Y', $calendar->getStartTime());
        $titleParts[] = '-';
        $titleParts[] = $dateTimeUtilities->formatLocaleDate(
            '%A %d %B %Y', strtotime('+6 Days', $calendar->getStartTime())
        );

        return implode(' ', $titleParts);
    }
}
