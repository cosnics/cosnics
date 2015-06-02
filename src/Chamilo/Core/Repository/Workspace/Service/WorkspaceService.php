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
     * @return \Chamilo\Core\Repository\Workspace\Architecture\WorkspaceInterface
     */
    public function determineWorkspaceForUserByIdentifier(User $user, $identifier = null)
    {
        if (! is_null($identifier))
        {
            if (! is_numeric($identifier))
            {
                throw new \InvalidArgumentException();
            }

            return $this->getWorkspaceByIdentifier($identifier);
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

    /**
     *
     * @param string[] $workspaceProperties
     * @return \Chamilo\Core\Repository\Workspace\Storage\DataClass\Workspace
     */
    public function createWorkspace($workspaceProperties)
    {
        $workspace = new Workspace();
        $this->setWorkspaceProperties($workspace, $workspaceProperties);

        if (! $workspace->create())
        {
            return false;
        }

        return $workspace;
    }

    /**
     *
     * @param \Chamilo\Core\Repository\Workspace\Storage\DataClass\Workspace $workspace
     * @param string[] $workspaceProperties
     * @return boolean
     */
    public function updateWorkspace(Workspace $workspace, $workspaceProperties)
    {
        $this->setWorkspaceProperties($workspace, $workspaceProperties);

        if (! $workspace->update())
        {
            return false;
        }

        return true;
    }

    /**
     *
     * @param \Chamilo\Core\Repository\Workspace\Storage\DataClass\Workspace $workspace
     * @return boolean
     */
    public function deleteWorkspace(Workspace $workspace)
    {
        if (! $workspace->delete())
        {
            return false;
        }

        return true;
    }

    /**
     *
     * @param \Chamilo\Core\Repository\Workspace\Storage\DataClass\Workspace $workspace
     * @param string[] $workspaceProperties
     */
    private function setWorkspaceProperties(Workspace $workspace, $workspaceProperties)
    {
        $workspace->setName($workspaceProperties[Workspace :: PROPERTY_NAME]);
        $workspace->setDescription($workspaceProperties[Workspace :: PROPERTY_DESCRIPTION]);
        $workspace->setCreationDate($workspaceProperties[Workspace :: PROPERTY_CREATION_DATE]);
        $workspace->setCreatorId($workspaceProperties[Workspace :: PROPERTY_CREATOR_ID]);
    }
}