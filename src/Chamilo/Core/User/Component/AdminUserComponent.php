<?php
namespace Chamilo\Core\User\Component;

use Chamilo\Core\User\Manager;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * @package user.lib.user_manager.component Component to change back from user view to your normal account
 * @author  Sven Vanpoucke
 */
class AdminUserComponent extends Manager
{

    /**
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NotAllowedException
     */
    public function run()
    {
        $adminUserIdentifier = $this->getSession()->get('_as_admin');

        if ($adminUserIdentifier)
        {
            $checkurl = $this->getSession()->get('checkChamiloURL');

            $this->getSession()->clear();
            $this->getSession()->set(Manager::SESSION_USER_ID, $adminUserIdentifier);
            $this->getSession()->set('checkChamiloURL', $checkurl);

            return new RedirectResponse($this->getUrlGenerator()->fromParameters());
        }
        else
        {
            throw new NotAllowedException();
        }
    }
}
