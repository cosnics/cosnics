<?php

namespace Chamilo\Core\Repository\ContentObject\GradeBook\Display\Ajax\Component;

use Chamilo\Core\Repository\ContentObject\GradeBook\Display\Ajax\Manager;
use Chamilo\Libraries\Architecture\Exceptions\UserException;
use Chamilo\Libraries\Platform\Security\Csrf\CsrfComponentInterface;
use Doctrine\ORM\ORMException;

class CalculateTotalScoresComponent extends Manager implements CsrfComponentInterface
{
    /**
     * @return array
     * @throws UserException
     * @throws ORMException
     */
    function runAjaxComponent(): array
    {
        return $this->getGradeBookAjaxService()->calculateTotalScores($this->getGradeBookData());
    }
}