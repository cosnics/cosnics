<?php

namespace Chamilo\Core\Repository\ContentObject\GradeBook\Display\Ajax\Component;

use Chamilo\Core\Repository\ContentObject\GradeBook\Display\Ajax\Manager;
use Chamilo\Libraries\Platform\Security\Csrf\CsrfComponentInterface;

class CalculateTotalScoresComponent extends Manager implements CsrfComponentInterface
{
    function runAjaxComponent()
    {
        return $this->getGradeBookAjaxService()->calculateTotalScores($this->getGradeBookDataId(), $this->getVersion());
    }
}