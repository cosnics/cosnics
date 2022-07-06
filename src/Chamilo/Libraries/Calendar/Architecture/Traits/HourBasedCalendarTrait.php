<?php
namespace Chamilo\Libraries\Calendar\Architecture\Traits;

use Chamilo\Core\User\Service\UserSettingService;
use Chamilo\Core\User\Storage\DataClass\User;

/**
 * @package Chamilo\Libraries\Calendar\Architecture\Traits
 *
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
        if (!isset($this->endHour))
        {
            $this->endHour = $this->getUserSettingService()->getSettingForUser(
                $this->getUser(), 'Chamilo\Libraries\Calendar', 'working_hours_end'
            );
        }

        return $this->endHour;
    }

    public function getHideOtherHours(): bool
    {
        if (!isset($this->hideOtherHours))
        {
            $this->hideOtherHours = $this->getUserSettingService()->getSettingForUser(
                $this->getUser(), 'Chamilo\Libraries\Calendar', 'hide_non_working_hours'
            );
        }

        return $this->hideOtherHours;
    }

    public function getHourStep(): int
    {
        if (!isset($this->hourStep))
        {
            $this->hourStep = $this->getUserSettingService()->getSettingForUser(
                $this->getUser(), 'Chamilo\Libraries\Calendar', 'hour_step'
            );
        }

        return $this->hourStep;
    }

    public function getStartHour(): int
    {
        if (!isset($this->startHour))
        {
            $this->startHour = $this->getUserSettingService()->getSettingForUser(
                $this->getUser(), 'Chamilo\Libraries\Calendar', 'working_hours_start'
            );
        }

        return $this->startHour;
    }

    abstract public function getUser(): User;

    abstract public function getUserSettingService(): UserSettingService;
}
