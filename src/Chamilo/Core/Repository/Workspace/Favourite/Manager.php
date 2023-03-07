<?php
namespace Chamilo\Core\Repository\Workspace\Favourite;

use Chamilo\Core\Repository\Workspace\Favourite\Service\FavouriteService;
use Chamilo\Core\Repository\Workspace\Service\WorkspaceService;
use Chamilo\Core\Repository\Workspace\Storage\DataClass\Workspace;
use Chamilo\Libraries\Architecture\Application\Application;

/**
 * @package Chamilo\Core\Repository\Workspace\Favourite
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
abstract class Manager extends Application
{
    public const ACTION_CREATE = 'Creator';
    public const ACTION_DELETE = 'Deleter';

    public const CONTEXT = __NAMESPACE__;

    public const PARAM_ACTION = 'favourite_action';
    public const PARAM_FAVOURITE_ID = 'favourite_id';

    public function getCurrentWorkspace(): ?Workspace
    {
        return $this->getWorkspaceService()->getWorkspaceByIdentifier($this->getCurrentWorkspaceIdentifier());
    }

    public function getCurrentWorkspaceIdentifier(): ?string
    {
        return $this->getRequest()->query->get(\Chamilo\Core\Repository\Workspace\Manager::PARAM_WORKSPACE_ID);
    }

    public function getFavouriteService(): FavouriteService
    {
        return $this->getService(FavouriteService::class);
    }

    public function getWorkspaceService(): WorkspaceService
    {
        return $this->getService(WorkspaceService::class);
    }
}
