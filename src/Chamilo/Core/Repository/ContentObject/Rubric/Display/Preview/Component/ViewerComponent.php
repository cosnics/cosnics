<?php

namespace Chamilo\Core\Repository\ContentObject\Rubric\Display\Preview\Component;

use Chamilo\Core\Repository\ContentObject\Rubric\Display\Preview\Bridge\RubricBridge;
use Chamilo\Core\Repository\ContentObject\Rubric\Display\Preview\Manager;
use Chamilo\Core\Repository\Viewer\ApplicationConfiguration;

/**
 * Class ViewerComponent
 * @package Chamilo\Core\Repository\ContentObject\Rubric\Display\Preview\Component
 */
class ViewerComponent extends Manager
{

    /**
     * @inheritDoc
     */
    function run()
    {
        $bridge = new RubricBridge();
        $this->getBridgeManager()->addBridge($bridge);

        $configuration = new ApplicationConfiguration($this->getRequest(), $this->getUser(), $this);

        return $this->getApplicationFactory()->getApplication(
            \Chamilo\Core\Repository\ContentObject\Rubric\Display\Manager::context(),
            $configuration
        )->run();
    }
}
