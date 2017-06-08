<?php

namespace Chamilo\Core\Repository\ContentObject\LearningPath\Ajax\Component;

use Chamilo\Core\Repository\ContentObject\LearningPath\Ajax\Manager;
use Chamilo\Core\Repository\Workspace\Repository\WorkspaceRepository;
use Chamilo\Core\Repository\Workspace\Service\RightsService;
use Chamilo\Core\Repository\Workspace\Service\WorkspaceService;
use Chamilo\Core\Repository\Workspace\Storage\DataClass\Workspace;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Returns the workspaces for which the user has the right to copy
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class GetWorkspacesWithCopyRightComponent extends Manager
{
    /**
     * Executes this component and return's its response
     */
    public function run()
    {
        try
        {
            $workspaceService = new WorkspaceService(new WorkspaceRepository());
            $workspaces = $workspaceService->getWorkspacesForUser($this->getUser(), RightsService::RIGHT_COPY);

            $workspacesArray = array();

            while($workspace = $workspaces->next_result())
            {
                /** @var Workspace $workspace */

                $workspacesArray[] = array(
                    'id' => $workspace->getId(),
                    'name' => $workspace->getName()
                );
            }

            return new JsonResponse($workspacesArray);
        }
        catch (\Exception $ex)
        {
            return $this->handleException($ex);
        }
    }

}