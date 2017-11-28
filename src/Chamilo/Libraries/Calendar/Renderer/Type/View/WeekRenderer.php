<?php
namespace Chamilo\Libraries\Calendar\Renderer\Type\View;

use Chamilo\Libraries\Calendar\Renderer\Event\EventRendererFactory;
use Chamilo\Libraries\Calendar\Table\Type\WeekCalendar;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\DatetimeUtilities;
use Chamilo\Libraries\Utilities\Utilities;

/**
 *
 * @package Chamilo\Libraries\Calendar\Renderer\Type\View
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class WeekRenderer extends FullTableRenderer
{

    /**
     *
     * @return \Chamilo\Libraries\Calendar\Table\Type\WeekCalendar
     */
    public function initializeCalendar()
    {
        $displayParameters = $this->getDataProvider()->getDisplayParameters();
        $displayParameters[self::PARAM_TIME] = WeekCalendar::TIME_PLACEHOLDER;
        $displayParameters[self::PARAM_TYPE] = self::TYPE_DAY;

        return $this->getWeekCalendarBuilder()->buildCalendar(
            $this->getDisplayTime(),
            $displayParameters,
            array('table-calendar-week'));
    }

    /**
     *
     * @return \Chamilo\Libraries\Calendar\Service\Table\WeekCalendarBuilder
     */
    protected function getWeekCalendarBuilder()
    {
        return $this->getService('chamilo.libraries.calendar.service.table.week_calendar_builder');
    }

    /**
     *
     * @return \Chamilo\Libraries\Calendar\Table\CalendarConfiguration
     */
    protected function getCalendarConfiguration()
    {
        return $this->getService('chamilo.libraries.calendar.table.calendar_configuration');
    }

    /**
     *
     * @see \Chamilo\Libraries\Calendar\Renderer\Type\View\FullRenderer::renderFullCalendar()
     */
    public function render()
    {
        $calendarConfiguration = $this->getCalendarConfiguration();
        $calendar = $this->getCalendar();

        $fromDate = strtotime('Last Monday', strtotime('+1 Day', strtotime(date('Y-m-d', $this->getDisplayTime()))));
        $toDate = strtotime('-1 Second', strtotime('Next Week', $fromDate));

        $events = $this->getEvents($fromDate, $toDate);

        $startTime = $calendar->getStartTime();
        $endTime = $toDate;

        $tableDate = $startTime;

        while ($tableDate <= $endTime)
        {
            $nextTableDate = strtotime('+' . $calendarConfiguration->getHourStep() . ' Hours', $tableDate);

            foreach ($events as $index => $event)
            {
                $startDate = $event->getStartDate();
                $endDate = $event->getEndDate();

                if ($tableDate < $startDate && $startDate < $nextTableDate ||
                     $tableDate < $endDate && $endDate <= $nextTableDate ||
                     $startDate <= $tableDate && $nextTableDate <= $endDate)
                {
                    $configuration = new \Chamilo\Libraries\Calendar\Renderer\Event\Configuration();
                    $configuration->setStartDate($tableDate);
                    $configuration->setHourStep($calendarConfiguration->getHourStep());

                    $eventRendererFactory = new EventRendererFactory($this, $event, $configuration);

                    $calendar->addEvent($tableDate, $eventRendererFactory->render());
                }
            }
            $tableDate = $nextTableDate;
        }

        return $calendar->render();
    }

    /**
     *
     * @return string
     */
    public function renderTitle()
    {
        $weekNumber = date('W', $this->getDisplayTime());

        return Translation::get('Week', null, Utilities::COMMON_LIBRARIES) . ' ' . $weekNumber . ' : ' .
             DatetimeUtilities::format_locale_date('%A %d %B %Y', $this->getCalendar()->getStartTime()) . ' - ' . DatetimeUtilities::format_locale_date(
                '%A %d %B %Y',
                strtotime('+6 Days', $this->getCalendar()->getStartTime()));
    }

    /**
     *
     * @see \Chamilo\Libraries\Calendar\Renderer\Type\View\FullTableRenderer::getPreviousDisplayTime()
     */
    public function getPreviousDisplayTime()
    {
        return strtotime('-1 Week', $this->getDisplayTime());
    }

    /**
     *
     * @see \Chamilo\Libraries\Calendar\Renderer\Type\View\FullTableRenderer::getNextDisplayTime()
     */
    public function getNextDisplayTime()
    {
        return strtotime('+1 Week', $this->getDisplayTime());
    }
}
