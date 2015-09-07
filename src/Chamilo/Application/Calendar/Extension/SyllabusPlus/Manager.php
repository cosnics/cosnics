<?php
namespace Chamilo\Application\Calendar\Extension\SyllabusPlus;

use Chamilo\Libraries\Architecture\Application\Application;

/**
 *
 * @package application\calendar
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
abstract class Manager extends Application
{
    // Parameters
    const PARAM_ACTION = 'syllabus_action';

    // Actions
    const ACTION_BROWSE = 'Browser';

    // Default action
    const DEFAULT_ACTION = self :: ACTION_BROWSE;
}
