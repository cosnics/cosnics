<?php
namespace Chamilo\Core\User\Component;

use Chamilo\Core\User\Manager;

/**
 * @package Chamilo\Core\User\Component
 * @author  Sven Vanpoucke
 */
class LogoutComponent extends Manager
{

    public function run()
    {
        $authenticationHandler = $this->getAuthenticationValidator();
        $authenticationHandler->logout($this->getUser());
        exit();
    }
}
