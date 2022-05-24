<?php
namespace Chamilo\Core\Repository\Workspace\Table\Workspace\Personal;

use Chamilo\Core\Repository\Workspace\Repository\WorkspaceRepository;
use Chamilo\Core\Repository\Workspace\Service\WorkspaceService;
use Chamilo\Core\Repository\Workspace\Table\Workspace\WorkspaceTableDataProvider;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\OrderBy;

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

    public function countData(?Condition $condition = null): int
    {
        return $this->getWorkspaceService()->countWorkspacesByCreator($this->get_component()->get_user());
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
    )
    {
        return $this->getWorkspaceService()->getWorkspacesByCreator(
            $this->get_component()->get_user(), $count, $offset, $orderBy
        );
    }
}