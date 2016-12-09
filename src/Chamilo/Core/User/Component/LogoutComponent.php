<?php
namespace Chamilo\Core\User\Component;

use Chamilo\Configuration\Configuration;
use Chamilo\Core\User\Manager;
use Chamilo\Libraries\Authentication\AuthenticationValidator;

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
        $authenticationHandler = new AuthenticationValidator($this->getRequest(), Configuration :: get_instance());
        $authenticationHandler->logout($this->getUser());
        exit();
    }
}
