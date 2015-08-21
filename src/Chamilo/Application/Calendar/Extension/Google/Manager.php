<?php
namespace Chamilo\Application\Calendar\Extension\Google;

use Chamilo\Libraries\Architecture\Application\Application;

/**
 *
 * @package application\calendar
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
abstract class Manager extends Application
{
    // Parameters
    const PARAM_ACTION = 'google_action';

    // Actions
    const ACTION_LOGIN = 'Login';
    const ACTION_LOGOUT = 'Logout';

    // Default action
    const DEFAULT_ACTION = self :: ACTION_LOGIN;
}
