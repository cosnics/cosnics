<?php

namespace Chamilo\Core\Repository\Workspace\Extension\Office365;

use Chamilo\Core\Repository\Component\ExtensionLauncherComponent;
use Chamilo\Core\Repository\Workspace\Extension\Office365\Service\WorkspaceOffice365Connector;
use Chamilo\Core\Repository\Workspace\Interfaces\WorkspaceExtensionSupport;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Application\ApplicationConfigurationInterface;

/**
 * @package Chamilo\Core\Repository\Workspace\Extension\Office365
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
abstract class Manager extends Application implements WorkspaceExtensionSupport
{
    const PARAM_ACTION = 'workspace_extension_action';
    const ACTION_VISIT_GROUP = 'VisitGroup';
    const DEFAULT_ACTION = self::ACTION_VISIT_GROUP;

    /**
     *
     * @param \Chamilo\Libraries\Architecture\Application\ApplicationConfigurationInterface $applicationConfiguration
     */
    public function __construct(ApplicationConfigurationInterface $applicationConfiguration)
    {
        if (!$applicationConfiguration->getApplication() instanceof ExtensionLauncherComponent)
        {
            throw new \InvalidArgumentException(
                'The Office365 extension can only be run through the extension component of the workspaces'
            );
        }

        parent::__construct($applicationConfiguration);
    }

    /**
     * @return \Chamilo\Libraries\Architecture\Application\Application | ExtensionLauncherComponent
     */
    public function getExtensionLauncherComponent()
    {
        return $this->get_application();
    }

    /**
     * @return \Chamilo\Core\Repository\Workspace\Extension\Office365\Service\WorkspaceOffice365Connector
     */
    public function getWorkspaceOffice365Connector()
    {
        return $this->getService(WorkspaceOffice365Connector::class);
    }
}