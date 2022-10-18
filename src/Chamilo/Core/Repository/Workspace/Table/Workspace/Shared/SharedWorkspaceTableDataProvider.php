<?php
namespace Chamilo\Core\Repository\Workspace\Table\Workspace\Shared;

use Chamilo\Core\Repository\Workspace\Repository\WorkspaceRepository;
use Chamilo\Core\Repository\Workspace\Service\WorkspaceService;
use Chamilo\Core\Repository\Workspace\Table\Workspace\WorkspaceTableDataProvider;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Doctrine\Common\Collections\ArrayCollection;

/**
 *
 * @package Chamilo\Core\Repository\Workspace\Table\Workspace\Shared
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class SharedWorkspaceTableDataProvider extends WorkspaceTableDataProvider
{

    public function countData(?Condition $condition = null): int
    {
        $workspaceService = new WorkspaceService(new WorkspaceRepository());

        return $workspaceService->countSharedWorkspacesForUser($this->get_component()->get_user());
    }

    public function retrieveData(
        ?Condition $condition = null, ?int $offset = null, ?int $count = null, ?OrderBy $orderBy = null
    ): ArrayCollection
    {
        $workspaceService = new WorkspaceService(new WorkspaceRepository());

        return $workspaceService->getSharedWorkspacesForUser(
            $this->get_component()->get_user(), $count, $offset, $orderBy
        );
    }
}