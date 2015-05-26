<?php
namespace Chamilo\Core\Repository\Workspace;

use Chamilo\Libraries\Architecture\Application\Application;

/**
 *
 * @package Chamilo\Core\Repository\Workspace
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
abstract class Manager extends Application
{
    // Parameters
    const PARAM_WORKSPACE_ID = 'workspace_id';

    // Actions
    const ACTION_BROWSE = 'Browser';
    const ACTION_DELETE = 'Deleter';
    const ACTION_CREATE = 'Creator';
    const ACTION_UPDATE = 'Updater';
    const ACTION_RIGHTS = 'Rights';

    // Default action
    const DEFAULT_ACTION = self :: ACTION_BROWSE;
}
