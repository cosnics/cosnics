<?php

namespace Chamilo\Core\Repository\Workspace\Extension\Office365\Component;

use Chamilo\Core\Repository\Workspace\Extension\Office365\Manager;
use Chamilo\Core\Repository\Workspace\Storage\DataClass\Workspace;
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
     * @throws \Exception
     */
    function run()
    {
        $parentComponent = $this->getExtensionLauncherComponent();
        $workspace = $parentComponent->getWorkspace();
        if (!$workspace instanceof Workspace)
        {
            throw new \Exception(
                'Groups can only be created / visited from within actual workspaces, not from the personal repository'
            );
        }

        try
        {
            if(!$this->getWorkspaceOffice365Connector()->isOffice365GroupActiveForWorkspace($workspace))
            {
                $this->getWorkspaceOffice365Connector()->createGroupForWorkspace($workspace, $this->getUser());
            }

            $groupUrl =
                $this->getWorkspaceOffice365Connector()->getGroupUrlForVisit($workspace, $this->getUser());

            return new RedirectResponse($groupUrl);
        }
        catch (\Exception $ex)
        {
            $this->getExceptionLogger()->logException($ex, ExceptionLoggerInterface::EXCEPTION_LEVEL_FATAL_ERROR);
            throw new NotAllowedException();
        }
    }
}