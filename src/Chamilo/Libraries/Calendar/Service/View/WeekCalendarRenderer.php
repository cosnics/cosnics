<?php
namespace Chamilo\Libraries\Calendar\Service\View;

use Chamilo\Libraries\Calendar\Architecture\Interface\CalendarRendererProviderInterface;
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
     * @throws \Exception
     */
    public function renderFullCalendar(CalendarRendererProviderInterface $dataProvider, int $displayTime): string
    {
        $calendarTableBuilder = $this->getWeekCalendarTableBuilder();

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
                    $tableDate < $endDate && $endDate <= $nextTableDate ||
                    $startDate <= $tableDate && $nextTableDate <= $endDate) {
                    $eventsToShow[$tableDate][] = $this->getEventDayRenderer()->render(
                        $event, $tableDate, $nextTableDate, $this->isEventSourceVisible($dataProvider, $event)
                    );
                }
            }

            $tableDate = $nextTableDate;
        }

        return $calendarTableBuilder->render($displayTime, $eventsToShow, ['table-calendar-week'],
            $this->getDayUrlTemplate($dataProvider));
    }

    public function renderTitle(CalendarRendererProviderInterface $dataProvider, int $displayTime): string
    {
        $weekNumber = date('W', $displayTime);
        $dateTimeUtilities = $this->getDatetimeUtilities();
        $calendarTableBuilder = $this->getWeekCalendarTableBuilder();

        $titleParts = [];

        $titleParts[] = $this->getTranslator()->trans('Week', [], StringUtilities::LIBRARIES);
        $titleParts[] = $weekNumber;
        $titleParts[] = ':';
        $titleParts[] = $dateTimeUtilities->formatLocaleDate(
            $calendarTableBuilder->getTableStartTime($displayTime), IntlDateFormatter::FULL, IntlDateFormatter::NONE
        );
        $titleParts[] = '-';
        $titleParts[] = $dateTimeUtilities->formatLocaleDate(
            strtotime('+6 Days', $calendarTableBuilder->getTableStartTime($displayTime)), IntlDateFormatter::FULL,
            IntlDateFormatter::NONE
        );

        return implode(' ', $titleParts);
    }
}
