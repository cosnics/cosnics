<?php
namespace Chamilo\Application\Calendar\Extension\Google;

use Chamilo\Application\Calendar\Extension\Google\Component\LoginComponent;
use Chamilo\Application\Calendar\Extension\Google\Component\LogoutComponent;
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

    public function getApplicationAction(): string
    {
        return match (static::class) {
            LoginComponent::class => self::ACTION_LOGIN,
            LogoutComponent::class => self::ACTION_LOGOUT
        };
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
        return self::ACTION_LOGIN;
    }
}
