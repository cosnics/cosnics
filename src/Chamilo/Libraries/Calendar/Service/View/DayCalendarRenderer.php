<?php
namespace Chamilo\Libraries\Calendar\Service\View;

use Chamilo\Libraries\Calendar\Architecture\Interface\CalendarRendererProviderInterface;
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

    public function getNextDisplayTime(int $displayTime): int
    {
        return strtotime('+1 Day', $displayTime);
    }

    public function getPreviousDisplayTime(int $displayTime): int
    {
        return strtotime('-1 Day', $displayTime);
    }

    /**
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

        while ($tableDate <= $endTime) {
            $nextTableDate = strtotime('+' . $calendarTableBuilder->getHourStep() . ' Hours', $tableDate);

            foreach ($events as $event) {
                $startDate = $event->getStartDate();
                $endDate = $event->getEndDate();

                if ($tableDate < $startDate && $startDate < $nextTableDate ||
                    $tableDate < $endDate && $endDate < $nextTableDate ||
                    $startDate <= $tableDate && $nextTableDate <= $endDate) {
                    $eventsToShow[$tableDate][] = $this->getEventDayRenderer()->render(
                        $event, $tableDate, $nextTableDate, $this->isEventSourceVisible($dataProvider, $event)

                    );
                }
            }

            $tableDate = $nextTableDate;
        }

        return $calendarTableBuilder->render($displayTime, $eventsToShow, ['table-calendar-day']);
    }

    public function renderTitle(CalendarRendererProviderInterface $dataProvider, int $displayTime): string
    {
        return $this->getDatetimeUtilities()->formatLocaleDate(
            $displayTime, IntlDateFormatter::FULL, IntlDateFormatter::NONE
        );
    }
}
