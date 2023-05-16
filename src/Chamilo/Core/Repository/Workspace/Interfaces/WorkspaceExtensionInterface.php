<?php
namespace Chamilo\Core\Repository\Workspace\Interfaces;

use Chamilo\Core\Repository\Workspace\Storage\DataClass\Workspace;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonGroup;

/**
 * Implement this interface as a service to provide the workspaces with the necessary extension actions
 *
 * @package Chamilo\Core\Repository\Workspace\Interfaces
 */
interface WorkspaceExtensionInterface
{
    /**
     * @param \Chamilo\Libraries\Architecture\Application\Application $workspaceComponent
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param \Chamilo\Libraries\Format\Structure\ActionBar\ButtonGroup $workspaceExtensionActions
     */
    public function getWorkspaceActions(
        Application $workspaceComponent, Workspace $workspace, User $user, ButtonGroup $workspaceExtensionActions
    );

    /**
     * @param \Chamilo\Core\Repository\Workspace\Storage\DataClass\Workspace $workspace
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     */
    public function workspaceDeleted(Workspace $workspace, User $user);

    /**
     * @param \Chamilo\Core\Repository\Workspace\Storage\DataClass\Workspace $workspace
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     */
    public function workspaceUpdated(Workspace $workspace, User $user);
}