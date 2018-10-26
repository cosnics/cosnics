<?php
namespace Chamilo\Core\User\Component;

use Chamilo\Core\User\Manager;

/**
 *
 * @package user.lib.user_manager.component
 * @author Sven Vanpoucke
 */
class LogoutComponent extends Manager
{

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        // $this->checkAuthorization(Manager::context(), 'Logout');
        $authenticationHandler = $this->getAuthenticationValidator();
        $authenticationHandler->logout($this->getUser());
        exit();
    }
}
