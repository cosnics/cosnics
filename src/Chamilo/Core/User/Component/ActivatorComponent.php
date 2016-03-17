<?php
namespace Chamilo\Core\User\Component;

/**
 *
 * @package user.lib.user_manager.component
 * @author Hans De Bisschop
 */
class ActivatorComponent extends ActiveChangerComponent
{

    private function getState()
    {
        return 1;
    }
}
