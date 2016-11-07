<?php
namespace Chamilo\Core\User\Component;

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
        // $this->checkAuthorization(Manager::context(), 'Logout');
        $authenticationHandler = new AuthenticationValidator(
            $this->getRequest(),
            $this->getService('chamilo.configuration.service.configuration_consulter'));
        $authenticationHandler->logout($this->getUser());
        exit();
    }
}
