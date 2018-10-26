<?php

namespace Chamilo\Core\Repository\Workspace\Extension\Office365\Component;

use Chamilo\Core\Repository\Workspace\Extension\Office365\Manager;
use Chamilo\Core\Repository\Workspace\Storage\DataClass\Workspace;
use Chamilo\Libraries\Architecture\ErrorHandler\ExceptionLogger\ExceptionLoggerInterface;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;

/**
 * @package Chamilo\Core\Repository\Workspace\Extension\Office365
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class SyncGroupComponent extends Manager
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
            $this->getWorkspaceOffice365Connector()->syncGroupForWorkspace($workspace, $this->getUser());
            $success = true;
            $message = 'GroupSynced';

        }
        catch (\Exception $ex)
        {
            $this->getExceptionLogger()->logException($ex, ExceptionLoggerInterface::EXCEPTION_LEVEL_FATAL_ERROR);
            $success = false;
            $message = 'GroupNotSynced';
        }

        $this->redirect(
            $this->getTranslator()->trans($message, [], Manager::context()), !$success,
            [
                \Chamilo\Core\Repository\Manager::PARAM_ACTION =>
                    \Chamilo\Core\Repository\Manager::ACTION_BROWSE_CONTENT_OBJECTS
            ],
            [self::PARAM_ACTION]
        );

        return;
    }
}