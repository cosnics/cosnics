<?php
namespace Chamilo\Core\Repository\Workspace\Favourite\Service;

use Chamilo\Core\Repository\Workspace\Favourite\Repository\FavouriteRepository;
use Chamilo\Core\Repository\Workspace\Favourite\Storage\DataClass\WorkspaceUserFavourite;
use Chamilo\Core\User\Storage\DataClass\User;

/**
 *
 * @package Chamilo\Core\Repository\Workspace\Favourite\Service
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class FavouriteService
{

    /**
     *
     * @var \Chamilo\Core\Repository\Workspace\Favourite\Repository\FavouriteRepository
     */
    private $favouriteRepository;

    /**
     *
     * @param \Chamilo\Core\Repository\Workspace\Favourite\Repository\FavouriteRepository $favouriteRepository
     */
    public function __construct(FavouriteRepository $favouriteRepository)
    {
        $this->favouriteRepository = $favouriteRepository;
    }

    /**
     *
     * @return \Chamilo\Core\Repository\Workspace\Favourite\Repository\FavouriteRepository
     */
    public function getFavouriteRepository()
    {
        return $this->favouriteRepository;
    }

    /**
     *
     * @param \Chamilo\Core\Repository\Workspace\Favourite\Repository\FavouriteRepository $favouriteRepository
     */
    public function setFavouriteRepository($favouriteRepository)
    {
        $this->favouriteRepository = $favouriteRepository;
    }

    /**
     *
     * @param User $user
     * @param integer $workspaceIdentifier
     * @return \Chamilo\Core\Repository\Workspace\Favourite\Storage\DataClass\WorkspaceUserFavourite
     */
    public function createWorkspaceUserFavourite(User $user, $workspaceIdentifier)
    {
        $existingWorkspaceUserFavorite = $this->getWorkspaceUserFavouriteByUserAndWorkspaceIdentifier(
            $user, 
            $workspaceIdentifier);
        
        if ($existingWorkspaceUserFavorite)
        {
            return $existingWorkspaceUserFavorite;
        }
        
        $workspaceUserFavourite = new WorkspaceUserFavourite();
        
        $workspaceUserFavourite->set_user_id($user->getId());
        $workspaceUserFavourite->set_workspace_id($workspaceIdentifier);
        
        if (! $workspaceUserFavourite->create())
        {
            return false;
        }
        
        return $workspaceUserFavourite;
    }

    /**
     *
     * @param \Chamilo\Core\Repository\Workspace\Favourite\Storage\DataClass\WorkspaceUserFavourite $workspaceUserFavourite
     * @return boolean
     */
    public function deleteWorkspaceUserFavourite(WorkspaceUserFavourite $workspaceUserFavourite)
    {
        if (! $workspaceUserFavourite->delete())
        {
            return false;
        }
        
        return true;
    }

    /**
     *
     * @param User $user
     * @param integer $workspaceIdentifier
     * @return \Chamilo\Core\Repository\Workspace\Favourite\Storage\DataClass\WorkspaceUserFavourite
     */
    public function getWorkspaceUserFavouriteByUserAndWorkspaceIdentifier(User $user, $workspaceIdentifier)
    {
        return $this->getFavouriteRepository()->findWorkspaceUserFavouriteByUserAndWorkspaceIdentifier(
            $user, 
            $workspaceIdentifier);
    }

    /**
     *
     * @param User $user
     * @param integer $workspaceIdentifier
     * @return boolean
     */
    public function deleteWorkspaceByUserAndWorkspaceIdentifier(User $user, $workspaceIdentifier)
    {
        $workspaceUserFavourite = $this->getWorkspaceUserFavouriteByUserAndWorkspaceIdentifier(
            $user, 
            $workspaceIdentifier);
        
        if (! $workspaceUserFavourite instanceof WorkspaceUserFavourite)
        {
            return false;
        }
        else
        {
            return $this->deleteWorkspaceUserFavourite($workspaceUserFavourite);
        }
    }
}