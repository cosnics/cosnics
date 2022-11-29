<?php

namespace Chamilo\Core\Repository\ContentObject\GradeBook\Display\Ajax\Component;

use Chamilo\Core\Repository\ContentObject\GradeBook\Display\Ajax\Manager;
use Chamilo\Libraries\Platform\Security\Csrf\CsrfComponentInterface;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;

/**
 * @package Chamilo\Core\Repository\ContentObject\Evaluation\Display\Ajax\Component
 *
 * @author Stefan Gabriëls - Hogeschool Gent
 */
class ImportComponent extends Manager implements CsrfComponentInterface
{
    function runAjaxComponent()
    {
        if (!$this->getRequest()->isMethod('POST'))
        {
            throw new NotAllowedException();
        }
        $targetUsers = $this->getGradeBookServiceBridge()->getTargetUsers();
        $gradebook = $this->getGradeBook();
        $gradeBookData = $this->getGradeBookService()->getGradeBook($gradebook->getActiveGradeBookDataId(), null);
        return $this->getImportFromCSVService()->importResults($gradeBookData, $this->getImportScores(), $targetUsers);
    }
}