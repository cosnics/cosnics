<?php

namespace Chamilo\Core\Repository\ContentObject\GradeBook\Display\Ajax\Component;

use Chamilo\Core\Repository\ContentObject\GradeBook\Display\Ajax\Manager;

/**
 * @package Chamilo\Core\Repository\ContentObject\GradeBook\Display\Ajax
 *
 * @author Stefan Gabriëls - Hogeschool Gent
 */
class UpdateCategoryComponent extends Manager
{
    function runAjaxComponent()
    {
        return $this->getGradeBookAjaxService()->updateCategory($this->getGradeBookDataId(), $this->getVersion(), $this->getCategoryData());
    }
}