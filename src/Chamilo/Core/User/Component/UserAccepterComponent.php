<?php
namespace Chamilo\Core\User\Component;

/**
 *
 * @package user.lib.user_manager.component
 * @author Hans De Bisschop
 */
class UserAccepterComponent extends UserApproverComponent
{

    protected function getChoice()
    {
        return 1;
    }
}
