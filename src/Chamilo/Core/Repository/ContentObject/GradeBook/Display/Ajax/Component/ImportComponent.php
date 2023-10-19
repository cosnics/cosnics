<?php

namespace Chamilo\Core\Repository\ContentObject\GradeBook\Display\Ajax\Component;

use Chamilo\Core\Repository\ContentObject\GradeBook\Display\Ajax\Manager;
use Chamilo\Libraries\Architecture\Exceptions\UserException;
use Chamilo\Libraries\Platform\Security\Csrf\CsrfComponentInterface;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\ORMException;

/**
 * @package Chamilo\Core\Repository\ContentObject\Evaluation\Display\Ajax\Component
 *
 * @author Stefan Gabriëls - Hogeschool Gent
 */
class ImportComponent extends Manager implements CsrfComponentInterface
{
    /**
     * @return array
     * @throws NotAllowedException
     * @throws UserException
     * @throws ORMException
     */
    function runAjaxComponent(): array
    {
        if (!$this->getRequest()->isMethod('POST'))
        {
            throw new NotAllowedException();
        }

        $targetUsers = $this->getGradeBookServiceBridge()->getTargetUsers();
        $contextIdentifier = $this->getGradeBookServiceBridge()->getContextIdentifier();

        try
        {
            $gradeBookData = $this->getGradeBookAjaxService()->getGradeBookData($this->getGradeBook(), $contextIdentifier);
        }
        catch (NoResultException $exception)
        {
            throw new UserException('Invalid gradebook parameters');
        }

        return $this->getImportFromCSVService()->importResults($gradeBookData, $this->getImportScores(), $targetUsers);
    }
}