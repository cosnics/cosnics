<?php

namespace Chamilo\Core\Repository\ContentObject\LearningPath\Display\Component;

use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Manager;
use Chamilo\Libraries\Architecture\Application\ApplicationConfiguration;
use Chamilo\Libraries\Architecture\Application\ApplicationFactory;

/**
 * Calls the AjaxManager for the learning path
 */
class AjaxComponent extends Manager
{
    /**
     * @return string
     */
    function run()
    {
        $factory = new ApplicationFactory(
            'Chamilo\Core\Repository\ContentObject\LearningPath\Display\Ajax',
            new ApplicationConfiguration($this->getRequest(), $this->getUser(), $this)
        );

        return $factory->run();
    }
}