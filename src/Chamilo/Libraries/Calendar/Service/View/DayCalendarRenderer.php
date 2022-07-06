<?php
namespace Chamilo\Libraries\Calendar\Service\View;

use Chamilo\Core\User\Service\UserSettingService;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Routing\UrlGenerator;
use Chamilo\Libraries\Calendar\Architecture\Interfaces\CalendarRendererProviderInterface;
use Chamilo\Libraries\Calendar\Service\Event\EventDayRenderer;
use Chamilo\Libraries\Calendar\Service\LegendRenderer;
use Chamilo\Libraries\Calendar\Service\View\TableBuilder\DayCalendarTableBuilder;
use Chamilo\Libraries\Calendar\Service\View\TableBuilder\WeekCalendarTableBuilder;
use Chamilo\Libraries\Utilities\DatetimeUtilities;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Libraries\Calendar\Service\View
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class DayCalendarRenderer extends SidebarTableCalendarRenderer
{
    protected DatetimeUtilities $datetimeUtilities;

    protected DayCalendarTableBuilder $dayCalendarTableBuilder;

    protected EventDayRenderer $eventDayRenderer;

    protected User $user;

    protected UserSettingService $userSettingService;

    public function __construct(
        LegendRenderer $legendRenderer, UrlGenerator $urlGenerator, Translator $translator,
        MiniMonthCalendarRenderer $miniMonthCalendarRenderer, DatetimeUtilities $datetimeUtilities,
        UserSettingService $userSettingService, User $user, EventDayRenderer $eventDayRenderer,
        DayCalendarTableBuilder $dayCalendarTableBuilder
    )
    {
        parent::__construct(
            $legendRenderer, $urlGenerator, $translator, $miniMonthCalendarRenderer
        );

        $this->eventDayRenderer = $eventDayRenderer;
        $this->datetimeUtilities = $datetimeUtilities;
        $this->userSettingService = $userSettingService;
        $this->user = $user;
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

    public function getDayUrlTemplate(CalendarRendererProviderInterface $dataProvider): string
    {
        $displayParameters = $dataProvider->getDisplayParameters();
        $displayParameters[self::PARAM_TIME] = WeekCalendarTableBuilder::TIME_PLACEHOLDER;
        $displayParameters[self::PARAM_TYPE] = self::TYPE_DAY;

        return $this->getUrlGenerator()->fromParameters($displayParameters);
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

    public function getUser(): User
    {
        return $this->user;
    }

    public function getUserSettingService(): UserSettingService
    {
        return $this->userSettingService;
    }

    /**
     * @throws \ReflectionException
     * @throws \Exception
     */
    public function renderFullCalendar(CalendarRendererProviderInterface $dataProvider, int $displayTime): string
    {
        //        $calendarTableBuilder = new DayCalendarTableBuilder(
        //            $displayTime, $this->getHourStep(), $this->getStartHour(), $this->getEndHour(), $this->getHideOtherHours(),
        //            ['table-calendar-day']
        //        );

        $calendarTableBuilder = $this->getDayCalendarTableBuilder();

        $fromDate = $calendarTableBuilder->getTableStartTime($displayTime);
        $toDate = $calendarTableBuilder->getTableEndTime($displayTime);

        $events = $this->getEvents($dataProvider, $fromDate, $toDate);

        $startTime = $calendarTableBuilder->getTableStartTime($displayTime);
        $endTime = $calendarTableBuilder->getTableEndTime($displayTime);
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

        return $calendarTableBuilder->render($displayTime, $eventsToShow, ['table-calendar-day']);
    }

    public function renderTitle(CalendarRendererProviderInterface $dataProvider, int $displayTime): string
    {
        return $this->getDatetimeUtilities()->formatLocaleDate('%A %d %B %Y', $displayTime);
    }
}
