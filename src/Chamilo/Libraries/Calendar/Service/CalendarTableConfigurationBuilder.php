<?php
namespace Chamilo\Libraries\Calendar\Service;

use Chamilo\Core\User\Service\UserSettingsService;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Calendar\Architecture\Domain\CalendarTableConfiguration;

/**
 * @package Chamilo\Libraries\Calendar\Service
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class CalendarTableConfigurationBuilder
{
    public function __construct(
        protected UserSettingsService $userSettingsService, protected string $defaultFirstDayOfWeek,
        protected bool $defaultHideNonWorkingHours, protected int $defaultStartHour, protected int $defaultEndHour,
        protected int $defaultHourStep
    )
    {
    }

    public function buildConfiguration(?User $user): CalendarTableConfiguration
    {
        return new CalendarTableConfiguration(
            $this->getFirstDayOfWeek($user), $this->getHideNonWorkingHours($user), $this->getStartHour($user),
            $this->getEndHour($user), $this->getHourStep($user)
        );
    }

    public function getEndHour(?User $user): int
    {
        if ($user instanceof User) {
            return $this->userSettingsService->findUserSetting(
                $user, 'cosnics.libraries.calendar.workingHoursEnd', $this->defaultEndHour
            );
        }

        return $this->defaultEndHour;
    }

    protected function getFirstDayOfWeek(?User $user): ?string
    {
        if ($user instanceof User) {
            return $this->userSettingsService->findUserSetting(
                $user, 'cosnics.libraries.calendar.firstDayOfWeek', $this->defaultFirstDayOfWeek
            );
        }

        return $this->defaultFirstDayOfWeek;
    }

    public function getHideNonWorkingHours(?User $user): bool
    {
        if ($user instanceof User) {
            return $this->userSettingsService->findUserSetting(
                $user, 'cosnics.libraries.calendar.hideNonWorkingHours', $this->defaultHideNonWorkingHours
            );
        }

        return $this->defaultHideNonWorkingHours;
    }

    public function getHourStep(?User $user): int
    {
        if ($user instanceof User) {
            return $this->userSettingsService->findUserSetting(
                $user, 'cosnics.libraries.calendar.hourStep', $this->defaultHourStep
            );
        }

        return $this->defaultHourStep;
    }

    public function getStartHour(?User $user): int
    {
        if ($user instanceof User) {
            return $this->userSettingsService->findUserSetting(
                $user, 'cosnics.libraries.calendar.workingHoursStart', $this->defaultStartHour
            );
        }

        return $this->defaultStartHour;
    }
}