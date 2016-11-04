<?php
namespace Chamilo\Core\User\Component;

use Chamilo\Core\User\Manager;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\File\Redirect;

/**
 *
 * @package user.lib.user_manager.component Component to change back from user view to your normal account
 * @author Sven Vanpoucke
 */
class AdminUserComponent extends Manager
{

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        $admin_user = \Chamilo\Libraries\Platform\Session\Session :: retrieve('_as_admin');

        if ($admin_user)
        {
            $checkurl = \Chamilo\Libraries\Platform\Session\Session :: retrieve('checkChamiloURL');
            \Chamilo\Libraries\Platform\Session\Session :: clear();
            \Chamilo\Libraries\Platform\Session\Session :: register('_uid', $admin_user);
            \Chamilo\Libraries\Platform\Session\Session :: register('checkChamiloURL', $checkurl);

            $redirect = new Redirect();
            $redirect->toUrl();
        }
        else
        {
            throw new NotAllowedException();
        }
    }
}
