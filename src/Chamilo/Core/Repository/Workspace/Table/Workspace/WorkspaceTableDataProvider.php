<?php
namespace Chamilo\Core\Repository\Workspace\Table\Workspace;

use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableDataProvider;
use Chamilo\Core\Repository\Workspace\Service\WorkspaceService;
use Chamilo\Core\Repository\Workspace\Repository\WorkspaceRepository;

/**
 *
 * @package Chamilo\Core\Repository\Workspace\Table\Workspace
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class WorkspaceTableDataProvider extends DataClassTableDataProvider
{

    public function retrieve_data($condition, $offset, $limit, $orderProperty = null)
    {
        $workspaceService = new WorkspaceService(new WorkspaceRepository());
        return $workspaceService->getWorkspacesByCreator(
            $this->get_component()->get_user(),
            $limit,
            $offset,
            $orderProperty);
    }

    public function count_data($condition)
    {
        $workspaceService = new WorkspaceService(new WorkspaceRepository());
        return $workspaceService->countWorkspacesByCreator($this->get_component()->get_user());
    }
}