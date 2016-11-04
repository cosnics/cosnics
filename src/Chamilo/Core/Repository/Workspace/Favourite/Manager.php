<?php
namespace Chamilo\Core\Repository\Workspace\Favourite;

use Chamilo\Core\Repository\Workspace\Repository\WorkspaceRepository;
use Chamilo\Core\Repository\Workspace\Service\WorkspaceService;
use Chamilo\Libraries\Architecture\Application\Application;

/**
 *
 * @package Chamilo\Core\Repository\Workspace\Favourite
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
abstract class Manager extends Application
{
    // Parameters
    const PARAM_FAVOURITE_ID = 'favourite_id';
    const PARAM_ACTION = 'favourite_action';

    // Actions
    const ACTION_BROWSE = 'Browser';
    const ACTION_DELETE = 'Deleter';
    const ACTION_CREATE = 'Creator';

    // Default action
    const DEFAULT_ACTION = self :: ACTION_BROWSE;

    /**
     *
     * @return \Chamilo\Core\Repository\Workspace\Storage\DataClass\Workspace
     */
    public function getCurrentWorkspace()
    {
        $workspaceService = new WorkspaceService(new WorkspaceRepository());
        return $workspaceService->getWorkspaceByIdentifier($this->getCurrentWorkspaceIdentifier());
    }

    /**
     *
     * @return \Symfony\Component\HttpFoundation\mixed
     */
    public function getCurrentWorkspaceIdentifier()
    {
        return $this->getRequest()->query->get(\Chamilo\Core\Repository\Workspace\Manager :: PARAM_WORKSPACE_ID);
    }
}
