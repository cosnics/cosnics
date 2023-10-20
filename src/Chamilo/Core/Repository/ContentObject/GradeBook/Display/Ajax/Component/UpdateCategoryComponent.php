<?php

namespace Chamilo\Core\Repository\ContentObject\GradeBook\Display\Ajax\Component;

use Chamilo\Core\Repository\ContentObject\GradeBook\Display\Ajax\Manager;
use Chamilo\Libraries\Architecture\Exceptions\UserException;
use Chamilo\Libraries\Platform\Security\Csrf\CsrfComponentInterface;
use Doctrine\ORM\ORMException;

/**
 * @package Chamilo\Core\Repository\ContentObject\GradeBook\Display\Ajax
 *
 * @author Stefan Gabriëls - Hogeschool Gent
 */
class UpdateCategoryComponent extends Manager implements CsrfComponentInterface
{
    /**
     * @return array
     *
     * @throws ORMException
     * @throws UserException
     */
    function runAjaxComponent(): array
    {
        return $this->getGradeBookAjaxService()->updateCategory($this->getGradeBookData(), $this->getCategoryData());
    }
}