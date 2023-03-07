<?php
namespace Chamilo\Core\Repository\Workspace\Favourite\Service;

use Chamilo\Core\Repository\Workspace\Favourite\Repository\FavouriteRepository;
use Chamilo\Core\Repository\Workspace\Favourite\Storage\DataClass\WorkspaceUserFavourite;
use Chamilo\Core\User\Storage\DataClass\User;

/**
 * @package Chamilo\Core\Repository\Workspace\Favourite\Service
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class FavouriteService
{

    protected FavouriteRepository $favouriteRepository;

    public function __construct(FavouriteRepository $favouriteRepository)
    {
        $this->favouriteRepository = $favouriteRepository;
    }

    /**
     * @throws \Exception
     */
    public function createWorkspaceUserFavourite(User $user, string $workspaceIdentifier): ?WorkspaceUserFavourite
    {
        $existingWorkspaceUserFavorite = $this->getWorkspaceUserFavouriteByUserAndWorkspaceIdentifier(
            $user, $workspaceIdentifier
        );

        if ($existingWorkspaceUserFavorite)
        {
            return $existingWorkspaceUserFavorite;
        }

        $workspaceUserFavourite = new WorkspaceUserFavourite();

        $workspaceUserFavourite->set_user_id($user->getId());
        $workspaceUserFavourite->set_workspace_id($workspaceIdentifier);

        if (!$this->getFavouriteRepository()->createWorkspaceUserFavourite($workspaceUserFavourite))
        {
            return null;
        }

        return $workspaceUserFavourite;
    }

    public function deleteWorkspaceByUserAndWorkspaceIdentifier(User $user, string $workspaceIdentifier): bool
    {
        $workspaceUserFavourite = $this->getWorkspaceUserFavouriteByUserAndWorkspaceIdentifier(
            $user, $workspaceIdentifier
        );

        if (!$workspaceUserFavourite instanceof WorkspaceUserFavourite)
        {
            return false;
        }
        else
        {
            return $this->deleteWorkspaceUserFavourite($workspaceUserFavourite);
        }
    }

    public function deleteWorkspaceUserFavourite(WorkspaceUserFavourite $workspaceUserFavourite): bool
    {
        if (!$this->getFavouriteRepository()->deleteWorkspaceUserFavourite($workspaceUserFavourite))
        {
            return false;
        }

        return true;
    }

    public function getFavouriteRepository(): FavouriteRepository
    {
        return $this->favouriteRepository;
    }

    public function getWorkspaceUserFavouriteByUserAndWorkspaceIdentifier(User $user, string $workspaceIdentifier
    ): ?WorkspaceUserFavourite
    {
        return $this->getFavouriteRepository()->findWorkspaceUserFavouriteByUserAndWorkspaceIdentifier(
            $user, $workspaceIdentifier
        );
    }
}