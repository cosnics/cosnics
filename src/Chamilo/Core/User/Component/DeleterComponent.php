<?php
namespace Chamilo\Core\User\Component;

use Chamilo\Core\Tracking\Storage\DataClass\Event;
use Chamilo\Core\User\Manager;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;

/**
 * $Id: deleter.class.php 211 2009-11-13 13:28:39Z vanpouckesven $
 *
 * @package user.lib.user_manager.component
 */
class DeleterComponent extends Manager
{

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        if (! $this->get_user()->is_platform_admin())
        {
            throw new NotAllowedException();
        }

        $ids = Request :: get(self :: PARAM_USER_USER_ID);

        if (! is_array($ids))
        {
            $ids = array($ids);
        }

        if (count($ids) > 0)
        {
            $failures = 0;

            foreach ($ids as $id)
            {
                $user = \Chamilo\Core\User\Storage\DataManager :: retrieve_by_id(
                    \Chamilo\Core\User\Storage\DataClass\User :: class_name(),
                    (int) $id);

                if (! \Chamilo\Core\User\Storage\DataManager :: user_deletion_allowed($user))
                {
                    $failures ++;
                    continue;
                }

                if ($user->delete())
                {
                    Event :: trigger(
                        'delete',
                        Manager :: context(),
                        array('target_user_id' => $user->get_id(), 'action_user_id' => $this->get_user()->get_id()));
                }
                else
                {
                    $failures ++;
                }
            }

            $message = $this->get_result(
                $failures,
                count($ids),
                'UserNotDeleted',
                'UsersNotDeleted',
                'UserDeleted',
                'UsersDeleted');

            $this->redirect(
                $message,
                ($failures > 0),
                array(Application :: PARAM_ACTION => self :: ACTION_BROWSE_USERS));
        }
        else
        {
            return $this->display_error_page(
                htmlentities(
                    Translation :: get(
                        'NoObjectSelected',
                        array('OBJECT' => Translation :: get('User')),
                        Utilities :: COMMON_LIBRARIES)));
        }
    }

    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add(
            new Breadcrumb(
                $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE_USERS)),
                Translation :: get('UserManagerAdminUserBrowserComponent')));
        $breadcrumbtrail->add_help('user_deleter');
    }

    public function get_additional_parameters()
    {
        return array(self :: PARAM_USER_USER_ID);
    }
}
