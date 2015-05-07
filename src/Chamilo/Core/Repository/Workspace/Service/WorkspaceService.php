<?php
namespace Chamilo\Core\Repository\Workspace\Service;

use Chamilo\Core\Repository\Workspace\Repository\WorkspaceRepository;
use Chamilo\Core\Repository\Workspace\PersonalWorkspace;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Core\Repository\Workspace\Storage\DataClass\Workspace;

/**
 *
 * @package Chamilo\Core\Repository\Workspace\Service
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class WorkspaceService
{

    /**
     *
     * @var \Chamilo\Core\Repository\Workspace\Repository\WorkspaceRepository
     */
    private $workspaceRepository;

    /**
     *
     * @param \Chamilo\Core\Repository\Workspace\Repository\WorkspaceRepository $workspaceRepository
     */
    public function __construct(WorkspaceRepository $workspaceRepository)
    {
        $this->workspaceRepository = $workspaceRepository;
    }

    /**
     *
     * @return \Chamilo\Core\Repository\Workspace\Repository\WorkspaceRepository
     */
    public function getWorkspaceRepository()
    {
        return $this->workspaceRepository;
    }

    /**
     *
     * @param \Chamilo\Core\Repository\Workspace\Repository\WorkspaceRepository $workspaceRepository
     */
    public function setWorkspaceRepository($workspaceRepository)
    {
        $this->workspaceRepository = $workspaceRepository;
    }

    /**
     *
     * @param integer $identifier
     * @return \Chamilo\Core\Repository\Workspace\Storage\DataClass\Workspace
     */
    public function getWorkspaceByIdentifier($identifier)
    {
        return $this->getWorkspaceRepository()->findWorkspaceByIdentifier($identifier);
    }

    /**
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param integer $identifier
     * @return \Chamilo\Core\Repository\Workspace\PersonalWorkspace
     */
    public function determineWorkspaceForUserByIdentifier(User $user, $identifier)
    {
        if (is_numeric($identifier))
        {
            $workspace = $this->getWorkspaceByIdentifier($identifier);

            if (! $workspace instanceof Workspace)
            {
                throw new \InvalidArgumentException();
            }
        }
        else
        {
            return $this->getUserPersonalWorkspace($user);
        }
    }

    /**
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @return \Chamilo\Core\Repository\Workspace\PersonalWorkspace
     */
    public function getUserPersonalWorkspace(User $user)
    {
        return new PersonalWorkspace($user);
    }
}