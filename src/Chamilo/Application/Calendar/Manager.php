<?php
namespace Chamilo\Application\Calendar;

use Chamilo\Libraries\Architecture\Application\Application;

/**
 *
 * @package application\calendar
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
abstract class Manager extends Application
{
    const APPLICATION_NAME = 'calendar';

    // Parameters
    const PARAM_TIME = 'time';
    const PARAM_VIEW = 'view';

    // Actions
    const ACTION_BROWSE = 'browser';

    // Default action
    const DEFAULT_ACTION = self :: ACTION_BROWSE;
}
