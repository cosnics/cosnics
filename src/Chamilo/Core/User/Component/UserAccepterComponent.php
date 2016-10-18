<?php
namespace Chamilo\Core\User\Component;

/**
 *
 * @package user.lib.user_manager.component
 * @author Hans De Bisschop
 */
class UserAccepterComponent extends UserApproverComponent
{

    private function getChoice()
    {
        return 1;
    }
}
