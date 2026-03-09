<?php
namespace Chamilo\Application\Calendar\Extension\Google\Architecture\Enum;

use Chamilo\Application\Calendar\Extension\Google\Component\LoginComponent;
use Chamilo\Application\Calendar\Extension\Google\Component\LogoutComponent;

/**
 * @package Chamilo\Application\Calendar\Extension\Google\Architecture\Enum
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
enum ActionEnum: string
{
    case LOGIN = 'Login';
    case LOGOUT = 'Logout';

    public static function getAction(string $className): ActionEnum
    {
        return match ($className) {
            LoginComponent::class => self::LOGIN,
            LogoutComponent::class => self::LOGOUT
        };
    }

    public static function getActionValue(string $className): string
    {
        return self::getAction($className)->value;
    }
}
