<?php

namespace Chamilo\Core\Repository\Workspace\Extension\Office365\Component;

use Chamilo\Core\Repository\Workspace\Extension\Office365\Manager;
use Chamilo\Libraries\Architecture\ErrorHandler\ExceptionLogger\ExceptionLoggerInterface;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * @package Chamilo\Core\Repository\Workspace\Extension\Office365
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class VisitGroupComponent extends Manager
{
    /**
     * @return string
     *
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NotAllowedException
     */
    function run()
    {
        $parentComponent = $this->getExtensionLauncherComponent();
        $workspace = $parentComponent->getWorkspace();

        try
        {
            $groupUrl =
                $this->getWorkspaceOffice365Connector()->getGroupUrlForVisit($workspace, $this->getUser());
        }
        catch(\Exception $ex)
        {
            $this->getExceptionLogger()->logException($ex, ExceptionLoggerInterface::EXCEPTION_LEVEL_FATAL_ERROR);
            throw new NotAllowedException();
        }

        return new RedirectResponse($groupUrl);
    }
}