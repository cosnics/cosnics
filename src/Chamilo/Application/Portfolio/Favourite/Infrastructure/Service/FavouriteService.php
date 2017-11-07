<?php
namespace Chamilo\Application\Portfolio\Favourite\Infrastructure\Service;

use Chamilo\Application\Portfolio\Favourite\Infrastructure\Repository\FavouriteRepository;
use Chamilo\Application\Portfolio\Favourite\Manager;
use Chamilo\Application\Portfolio\Favourite\Storage\DataClass\UserFavourite;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\ResultSet\ResultSet;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * The favourite service
 * 
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class FavouriteService
{

    /**
     *
     * @var FavouriteRepository
     */
    protected $favouriteRepository;

    /**
     *
     * @var Translation
     */
    protected $translator;

    /**
     * FavouriteService constructor.
     * 
     * @param FavouriteRepository $favouriteRepository
     * @param Translation $translator
     */
    public function __construct(FavouriteRepository $favouriteRepository, Translation $translator)
    {
        $this->favouriteRepository = $favouriteRepository;
        $this->translator = $translator;
    }

    /**
     * Creates user favourites by the given source user and favourite user ids
     * 
     * @param User $sourceUser
     * @param int[] $favouriteUserIds
     */
    public function createUserFavouritesByUserIds(User $sourceUser, $favouriteUserIds = array())
    {
        if (! is_array($favouriteUserIds))
        {
            $favouriteUserIds = array($favouriteUserIds);
        }
        
        foreach ($favouriteUserIds as $favouriteUserId)
        {
            /** @var User $favouriteUser */
            $favouriteUser = $this->favouriteRepository->findUserById($favouriteUserId);
            
            $this->createUserFavourite($sourceUser, $favouriteUser);
        }
    }

    /**
     * Creates a user favourite
     * 
     * @param User $sourceUser
     * @param User $favouriteUser
     *
     * @throws \RuntimeException
     *
     * @return UserFavourite
     */
    public function createUserFavourite($sourceUser, $favouriteUser)
    {
        if (! $sourceUser instanceof User)
        {
            throw new \InvalidArgumentException(
                $this->translator->getTranslation('InvalidSourceUser', null, Manager::context()));
        }
        
        if (! $favouriteUser instanceof User)
        {
            throw new \InvalidArgumentException(
                $this->translator->getTranslation('InvalidFavouriteUser', null, Manager::context()));
        }
        
        $userFavourite = new UserFavourite();
        $userFavourite->setSourceUserId($sourceUser->getId());
        $userFavourite->setFavouriteUserId($favouriteUser->getId());
        
        if (! $userFavourite->create())
        {
            $objectTranslation = $this->getObjectTranslation();
            
            throw new \RuntimeException(
                $this->translator->getTranslation(
                    'ObjectNotCreated', 
                    array('OBJECT' => $objectTranslation), 
                    Utilities::COMMON_LIBRARIES));
        }
        
        return $userFavourite;
    }

    /**
     * Deletes user favourites by the given identifiers
     * 
     * @param int[] $userFavouriteIds
     *
     * @throws ObjectNotExistException
     */
    public function deleteUserFavouritesById($userFavouriteIds = array())
    {
        if (! is_array($userFavouriteIds))
        {
            $userFavouriteIds = array($userFavouriteIds);
        }
        
        foreach ($userFavouriteIds as $userFavouriteId)
        {
            $this->deleteUserFavouriteById($userFavouriteId);
        }
    }

    /**
     * Deletes a user favourite by a given id
     * 
     * @param int $userFavouriteId
     *
     * @throws ObjectNotExistException
     */
    public function deleteUserFavouriteById($userFavouriteId)
    {
        $objectTranslation = $this->getObjectTranslation();
        
        $userFavourite = $this->favouriteRepository->findUserFavouriteById($userFavouriteId);
        if (! $userFavourite)
        {
            throw new ObjectNotExistException($objectTranslation, $userFavouriteId);
        }
        
        if (! $userFavourite->delete())
        {
            throw new \RuntimeException(
                $this->translator->getTranslation(
                    'ObjectNotCreated', 
                    array('OBJECT' => $objectTranslation), 
                    Utilities::COMMON_LIBRARIES));
        }
    }

    /**
     * Finds a user favourite object by a given source and favourite user
     * 
     * @param User $sourceUser
     * @param User $possibleFavouriteUser
     *
     * @return \Chamilo\Libraries\Storage\ResultSet\ResultSet
     */
    public function findUserFavouriteBySourceAndFavouriteUser($sourceUser, $possibleFavouriteUser)
    {
        return $this->favouriteRepository->findUserFavouriteBySourceAndFavouriteUserId(
            $sourceUser->getId(), 
            $possibleFavouriteUser->getId());
    }

    /**
     * Returns whether or not the given possible user is a favourite user of the given user
     * 
     * @param User $sourceUser
     * @param User $possibleFavouriteUser
     *
     * @return bool
     */
    public function isUserFavourite($sourceUser, $possibleFavouriteUser)
    {
        return $this->findUserFavouriteBySourceAndFavouriteUser($sourceUser, $possibleFavouriteUser) instanceof UserFavourite;
    }

    /**
     * Counts the favourite users for a given user
     * 
     * @param User $sourceUser
     * @param Condition $condition
     *
     * @return int
     */
    public function countFavouriteUsers(User $sourceUser, $condition = null)
    {
        return $this->favouriteRepository->countFavouriteUsers($sourceUser, $condition);
    }

    /**
     * Finds the favourite users for a given user
     * 
     * @param User $sourceUser
     * @param Condition $condition
     * @param int $offset
     * @param int $count
     * @param OrderBy[] $orderProperty
     *
     * @return ResultSet
     */
    public function findFavouriteUsers(User $sourceUser, $condition = null, $offset = null, $count = null, $orderProperty = null)
    {
        return $this->favouriteRepository->findFavouriteUsers($sourceUser, $condition, $offset, $count, $orderProperty);
    }

    /**
     * Returns the object translation for the user favourite data class
     * 
     * @return string
     */
    protected function getObjectTranslation()
    {
        return $this->translator->getTranslation('UserFavourite', null, Manager::context());
    }
}
