<?php
namespace Chamilo\Core\User\Component;

use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;

/**
 *
 * @package user.lib.user_manager.component
 * @author Hans De Bisschop
 */
class DeactivatorComponent extends ActiveChangerComponent
{

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        if (! $this->get_user()->is_platform_admin())
        {
            throw new NotAllowedException();
        }

        Request :: set_get(self :: PARAM_ACTIVE, 0);
        parent :: run();
    }
}
