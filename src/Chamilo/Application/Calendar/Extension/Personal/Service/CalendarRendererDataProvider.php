<?php
namespace Chamilo\Application\Calendar\Extension\Personal\Service;

use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Calendar\Architecture\Interfaces\CalendarRendererDataProviderInterface;
use Chamilo\Libraries\Calendar\Architecture\Interfaces\CalendarRendererProviderInterface;

class CalendarRendererDataProvider implements CalendarRendererDataProviderInterface, CalendarRendererProviderInterface
{
    public function getCalendarEvents(): array
    {
        return [];
    }

    public function getCalendarEventsInPeriod(int $startTime, int $endTime): array
    {
        return [];
    }

    public function getEvents(?int $startTime = null, ?int $endTime = null, bool $calculateRecurrence = false): array
    {
        return [];
    }

    public function getEventsInPeriod(int $startTime, int $endTime, bool $calculateRecurrence = true): array
    {
        return [];
    }

    public function getDataUser(): User
    {
        return new User();
    }

    public function getDisplayParameters(): array
    {
        return [];
    }
}