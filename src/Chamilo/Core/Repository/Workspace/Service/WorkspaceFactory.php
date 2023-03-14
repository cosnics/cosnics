<?php
namespace Chamilo\Core\Repository\Workspace\Service;

use Chamilo\Core\Repository\Manager;
use Chamilo\Core\Repository\Workspace\Storage\DataClass\Workspace;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Platform\ChamiloRequest;

/**
 * @package Chamilo\Core\Repository\Workspace\Service
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class WorkspaceFactory
{
    protected ChamiloRequest $request;

    protected RightsService $rightsService;

    protected User $user;

    protected WorkspaceService $workspaceService;
    
    public function __construct(
        ChamiloRequest $request, RightsService $rightsService, User $user, WorkspaceService $workspaceService
    )
    {
        $this->request = $request;
        $this->rightsService = $rightsService;
        $this->user = $user;
        $this->workspaceService = $workspaceService;
    }

    public function getRequest(): ChamiloRequest
    {
        return $this->request;
    }

    public function getRightsService(): RightsService
    {
        return $this->rightsService;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @throws \Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function getWorkspace(): Workspace
    {
        $workspaceService = $this->getWorkspaceService();

        $workspaceIdentifier = $this->getRequest()->query->get(Manager::PARAM_WORKSPACE_ID);

        if ($workspaceIdentifier)
        {
            $workspace = $workspaceService->getWorkspaceByIdentifier($workspaceIdentifier);

            if ($workspace instanceof Workspace)
            {
                return $workspace;
            }
        }

        return $workspaceService->getDefaultWorkspaceForUserIdentifier($this->getUser()->getId());
    }

    public function getWorkspaceService(): WorkspaceService
    {
        return $this->workspaceService;
    }
}

