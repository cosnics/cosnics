<?php
namespace Chamilo\Libraries\Calendar\Architecture\Trait;

use Chamilo\Core\User\Service\UserService;
use Chamilo\Core\User\Storage\DataClass\User;

/**
 * @package Chamilo\Libraries\Calendar\Architecture\Trait
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
trait HourBasedCalendarTrait
{
    protected bool $defaultHideNonWorkingHours;

    protected int $defaultHourStep;

    protected int $defaultWorkingHoursEnd;

    protected int $defaultWorkingHoursStart;

    protected int $endHour;

    protected bool $hideNonWorkingHours;

    protected int $hourStep;

    protected int $startHour;

    protected function getDefaultHideNonWorkingHours(): bool
    {
        return $this->defaultHideNonWorkingHours;
    }

    public function setDefaultHideNonWorkingHours(bool $defaultHideNonWorkingHours): static
    {
        $this->defaultHideNonWorkingHours = $defaultHideNonWorkingHours;

        return $this;
    }

    protected function getDefaultHourStep(): int
    {
        return $this->defaultHourStep;
    }

    public function setDefaultHourStep(int $defaultHourStep): static
    {
        $this->defaultHourStep = $defaultHourStep;

        return $this;
    }

    protected function getDefaultWorkingHoursEnd(): int
    {
        return $this->defaultWorkingHoursEnd;
    }

    public function setDefaultWorkingHoursEnd(int $defaultWorkingHoursEnd): static
    {
        $this->defaultWorkingHoursEnd = $defaultWorkingHoursEnd;

        return $this;
    }

    protected function getDefaultWorkingHoursStart(): int
    {
        return $this->defaultWorkingHoursStart;
    }

    public function setDefaultWorkingHoursStart(int $defaultWorkingHoursStart): static
    {
        $this->defaultWorkingHoursStart = $defaultWorkingHoursStart;

        return $this;
    }

    public function getEndHour(): int
    {
        if (!isset($this->endHour)) {
            $this->endHour = $this->getUserService()->findUserSetting(
                $this->getUser(), 'cosnics.libraries.calendar.workingHoursEnd', $this->getDefaultWorkingHoursEnd()
            );
        }

        return $this->endHour;
    }

    public function getHideNonWorkingHours(): bool
    {
        if (!isset($this->hideNonWorkingHours)) {
            $this->hideNonWorkingHours = $this->getUserService()->findUserSetting(
                $this->getUser(), 'cosnics.libraries.calendar.hideNonWorkingHours',
                $this->getDefaultHideNonWorkingHours()
            );
        }

        return $this->hideNonWorkingHours;
    }

    public function getHourStep(): int
    {
        if (!isset($this->hourStep)) {
            $this->hourStep = $this->getUserService()->findUserSetting(
                $this->getUser(), 'cosnics.libraries.calendar.hourStep', $this->getDefaultHourStep()
            );
        }

        return $this->hourStep;
    }

    public function getStartHour(): int
    {
        if (!isset($this->startHour)) {
            $this->startHour = $this->getUserService()->findUserSetting(
                $this->getUser(), 'cosnics.libraries.calendar.workingHoursStart', $this->getDefaultWorkingHoursStart()
            );
        }

        return $this->startHour;
    }

    abstract public function getUser(): ?User;

    abstract public function getUserService(): UserService;
}
