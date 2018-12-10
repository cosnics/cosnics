<?php

namespace Chamilo\Core\Repository\ContentObject\Assignment\Display\Component;

use Chamilo\Core\Repository\ContentObject\Assignment\Display\Manager;
use Chamilo\Libraries\Architecture\Application\ApplicationConfiguration;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;

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
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NotAllowedException
     */
    function run()
    {
        if( !$this->isEphorusEnabled() ||!$this->getAssignmentServiceBridge()->canEditAssignment())
        {
            throw new NotAllowedException();
        }

        $applicationConfiguration = new ApplicationConfiguration($this->getRequest(), $this->getUser(), $this);

        return $this->getApplicationFactory()->getApplication(
            'Chamilo\Core\Repository\ContentObject\Assignment\Display\Ephorus', $applicationConfiguration
        )->run();
    }
}