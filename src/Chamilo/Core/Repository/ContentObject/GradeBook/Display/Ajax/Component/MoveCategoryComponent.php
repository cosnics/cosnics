<?php

namespace Chamilo\Core\Repository\ContentObject\GradeBook\Display\Ajax\Component;

use Chamilo\Core\Repository\ContentObject\GradeBook\Display\Ajax\Manager;
use Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException;
use Chamilo\Libraries\Architecture\Exceptions\UserException;
use Chamilo\Libraries\Platform\Security\Csrf\CsrfComponentInterface;
use Doctrine\ORM\ORMException;

/**
 * @package Chamilo\Core\Repository\ContentObject\GradeBook\Display\Ajax
 *
 * @author Stefan Gabriëls - Hogeschool Gent
 */
class MoveCategoryComponent extends Manager implements CsrfComponentInterface
{
    /**
     * @return array
     * @throws ObjectNotExistException
     * @throws UserException
     * @throws ORMException
     */
    function runAjaxComponent(): array
    {
        return $this->getGradeBookAjaxService()->moveCategory($this->getGradeBookData(), $this->getCategoryData(), $this->getRequest()->getFromPost(self::PARAM_NEW_SORT));
    }
}