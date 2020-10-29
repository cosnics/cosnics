<?php

namespace Chamilo\Application\ExamAssignment\Component;

use Chamilo\Application\ExamAssignment\Manager;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Interfaces\NoAuthenticationSupport;

/**
 * Class LoginComponent
 * @package Chamilo\Application\ExamAssignment\Component
 *
 * @author - Sven Vanpoucke - Hogeschool Gent
 * @author - Stefan Gabriëls - Hogeschool Gent
 */
class LogoutComponent extends Manager implements NoAuthenticationSupport
{
    /**
     * @return string
     */
    function run()
    {
        if(!$this->getUser() instanceof User)
        {
            $this->redirect([self::PARAM_ACTION => self::ACTION_LOGIN]);
            return;
        }

        $authenticationHandler = $this->getAuthenticationValidator();
        $authenticationHandler->logout($this->getUser(), $this->get_url([self::PARAM_ACTION => self::ACTION_LOGIN]));
        exit();
    }

}
