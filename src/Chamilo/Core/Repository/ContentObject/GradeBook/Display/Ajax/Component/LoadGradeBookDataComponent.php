<?php

namespace Chamilo\Core\Repository\ContentObject\GradeBook\Display\Ajax\Component;

use Chamilo\Core\Repository\ContentObject\GradeBook\Display\Ajax\Manager;

/**
 * @package Chamilo\Core\Repository\ContentObject\GradeBook\Display\Ajax\Component
 *
 * @author Stefan Gabriëls - Hogeschool Gent
 */
class LoadGradeBookDataComponent extends Manager
{
    function runAjaxComponent()
    {
        return $this->getGradeBookAjaxService()->getGradeBookObjectData($this->getGradeBook());
    }
}