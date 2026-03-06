<?php
namespace Chamilo\Application\Calendar\Extension\Office365;

use Chamilo\Libraries\Architecture\Domain\Application;

/**
 * @package Chamilo\Application\Calendar\Extension\Office365
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
abstract class Manager extends Application
{
    public const ACTION_LOGIN = 'Login';
    public const ACTION_LOGOUT = 'Logout';
    public const CONTEXT = __NAMESPACE__;

    public function getApplicationContext(): string
    {
        return self::CONTEXT;
    }

    public function getDefaultApplicationAction(): string
    {
        return self::ACTION_LOGIN;
    }
}
