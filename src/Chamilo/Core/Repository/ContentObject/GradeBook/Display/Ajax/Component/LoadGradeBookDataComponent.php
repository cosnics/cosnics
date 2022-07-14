<?php

namespace Chamilo\Core\Repository\ContentObject\GradeBook\Display\Ajax\Component;

use Chamilo\Core\Repository\ContentObject\GradeBook\Display\Ajax\Manager;
use Chamilo\Libraries\Platform\Security\Csrf\CsrfComponentInterface;

/**
 * @package Chamilo\Core\Repository\ContentObject\GradeBook\Display\Ajax\Component
 *
 * @author Stefan Gabriëls - Hogeschool Gent
 */
class LoadGradeBookDataComponent extends Manager implements CsrfComponentInterface
{
    function runAjaxComponent()
    {
        $gradeBookData = $this->getGradeBookAjaxService()->getGradeBookData($this->getGradeBook());
        $gradebookItems = $this->getGradeBookServiceBridge()->findPublicationGradeBookItems();
        $this->getGradeBookAjaxService()->updateGradeBookData($gradeBookData, $gradebookItems);
        return $this->getGradeBookAjaxService()->getGradeBookObjectData($gradeBookData);
    }
}