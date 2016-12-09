<?php
namespace Chamilo\Core\Repository\Workspace\Table\Workspace\Personal;

use Chamilo\Core\Repository\Workspace\Repository\WorkspaceRepository;
use Chamilo\Core\Repository\Workspace\Service\WorkspaceService;
use Chamilo\Core\Repository\Workspace\Table\Workspace\WorkspaceTableDataProvider;

/**
 *
 * @package Chamilo\Core\Repository\Workspace\Table\Workspace\Personal
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class PersonalWorkspaceTableDataProvider extends WorkspaceTableDataProvider
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
        return $this->getWorkspaceService()->getWorkspacesByCreator(
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
        return $this->getWorkspaceService()->countWorkspacesByCreator($this->get_component()->get_user());
    }

    /**
     *
     * @return \Chamilo\Core\Repository\Workspace\Service\WorkspaceService
     */
    private function getWorkspaceService()
    {
        if (! isset($this->workspaceService))
        {
            $this->workspaceService = new WorkspaceService(new WorkspaceRepository());
        }
        
        return $this->workspaceService;
    }
}