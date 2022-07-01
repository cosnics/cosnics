<?php
namespace Chamilo\Libraries\Calendar\Service\View;

use Chamilo\Libraries\Calendar\Architecture\Interfaces\CalendarRendererProviderInterface;
use Chamilo\Libraries\Calendar\Service\LegendRenderer;

abstract class HourBasedTableCalendarRenderer extends TableCalendarRenderer
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

    public function getStartHour(): int
    {
        return $this->startHour;
    }

    public function setStartHour(int $startHour)
    {
        $this->startHour = $startHour;
    }
}