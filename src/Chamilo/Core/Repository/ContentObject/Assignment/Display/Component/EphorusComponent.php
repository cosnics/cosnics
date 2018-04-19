<?php

namespace Chamilo\Core\Repository\ContentObject\Assignment\Display\Component;

use Chamilo\Core\Repository\ContentObject\Assignment\Display\Manager;
use Chamilo\Libraries\Architecture\Application\ApplicationConfiguration;

/**
 * @package Chamilo\Core\Repository\ContentObject\Assignment\Display\Component
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class EphorusComponent extends Manager
{

    /**
     *
     * @return string
     */
    function run()
    {
        $applicationConfiguration = new ApplicationConfiguration($this->getRequest(), $this->getUser(), $this);

        $this->getApplicationFactory()->getApplication(
            'Chamilo\Core\Repository\ContentObject\Assignment\Display\Ephorus', $applicationConfiguration
        )->run();
    }
}