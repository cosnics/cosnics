<?php

namespace Chamilo\Core\Repository\ContentObject\GradeBook\Display\Ajax\Component;

use Chamilo\Core\Repository\ContentObject\GradeBook\Display\Ajax\Manager;
use Chamilo\Libraries\Platform\Security\Csrf\CsrfComponentInterface;

/**
 * @package Chamilo\Core\Repository\ContentObject\GradeBook\Display\Ajax
 *
 * @author Stefan Gabriëls - Hogeschool Gent
 */
class MoveCategoryComponent extends Manager implements CsrfComponentInterface
{
    /**
     * @return array
     *
     * @throws \Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException
     * @throws \Doctrine\ORM\ORMException
     */
    function runAjaxComponent()
    {
        return $this->getGradeBookAjaxService()->moveCategory($this->getGradeBookDataId(), $this->getVersion(), $this->getCategoryData(), $this->getRequest()->getFromPost(self::PARAM_NEW_SORT));
    }
}