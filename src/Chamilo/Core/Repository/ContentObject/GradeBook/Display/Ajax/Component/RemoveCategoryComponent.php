<?php

namespace Chamilo\Core\Repository\ContentObject\GradeBook\Display\Ajax\Component;

use Chamilo\Core\Repository\ContentObject\GradeBook\Display\Ajax\Manager;
use Chamilo\Libraries\Platform\Security\Csrf\CsrfComponentInterface;

/**
 * @package Chamilo\Core\Repository\ContentObject\GradeBook\Display\Ajax
 *
 * @author Stefan Gabriëls - Hogeschool Gent
 */
class RemoveCategoryComponent extends Manager implements CsrfComponentInterface
{
    function runAjaxComponent()
    {
        return $this->getGradeBookAjaxService()->removeCategory(
            $this->getGradeBookDataId(), $this->getVersion(), $this->getCategoryData());
    }
}