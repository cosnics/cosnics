<?php

namespace Chamilo\Core\Repository\ContentObject\GradeBook\Display\Ajax\Component;

use Chamilo\Core\Repository\ContentObject\GradeBook\Display\Ajax\Manager;

/**
 * @package Chamilo\Core\Repository\ContentObject\GradeBook\Display\Ajax\Component
 *
 * @author Stefan Gabriëls - Hogeschool Gent
 */
class LoadGradeItemScoresComponent extends Manager
{
    function runAjaxComponent()
    {
        $gradeBookData = $this->getGradeBookAjaxService()->getGradeBookData($this->getGradeBook());
        $gradeItem = $gradeBookData->getGradeBookItemById($this->getGradeItemId());

        return $this->getGradeBookServiceBridge()->findScores($gradeItem);
    }
}