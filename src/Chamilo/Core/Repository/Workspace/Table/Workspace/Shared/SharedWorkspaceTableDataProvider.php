<?php
namespace Chamilo\Core\Repository\Workspace\Table\Workspace\Shared;

use Chamilo\Core\Repository\Workspace\Repository\WorkspaceRepository;
use Chamilo\Core\Repository\Workspace\Service\WorkspaceService;
use Chamilo\Core\Repository\Workspace\Table\Workspace\WorkspaceTableDataProvider;

/**
 *
 * @package Chamilo\Core\Repository\Workspace\Table\Workspace\Shared
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class SharedWorkspaceTableDataProvider extends WorkspaceTableDataProvider
{

    /**
     *
     * @see \Chamilo\Core\Repository\Workspace\Table\Workspace\WorkspaceTableDataProvider::retrieve_data()
     */
    public function retrieve_data($condition, $offset, $limit, $orderProperty = null)
    {
        $workspaceService = new WorkspaceService(new WorkspaceRepository());
        return $workspaceService->getSharedWorkspacesForUser(
            $this->get_component()->get_user(), 
            $limit, 
            $offset, 
            $orderProperty);
    }

    /**
     *
     * @see \Chamilo\Core\Repository\Workspace\Table\Workspace\WorkspaceTableDataProvider::count_data()
     */
    public function count_data($condition)
    {
        $workspaceService = new WorkspaceService(new WorkspaceRepository());
        return $workspaceService->countSharedWorkspacesForUser($this->get_component()->get_user());
    }
}