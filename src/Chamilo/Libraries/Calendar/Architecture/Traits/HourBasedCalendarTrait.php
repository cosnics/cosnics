<?php
namespace Chamilo\Libraries\Calendar\Architecture\Traits;

use Chamilo\Core\User\Service\UserService;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Utilities\DatetimeUtilities;

/**
 * @package Chamilo\Libraries\Calendar\Architecture\Traits
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
trait HourBasedCalendarTrait
{
    protected DatetimeUtilities $datetimeUtilities;

    protected UserService $userService;

    private int $endHour;

    private bool $hideOtherHours;

    private int $hourStep;

    private int $startHour;

    public function getDatetimeUtilities(): DatetimeUtilities
    {
        return $this->datetimeUtilities;
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

    protected User $user;

    public function getUser(): User
    {
        return $this->user;
    }

    public function getStartHour(): int
    {
        if(!isset($this->startHour))
        {
            // TODO: Continue her
            $this->getUserService()->getUserSettingForSettingContextVariableAndUser();
        }

        return $this->startHour;
    }

    public function setStartHour(int $startHour)
    {
        $this->startHour = $startHour;
    }

    public function getUserService(): UserService
    {
        return $this->userService;
    }
}
