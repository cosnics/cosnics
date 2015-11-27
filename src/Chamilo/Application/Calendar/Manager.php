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
    // Parameters
    const PARAM_TIME = 'time';
    const PARAM_VIEW = 'view';
    const PARAM_DOWNLOAD = 'download';

    // Actions
    const ACTION_BROWSE = 'Browser';
    const ACTION_AVAILABILITY = 'Availability';
    const ACTION_ICAL = 'ICal';

    // Default action
    const DEFAULT_ACTION = self :: ACTION_BROWSE;
}
