<?php

namespace Chamilo\Core\Repository\ContentObject\LearningPath\Ajax\Component;

use Chamilo\Core\Repository\ContentObject\LearningPath\Ajax\Manager;
use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Renderer\LearningPathTreeJSONMapper;
use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\LearningPathTree;
use Chamilo\Core\Repository\ContentObject\LearningPath\Service\ActionGenerator\NodeBaseActionGenerator;
use Chamilo\Core\Repository\ContentObject\LearningPath\Service\AutomaticNumberingService;
use Chamilo\Core\Repository\ContentObject\LearningPath\Service\LearningPathTreeBuilder;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\LearningPath;
use Chamilo\Core\Repository\Menu\ContentObjectCategoryMenu;
use Chamilo\Core\Repository\Workspace\Repository\ContentObjectRepository;
use Chamilo\Core\Repository\Workspace\Repository\WorkspaceRepository;
use Chamilo\Core\Repository\Workspace\Service\ContentObjectRelationService;
use Chamilo\Core\Repository\Workspace\Service\RightsService;
use Chamilo\Core\Repository\Workspace\Service\WorkspaceService;
use Chamilo\Core\Repository\Workspace\Storage\DataClass\Workspace;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException;
use Chamilo\Libraries\Format\Menu\OptionsMenuRenderer;
use Chamilo\Libraries\Platform\Translation;
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