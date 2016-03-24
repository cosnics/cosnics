<?php
namespace Chamilo\Application\Survey\Favourite\Service;

use Chamilo\Application\Survey\Favourite\Repository\FavouriteRepository;
use Chamilo\Application\Survey\Favourite\Storage\DataClass\PublicationUserFavourite;
use Chamilo\Core\User\Storage\DataClass\User;

/**
 *
 * @package Chamilo\Application\Survey\Favourite\Service
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class FavouriteService
{

    /**
     *
     * @var \Chamilo\Application\Survey\Favourite\Repository\FavouriteRepository
     */
    private $favouriteRepository;

    /**
     *
     * @param \Chamilo\Application\Survey\Favourite\Repository\FavouriteRepository $favouriteRepository
     */
    public function __construct(FavouriteRepository $favouriteRepository)
    {
        $this->favouriteRepository = $favouriteRepository;
    }

    /**
     *
     * @return \Chamilo\Application\Survey\Favourite\Repository\FavouriteRepository
     */
    public function getFavouriteRepository()
    {
        return $this->favouriteRepository;
    }

    /**
     *
     * @param \Chamilo\Application\Survey\Favourite\Repository\FavouriteRepository $favouriteRepository
     */
    public function setFavouriteRepository($favouriteRepository)
    {
        $this->favouriteRepository = $favouriteRepository;
    }

    /**
     *
     * @param User $user
     * @param integer $publicationIdentifier
     * @return \Chamilo\Application\Survey\Favourite\Storage\DataClass\PublicationUserFavourite
     */
    public function createPublicationUserFavourite(User $user, $publicationIdentifier)
    {
        $publicationUserFavourite = new PublicationUserFavourite();

        $publicationUserFavourite->set_user_id($user->getId());
        $publicationUserFavourite->set_publication_id($publicationIdentifier);

        if (! $publicationUserFavourite->create())
        {
            return false;
        }

        return $publicationUserFavourite;
    }

    /**
     *
     * @param \Chamilo\Application\Survey\Favourite\Storage\DataClass\PublicationUserFavourite $publicationUserFavourite
     * @return boolean
     */
    public function deletePublicationUserFavourite(PublicationUserFavourite $publicationUserFavourite)
    {
        if (! $publicationUserFavourite->delete())
        {
            return false;
        }

        return true;
    }

    /**
     *
     * @param User $user
     * @param integer $publicationIdentifier
     * @return \Chamilo\Application\Survey\Favourite\Storage\DataClass\PublicationUserFavourite
     */
    public function getPublicationUserFavouriteByUserAndPublicationIdentifier(User $user, $publicationIdentifier)
    {
        return $this->getFavouriteRepository()->findPublicationUserFavouriteByUserAndPublicationIdentifier(
            $user,
            $publicationIdentifier);
    }

    /**
     *
     * @param User $user
     * @param integer $publicationIdentifier
     * @return boolean
     */
    public function deletePublicationByUserAndPublicationIdentifier(User $user, $publicationIdentifier)
    {
        $publicationUserFavourite = $this->getPublicationUserFavouriteByUserAndPublicationIdentifier(
            $user,
            $publicationIdentifier);

        if (! $publicationUserFavourite instanceof PublicationUserFavourite)
        {
            return false;
        }
        else
        {
            return $this->deletePublicationUserFavourite($publicationUserFavourite);
        }
    }
}