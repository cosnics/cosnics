<?php
namespace Chamilo\Core\Repository\UserView;

use Chamilo\Libraries\Architecture\Application\Application;

/**
 *
 * @package core\repository\user_view
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
abstract class Manager extends Application
{
    // Parameters
    const PARAM_ACTION = 'user_view_action';
    const PARAM_USER_VIEW_ID = 'user_view_id';

    // Actions
    const ACTION_BROWSE = 'browser';
    const ACTION_CREATE = 'creator';
    const ACTION_DELETE = 'deleter';
    const ACTION_UPDATE = 'updater';

    // Default action
    const DEFAULT_ACTION = self :: ACTION_BROWSE;
}
