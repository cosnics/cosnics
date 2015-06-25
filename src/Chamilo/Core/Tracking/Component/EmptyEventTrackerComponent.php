<?php
namespace Chamilo\Core\Tracking\Component;

use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;

/**
 *
 * @package tracking.lib.tracking_manager.component
 */
class EmptyEventTrackerComponent extends EmptyTrackerComponent
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

        Request :: set_get(self :: PARAM_TYPE, 'event');
        parent :: run();
    }
}
