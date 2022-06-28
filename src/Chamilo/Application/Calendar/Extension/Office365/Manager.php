<?php
namespace Chamilo\Application\Calendar\Extension\Office365;

use Chamilo\Libraries\Architecture\Application\Application;

/**
 *
 * @package application\calendar
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
abstract class Manager extends Application
{
    public const ACTION_LOGIN = 'Login';
    public const ACTION_LOGOUT = 'Logout';

    public const DEFAULT_ACTION = self::ACTION_LOGIN;
    public const PARAM_ACTION = 'office365_action';
}
