<?php

namespace Chamilo\Core\Repository\ContentObject\GradeBook\Display\Ajax\Component;

use Chamilo\Core\Repository\ContentObject\GradeBook\Display\Ajax\Manager;
use Chamilo\Libraries\Platform\Security\Csrf\CsrfComponentInterface;

/**
 * @package Chamilo\Core\Repository\ContentObject\GradeBook\Display\Ajax
 *
 * @author Stefan Gabriëls - Hogeschool Gent
 */
class UpdateScoreCommentComponent extends Manager implements CsrfComponentInterface
{
    /**
     * @return array|array[]
     *
     * @throws \Doctrine\ORM\ORMException
     */
    function runAjaxComponent()
    {
        return $this->getGradeBookAjaxService()->updateGradeBookScoreComment($this->getGradeBookDataId(), $this->getVersion(), $this->getGradeScoreId(), $this->getScoreComment());
    }
}