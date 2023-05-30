<?php
namespace Chamilo\Core\User\Component;

use Chamilo\Core\User\Manager;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Platform\Session\Session;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * @package user.lib.user_manager.component Component to change back from user view to your normal account
 * @author  Sven Vanpoucke
 */
class AdminUserComponent extends Manager
{

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        $admin_user = Session::retrieve('_as_admin');

        if ($admin_user)
        {
            $checkurl = Session::retrieve('checkChamiloURL');
            Session::clear();
            Session::register('_uid', $admin_user);
            Session::register('checkChamiloURL', $checkurl);

            return new RedirectResponse($this->getUrlGenerator()->fromParameters());
        }
        else
        {
            throw new NotAllowedException();
        }
    }
}
