<?php

namespace Chamilo\Core\Repository\ContentObject\Rubric\Display\Component;

use Chamilo\Core\Repository\ContentObject\Rubric\Display\Manager;
use Chamilo\Libraries\Architecture\Application\ApplicationConfiguration;

/**
 * Class AjaxComponent
 * @package Chamilo\Core\Repository\ContentObject\Rubric\Display\Component
 */
class AjaxComponent extends Manager
{

    /**
     * @inheritDoc
     */
    function run()
    {
        $configuration = new ApplicationConfiguration($this->getRequest(), $this->getUser(), $this);

        return $this->getApplicationFactory()->getApplication(
            \Chamilo\Core\Repository\ContentObject\Rubric\Ajax\Manager::context(),
            $configuration
        )->run();
    }
}
