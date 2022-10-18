<?php
namespace Chamilo\Core\Repository\Workspace\Favourite\Table\Favourite;

use Chamilo\Core\Repository\Workspace\Repository\WorkspaceRepository;
use Chamilo\Core\Repository\Workspace\Service\EntityService;
use Chamilo\Core\Repository\Workspace\Service\WorkspaceService;
use Chamilo\Core\Repository\Workspace\Table\Workspace\WorkspaceTableDataProvider;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Doctrine\Common\Collections\ArrayCollection;

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
     * @var \Chamilo\Core\Repository\Workspace\Service\EntityService
     */
    private $entityService;

    /**
     *
     * @var \Chamilo\Core\Repository\Workspace\Service\WorkspaceService
     */
    private $workspaceService;

    public function countData(?Condition $condition = null): int
    {
        return $this->getWorkspaceService()->countWorkspaceFavouritesByUser(
            $this->getEntityService(), $this->get_component()->get_user()
        );
    }

    /**
     *
     * @return \Chamilo\Core\Repository\Workspace\Service\EntityService
     */
    private function getEntityService()
    {
        if (!isset($this->entityService))
        {
            $this->entityService = new EntityService();
        }

        return $this->entityService;
    }

    /**
     *
     * @return \Chamilo\Core\Repository\Workspace\Service\WorkspaceService
     */
    private function getWorkspaceService()
    {
        if (!isset($this->workspaceService))
        {
            $this->workspaceService = new WorkspaceService(new WorkspaceRepository());
        }

        return $this->workspaceService;
    }

    public function retrieveData(
        ?Condition $condition = null, ?int $offset = null, ?int $count = null, ?OrderBy $orderBy = null
    ): ArrayCollection
    {
        return $this->getWorkspaceService()->getWorkspaceFavouritesByUser(
            $this->getEntityService(), $this->get_component()->get_user(), $count, $offset, $orderBy
        );
    }
}