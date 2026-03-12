<?php
namespace Chamilo\Libraries\Calendar\Architecture\Domain;

/**
 * @package Chamilo\Libraries\Calendar\Architecture\Domain
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class CalendarTableConfiguration
{
    protected int $endHour;

    protected string $firstDayOfWeek;

    protected bool $hideNonWorkingHours;

    protected int $hourStep;

    protected int $startHour;

    public function __construct(
        string $firstDayOfWeek, bool $hideNonWorkingHours, int $startHour, int $endHour, int $hourStep
    )
    {
        $this->endHour = $endHour;
        $this->firstDayOfWeek = $firstDayOfWeek;
        $this->hideNonWorkingHours = $hideNonWorkingHours;
        $this->hourStep = $hourStep;
        $this->startHour = $startHour;
    }

    public function getEndHour(): int
    {
        return $this->endHour;
    }

    public function getFirstDayOfWeek(): string
    {
        return $this->firstDayOfWeek;
    }

    public function getHourStep(): int
    {
        return $this->hourStep;
    }

    public function getStartHour(): int
    {
        return $this->startHour;
    }

    public function getHideNonWorkingHours(): bool
    {
        return $this->hideNonWorkingHours;
    }
}