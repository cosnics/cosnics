<?php
namespace Chamilo\Core\Repository\Service;

use Chamilo\Core\Repository\Workspace\Interfaces\WorkspaceExtensionInterface;
use Chamilo\Core\Repository\Workspace\Storage\DataClass\Workspace;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonGroup;

/**
 * @package Chamilo\Core\Repository\Service
 * @author  Sven Vanpoucke - Hogeschool Gent
 */
class WorkspaceExtensionManager implements WorkspaceExtensionInterface
{
    /**
     * @var WorkspaceExtensionInterface[]
     */
    protected $extensions;

    /**
     * @param \Chamilo\Core\Repository\Workspace\Interfaces\WorkspaceExtensionInterface $extension
     */
    public function addExtension(WorkspaceExtensionInterface $extension)
    {
        $this->extensions[] = $extension;
    }

    /**
     * @return WorkspaceExtensionInterface[]
     */
    protected function getExtensions()
    {
        return $this->extensions;
    }

    /**
     * @param \Chamilo\Libraries\Architecture\Application\Application $workspaceComponent
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param \Chamilo\Libraries\Format\Structure\ActionBar\ButtonGroup $workspaceExtensionActions
     */
    public function getWorkspaceActions(
        Application $workspaceComponent, Workspace $workspace, User $user, ButtonGroup $workspaceExtensionActions
    )
    {
        foreach ($this->getExtensions() as $extension)
        {
            $extension->getWorkspaceActions($workspaceComponent, $workspace, $user, $workspaceExtensionActions);
        }
    }

    /**
     * @param \Chamilo\Core\Repository\Workspace\Storage\DataClass\Workspace $workspace
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     */
    public function workspaceDeleted(Workspace $workspace, User $user)
    {
        foreach ($this->getExtensions() as $extension)
        {
            $extension->workspaceDeleted($workspace, $user);
        }
    }

    /**
     * @param \Chamilo\Core\Repository\Workspace\Storage\DataClass\Workspace $workspace
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     */
    public function workspaceUpdated(Workspace $workspace, User $user)
    {
        foreach ($this->getExtensions() as $extension)
        {
            $extension->workspaceUpdated($workspace, $user);
        }
    }
}