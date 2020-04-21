<?php
namespace Chamilo\Libraries\Ajax\Component;

use Chamilo\Libraries\Ajax\Manager;
use Chamilo\Libraries\Architecture\JsonAjaxResult;

/**
 * Simple ajax component which can be called at regular intervals to make sure that the session of the user is still
 * alive.
 * Useful in places were content editors are opened for a longer period of time.
 *
 * @package Chamilo\Libraries\Ajax\Component
 * @author Sven Vanpoucke - Hogeschool Gent <sven.vanpoucke@hogent.be>
 */
class HeartBeatComponent extends Manager
{

    /**
     *
     * @see \Chamilo\Libraries\Architecture\Application\Application::run()
     */
    public function run()
    {
        JsonAjaxResult::success();
    }
}