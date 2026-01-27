<?php
namespace Chamilo\Core\Admin;

use Chamilo\Libraries\Architecture\Domain\Application;

/**
 * @package Chamilo\Core\Admin
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Dieter De Neef
 */
abstract class Manager extends Application
{
    public const ACTION_BROWSE = 'Browse';
    public const ACTION_CONFIGURE = 'Configure';
    public const ACTION_DIAGNOSE = 'Diagnose';
    public const ACTION_ONLINE = 'Online';
    public const ACTION_VIEW_LOGS = 'ViewLogs';

    public const CONTEXT = __NAMESPACE__;
    public const DEFAULT_ACTION = self::ACTION_BROWSE;

    public const PARAM_SELECTED_CONTEXT = 'context';
    public const PARAM_USER_ID = 'user_id';
}
