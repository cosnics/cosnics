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
    // Parameters
    const PARAM_ACTION = 'office365_action';

    // Actions
    const ACTION_BROWSE = 'Browser';
    const ACTION_LOGIN = 'Login';
    const ACTION_LOGOUT = 'Logout';
    const ACTION_VISIBILITY = 'Visibility';

    // Default action
    const DEFAULT_ACTION = self :: ACTION_BROWSE;
}
