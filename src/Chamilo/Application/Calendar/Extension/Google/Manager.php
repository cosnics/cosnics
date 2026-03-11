<?php
namespace Chamilo\Application\Calendar\Extension\Google;

use Chamilo\Application\Calendar\Extension\Google\Architecture\Enum\ActionEnum;
use Chamilo\Application\Calendar\Extension\Google\Service\CalendarService;
use Chamilo\Libraries\Architecture\Domain\Application;

/**
 * @package Chamilo\Application\Calendar\Extension\Google
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
abstract class Manager extends Application
{
    public const string CONTEXT = __NAMESPACE__;

    public function getApplicationAction(): string
    {
        return ActionEnum::getActionValue(static::class);
    }

    public function getApplicationContext(): string
    {
        return self::CONTEXT;
    }

    public function getCalendarService(): CalendarService
    {
        return $this->getService(CalendarService::class);
    }

    public function getDefaultApplicationAction(): string
    {
        return ActionEnum::LOGIN->value;
    }
}
