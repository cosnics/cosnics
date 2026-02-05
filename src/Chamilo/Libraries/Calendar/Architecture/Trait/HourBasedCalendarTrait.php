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
    protected int $endHour;

    protected bool $hideOtherHours;

    protected int $hourStep;

    protected int $startHour;

    public function getEndHour(): int
    {
        if (!isset($this->endHour)) {
            $this->endHour = (int) $this->getUserService()->findUserSetting(
                $this->getUser(), 'Chamilo\Libraries\Calendar', 'WorkingHoursEnd'
            );
        }

        return $this->endHour;
    }

    public function getHideOtherHours(): bool
    {
        if (!isset($this->hideOtherHours)) {
            $this->hideOtherHours = (bool) $this->getUserService()->findUserSetting(
                $this->getUser(), 'Chamilo\Libraries\Calendar', 'HideNonWorkingHours'
            );
        }

        return $this->hideOtherHours;
    }

    public function getHourStep(): int
    {
        if (!isset($this->hourStep)) {
            $this->hourStep = (int) $this->getUserService()->findUserSetting(
                $this->getUser(), 'Chamilo\Libraries\Calendar', 'HourStep'
            );
        }

        return $this->hourStep;
    }

    public function getStartHour(): int
    {
        if (!isset($this->startHour)) {
            $this->startHour = (int) $this->getUserService()->findUserSetting(
                $this->getUser(), 'Chamilo\Libraries\Calendar', 'WorkingHoursStart'
            );
        }

        return $this->startHour;
    }

    abstract public function getUser(): ?User;

    abstract public function getUserService(): UserService;
}
