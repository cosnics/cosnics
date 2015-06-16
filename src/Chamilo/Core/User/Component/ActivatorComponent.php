<?php
namespace Chamilo\Core\User\Component;

use Chamilo\Libraries\Platform\Session\Request;

/**
 *
 * @package user.lib.user_manager.component
 * @author Hans De Bisschop
 */
class ActivatorComponent extends ActiveChangerComponent
{

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        Request :: set_get(self :: PARAM_ACTIVE, 1);
        parent :: run();
    }
}
