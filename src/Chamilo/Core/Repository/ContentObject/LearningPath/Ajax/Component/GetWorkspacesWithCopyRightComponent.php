<?php

namespace Chamilo\Core\Repository\ContentObject\LearningPath\Ajax\Component;

use Chamilo\Core\Repository\ContentObject\LearningPath\Ajax\Manager;
use Chamilo\Core\Repository\Workspace\Repository\WorkspaceRepository;
use Chamilo\Core\Repository\Workspace\Service\RightsService;
use Chamilo\Core\Repository\Workspace\Service\WorkspaceService;
use Chamilo\Libraries\Storage\Iterator\DataClassIterator;
use Exception;
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
            $workspacesDataArray = [];

            $workspaces = $workspaceService->getWorkspacesForUser($this->getUser(), RightsService::RIGHT_VIEW);
            $this->processWorkspaces($workspacesDataArray, $workspaces);

            return new JsonResponse($workspacesDataArray);
        }
        catch (Exception $ex)
        {
            return $this->handleException($ex);
        }
    }

    /**
     * Processes the workspaces to an array
     *
     * @param array $workspacesDataArray
     * @param \Chamilo\Libraries\Storage\Iterator\DataClassIterator<\Chamilo\Core\Repository\Workspace\Storage\DataClass\Workspace> $workspaces
     */
    protected function processWorkspaces(&$workspacesDataArray = [], DataClassIterator $workspaces)
    {
        $rightService = RightsService::getInstance();

        foreach ($workspaces as $workspace)
        {
            $canUse = $rightService->canUseContentObjects($this->getUser(), $workspace);
            $canCopy = $rightService->canCopyContentObjects($this->getUser(), $workspace);

            if (!$canUse && !$canCopy)
            {
                continue;
            }

            $workspacesDataArray[] = array(
                'id' => $workspace->getId(),
                'name' => $workspace->getName(),
                'use_right' => $canUse,
                'copy_right' => $canCopy
            );
        }
    }

}