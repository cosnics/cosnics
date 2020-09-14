<?php

namespace Chamilo\Core\Repository\ContentObject\Assignment\Display\Component;

use Chamilo\Core\Repository\ContentObject\Assignment\Display\Manager;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;

/**
 * @package Chamilo\Core\Repository\ContentObject\Assignment\Display\Component
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class BuildRubricComponent extends Manager
{
    /**
     * @return string
     * @throws \Exception
     */
    public function run()
    {
        if (!$this->getRightsService()->canUserEditAssignment($this->getUser(), $this->getAssignment()))
        {
            throw new NotAllowedException();
        }

        return $this->runRubricComponent('Builder', false);
    }
}
