<?php
namespace Chamilo\Application\Calendar\Extension\Google;

use Chamilo\Application\Calendar\Extension\Google\Service\CalendarService;
use Chamilo\Libraries\Architecture\Domain\Application;

/**
 * @package Chamilo\Application\Calendar\Extension\Google
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
abstract class Manager extends Application
{
    public const ACTION_LOGIN = 'Login';
    public const ACTION_LOGOUT = 'Logout';
    public const CONTEXT = __NAMESPACE__;
    public const DEFAULT_ACTION = self::ACTION_LOGIN;

    public function getCalendarService(): CalendarService
    {
        return $this->getService(CalendarService::class);
    }

    public function getContext(): string
    {
        return self::CONTEXT;
    }

    public function getDefaultAction(): string
    {
        return self::DEFAULT_ACTION;
    }
}
