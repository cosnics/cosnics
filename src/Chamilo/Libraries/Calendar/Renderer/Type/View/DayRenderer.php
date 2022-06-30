<?php
namespace Chamilo\Libraries\Calendar\Renderer\Type\View;

use Chamilo\Libraries\Calendar\Renderer\Event\Configuration;
use Chamilo\Libraries\Calendar\Renderer\Event\EventRendererFactory;
use Chamilo\Libraries\Calendar\Renderer\Interfaces\CalendarRendererProviderInterface;
use Chamilo\Libraries\Calendar\Renderer\LegendRenderer;
use Chamilo\Libraries\Calendar\Table\Calendar;
use Chamilo\Libraries\Calendar\Table\Type\DayCalendar;
use Chamilo\Libraries\Utilities\DatetimeUtilities;

/**
 *
 * @package Chamilo\Libraries\Calendar\Renderer\Type\View
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class DayRenderer extends FullTableRenderer
{

    private int $endHour;

    private bool $hideOtherHours;

    private int $hourStep;

    private int $startHour;

    /**
     * @param \Chamilo\Libraries\Format\Structure\ActionBar\AbstractButtonToolBarItem[] $viewActions
     *
     * @throws \Exception
     */
    public function __construct(
        CalendarRendererProviderInterface $dataProvider, LegendRenderer $legend, int $displayTime,
        array $viewActions = [], string $linkTarget = '', int $hourStep = 1, int $startHour = 0, int $endHour = 24,
        bool $hideOtherHours = false
    )
    {
        $this->hourStep = $hourStep;
        $this->startHour = $startHour;
        $this->endHour = $endHour;
        $this->hideOtherHours = $hideOtherHours;

        parent::__construct($dataProvider, $legend, $displayTime, $viewActions, $linkTarget);
    }

    public function getEndHour(): int
    {
        return $this->endHour;
    }

    public function setEndHour(int $endHour)
    {
        $this->endHour = $endHour;
    }

    public function getHideOtherHours(): bool
    {
        return $this->hideOtherHours;
    }

    public function setHideOtherHours(bool $hideOtherHours)
    {
        $this->hideOtherHours = $hideOtherHours;
    }

    public function getHourStep(): int
    {
        return $this->hourStep;
    }

    public function setHourStep(int $hourStep)
    {
        $this->hourStep = $hourStep;
    }

    public function getNextDisplayTime(): int
    {
        return strtotime('+1 Day', $this->getDisplayTime());
    }

    public function getPreviousDisplayTime(): int
    {
        return strtotime('-1 Day', $this->getDisplayTime());
    }

    public function getStartHour(): int
    {
        return $this->startHour;
    }

    public function setStartHour(int $startHour)
    {
        $this->startHour = $startHour;
    }

    public function initializeCalendar(): Calendar
    {
        return new DayCalendar(
            $this->getDisplayTime(), $this->getHourStep(), $this->getStartHour(), $this->getEndHour(),
            $this->getHideOtherHours(), ['table-calendar-day']
        );
    }

    /**
     * @throws \Exception
     */
    public function renderFullCalendar(): string
    {
        $calendar = $this->getCalendar();

        $fromDate = $calendar->getStartTime();
        $toDate = $calendar->getEndTime();

        $events = $this->getEvents($fromDate, $toDate);

        $startTime = $calendar->getStartTime();
        $endTime = $calendar->getEndTime();
        $tableDate = $startTime;

        while ($tableDate <= $endTime)
        {
            $nextTableDate = strtotime('+' . $this->getHourStep() . ' Hours', $tableDate);

            foreach ($events as $event)
            {
                $startDate = $event->getStartDate();
                $endDate = $event->getEndDate();

                if ($tableDate < $startDate && $startDate < $nextTableDate ||
                    $tableDate < $endDate && $endDate < $nextTableDate ||
                    $startDate <= $tableDate && $nextTableDate <= $endDate)
                {
                    $configuration = new Configuration();
                    $configuration->setStartDate($tableDate);
                    $configuration->setHourStep($this->getHourStep());

                    $eventRendererFactory = new EventRendererFactory($this, $event, $configuration);

                    $calendar->addEvent($tableDate, $eventRendererFactory->render());
                }
            }

            $tableDate = $nextTableDate;
        }

        return $calendar->render();
    }

    /**
     * @throws \Exception
     */
    public function renderTitle(): string
    {
        return DatetimeUtilities::getInstance()->formatLocaleDate('%A %d %B %Y', $this->getDisplayTime());
    }
}
