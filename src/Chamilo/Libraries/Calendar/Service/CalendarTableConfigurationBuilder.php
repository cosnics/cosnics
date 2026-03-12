<?php
namespace Chamilo\Libraries\Calendar\Service;

use Chamilo\Core\User\Service\UserService;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Calendar\Architecture\Domain\CalendarTableConfiguration;

/**
 * @package Chamilo\Libraries\Calendar\Service
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class CalendarTableConfigurationBuilder
{
    protected int $defaultEndHour;

    protected string $defaultFirstDayOfWeek;

    protected bool $defaultHideNonWorkingHours;

    protected int $defaultHourStep;

    protected int $defaultStartHour;

    protected UserService $userService;

    public function __construct(
        UserService $userService, string $defaultFirstDayOfWeek, bool $defaultHideNonWorkingHours,
        int $defaultStartHour, int $defaultEndHour, int $defaultHourStep
    )
    {
        $this->userService = $userService;
        $this->defaultEndHour = $defaultEndHour;
        $this->defaultFirstDayOfWeek = $defaultFirstDayOfWeek;
        $this->defaultHideNonWorkingHours = $defaultHideNonWorkingHours;
        $this->defaultHourStep = $defaultHourStep;
        $this->defaultStartHour = $defaultStartHour;
    }

    public function buildConfiguration(?User $user): CalendarTableConfiguration
    {
        return new CalendarTableConfiguration(
            $this->getFirstDayOfWeek($user), $this->getHideNonWorkingHours($user), $this->getStartHour($user),
            $this->getEndHour($user), $this->getHourStep($user)
        );
    }

    public function getDefaultEndHour(): int
    {
        return $this->defaultEndHour;
    }

    public function getDefaultFirstDayOfWeek(): string
    {
        return $this->defaultFirstDayOfWeek;
    }

    public function getDefaultHideNonWorkingHours(): bool
    {
        return $this->defaultHideNonWorkingHours;
    }

    public function getDefaultHourStep(): int
    {
        return $this->defaultHourStep;
    }

    public function getDefaultStartHour(): int
    {
        return $this->defaultStartHour;
    }

    public function getEndHour(?User $user): int
    {
        if ($user instanceof User) {
            return $this->getUserService()->findUserSetting(
                $user, 'cosnics.libraries.calendar.workingHoursEnd', $this->getDefaultEndHour()
            );
        }

        return $this->getDefaultEndHour();
    }

    protected function getFirstDayOfWeek(?User $user): ?string
    {
        if ($user instanceof User) {
            return $this->getUserService()->findUserSetting(
                $user, 'cosnics.libraries.calendar.firstDayOfWeek', $this->getDefaultFirstDayOfWeek()
            );
        }

        return $this->getDefaultFirstDayOfWeek();
    }

    public function getHideNonWorkingHours(?User $user): bool
    {
        if ($user instanceof User) {
            return $this->getUserService()->findUserSetting(
                $user, 'cosnics.libraries.calendar.hideNonWorkingHours', $this->getDefaultHideNonWorkingHours()
            );
        }

        return $this->getDefaultHideNonWorkingHours();
    }

    public function getHourStep(?User $user): int
    {
        if ($user instanceof User) {
            return $this->getUserService()->findUserSetting(
                $user, 'cosnics.libraries.calendar.hourStep', $this->getDefaultHourStep()
            );
        }

        return $this->getDefaultHourStep();
    }

    public function getStartHour(?User $user): int
    {
        if ($user instanceof User) {
            return $this->getUserService()->findUserSetting(
                $user, 'cosnics.libraries.calendar.workingHoursStart', $this->getDefaultStartHour()
            );
        }

        return $this->getDefaultStartHour();
    }

    public function getUserService(): UserService
    {
        return $this->userService;
    }
}