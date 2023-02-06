<?php

namespace Chamilo\Core\Repository\ContentObject\GradeBook\Display\Component;

use Chamilo\Core\Repository\ContentObject\GradeBook\Display\Manager;
use Chamilo\Libraries\Architecture\Application\ApplicationConfiguration;

/**
 * @package Chamilo\Core\Repository\ContentObject\GradeBook\Display\Component
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
            'Chamilo\Core\Repository\ContentObject\GradeBook\Display\Ajax',
            new ApplicationConfiguration($this->getRequest(), $this->getUser(), $this)
        )->run();
    }
}
