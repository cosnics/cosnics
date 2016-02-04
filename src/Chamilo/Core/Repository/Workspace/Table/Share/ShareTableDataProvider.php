<?php
namespace Chamilo\Core\Repository\Workspace\Table\Share;

use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableDataProvider;
use Chamilo\Core\Repository\Workspace\Service\WorkspaceService;
use Chamilo\Core\Repository\Workspace\Repository\WorkspaceRepository;
use Chamilo\Core\Repository\Workspace\Service\RightsService;

/**
 *
 * @package Chamilo\Core\Repository\Workspace\Table\Workspace
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class ShareTableDataProvider extends DataClassTableDataProvider
{

    /**
     *
     * @var \Chamilo\Core\Repository\Workspace\Service\WorkspaceService
     */
    private $workspaceService;

    /**
     *
     * @see \Chamilo\Core\Repository\Workspace\Table\Workspace\WorkspaceTableDataProvider::retrieve_data()
     */
    public function retrieve_data($condition, $offset, $limit, $orderProperty = null)
    {
        return $this->getWorkspaceService()->getWorkspacesForUser(
            $this->get_component()->get_user(),
            RightsService :: RIGHT_ADD,
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
        return $this->getWorkspaceService()->countWorkspacesForUser(
            $this->get_component()->get_user(),
            RightsService :: RIGHT_ADD);
    }

    /**
     *
     * @return \Chamilo\Core\Repository\Workspace\Service\WorkspaceService
     */
    protected function getWorkspaceService()
    {
        if (! isset($this->workspaceService))
        {
            $this->workspaceService = new WorkspaceService(new WorkspaceRepository());
        }

        return $this->workspaceService;
    }
}