<?php
namespace Chamilo\Core\User\Component;

use Chamilo\Core\User\Manager;
use Symfony\Component\HttpFoundation\Response;

/**
 * @package Chamilo\Core\User\Component
 * @author  Sven Vanpoucke
 */
class LogoutComponent extends Manager
{

    public function run(): Response
    {
        $authenticationHandler = $this->getAuthenticationValidator();
        $authenticationHandler->logout($this->getUser());
        exit();
    }
}
