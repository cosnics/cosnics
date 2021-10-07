<?php

namespace Chamilo\Core\Repository\ContentObject\Presence\Display\Component;

use Chamilo\Core\Repository\ContentObject\Presence\Display\Manager;
use Chamilo\Libraries\Architecture\Application\ApplicationConfiguration;

/**
 * @package Chamilo\Core\Repository\ContentObject\Presence\Display\Component
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class AjaxComponent extends Manager
{

    /**
     *
     * @return string
     */
    function run()
    {
        return $this->getApplicationFactory()->getApplication(
            'Chamilo\Core\Repository\ContentObject\Presence\Display\Ajax',
            new ApplicationConfiguration($this->getRequest(), $this->getUser(), $this)
        )->run();
    }
}
