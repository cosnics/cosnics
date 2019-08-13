<?php
namespace Chamilo\Core\Repository\Workspace\Favourite\Table\Favourite;

use Chamilo\Core\Group\Service\GroupSubscriptionService;
use Chamilo\Core\Repository\Workspace\Repository\WorkspaceRepository;
use Chamilo\Core\Repository\Workspace\Service\EntityService;
use Chamilo\Core\Repository\Workspace\Service\WorkspaceService;
use Chamilo\Core\Repository\Workspace\Table\Workspace\WorkspaceTableDataProvider;
use Chamilo\Libraries\DependencyInjection\DependencyInjectionContainerBuilder;

/**
 *
 * @package Chamilo\Core\Repository\Workspace\Table\Workspace\Personal
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class FavouriteTableDataProvider extends WorkspaceTableDataProvider
{

    /**
     *
     * @var \Chamilo\Core\Repository\Workspace\Service\WorkspaceService
     */
    private $workspaceService;

    /**
     *
     * @var \Chamilo\Core\Repository\Workspace\Service\EntityService
     */
    private $entityService;

    /**
     *
     * @see \Chamilo\Core\Repository\Workspace\Table\Workspace\WorkspaceTableDataProvider::retrieve_data()
     */
    public function retrieve_data($condition, $offset, $limit, $orderProperty = null)
    {
        return $this->getWorkspaceService()->getWorkspaceFavouritesByUser(
            $this->getEntityService(), 
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
        return $this->getWorkspaceService()->countWorkspaceFavouritesByUser(
            $this->getEntityService(), 
            $this->get_component()->get_user());
    }

    /**
     *
     * @return \Chamilo\Core\Repository\Workspace\Service\EntityService
     */
    private function getEntityService()
    {
        if (! isset($this->entityService))
        {
            $this->entityService = new EntityService($this->getGroupSubscriptionService());
        }
        
        return $this->entityService;
    }

    /**
     * @return GroupSubscriptionService
     */
    protected function getGroupSubscriptionService()
    {
        $container = DependencyInjectionContainerBuilder::getInstance()->createContainer();
        return $container->get(GroupSubscriptionService::class);
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