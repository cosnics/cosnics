<?php
namespace Chamilo\Application\Portfolio\Favourite\Infrastructure\Repository;

use Chamilo\Application\Portfolio\Favourite\Storage\DataClass\UserFavourite;
use Chamilo\Application\Portfolio\Favourite\Storage\DataManager;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Storage\DataClass\Property\DataClassProperties;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;
use Chamilo\Libraries\Storage\Parameters\RecordRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Join;
use Chamilo\Libraries\Storage\Query\Joins;
use Chamilo\Libraries\Storage\Query\Variable\FixedPropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 * Wrapper for the Portfolio Favourite DataManager
 * 
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class FavouriteRepository
{
    const PROPERTY_USER_ID = 'user_id';

    /**
     * Finds a user favourite by a given id
     * 
     * @param int $userFavouriteId
     *
     * @return \Chamilo\Libraries\Storage\DataClass\DataClass
     */
    public function findUserFavouriteById($userFavouriteId)
    {
        return DataManager::retrieve_by_id(UserFavourite::class_name(), $userFavouriteId);
    }

    /**
     * Finds a user favourite by a given source and favourite user id
     * 
     * @param int $sourceUserId
     * @param int $favouriteUserId
     *
     * @return \Chamilo\Libraries\Storage\ResultSet\ResultSet
     */
    public function findUserFavouriteBySourceAndFavouriteUserId($sourceUserId, $favouriteUserId)
    {
        $conditions = array();
        
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(UserFavourite::class_name(), UserFavourite::PROPERTY_SOURCE_USER_ID), 
            new StaticConditionVariable($sourceUserId));
        
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(UserFavourite::class_name(), UserFavourite::PROPERTY_FAVOURITE_USER_ID), 
            new StaticConditionVariable($favouriteUserId));
        
        $condition = new AndCondition($conditions);
        
        $parameters = new DataClassRetrieveParameters($condition);
        
        return DataManager::retrieve(UserFavourite::class_name(), $parameters);
    }

    /**
     * Temporary wrapper for the user datamanager to find a user by an id
     * 
     * @param int $userId
     *
     * @return \Chamilo\Libraries\Storage\DataClass\DataClass
     */
    public function findUserById($userId)
    {
        return \Chamilo\Core\User\Storage\DataManager::retrieve_by_id(User::class_name(), $userId);
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
        $parameters = new DataClassCountParameters(
            $this->getUserFavouriteCondition($sourceUser, $condition), 
            $this->getFavouriteUsersJoins());
        
        return DataManager::count(User::class_name(), $parameters);
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
     * @return mixed
     */
    public function findFavouriteUsers(User $sourceUser, $condition = null, $offset = null, $count = null, $orderProperty = null)
    {
        $properties = array();
        
        $properties[] = new PropertyConditionVariable(UserFavourite::class_name(), UserFavourite::PROPERTY_ID);
        
        $properties[] = new FixedPropertyConditionVariable(User::class_name(), User::PROPERTY_ID, self::PROPERTY_USER_ID);
        
        $properties[] = new PropertyConditionVariable(User::class_name(), User::PROPERTY_FIRSTNAME);
        $properties[] = new PropertyConditionVariable(User::class_name(), User::PROPERTY_LASTNAME);
        $properties[] = new PropertyConditionVariable(User::class_name(), User::PROPERTY_OFFICIAL_CODE);
        
        $dataClassProperties = new DataClassProperties($properties);
        
        $parameters = new RecordRetrievesParameters(
            $dataClassProperties, 
            $this->getUserFavouriteCondition($sourceUser, $condition), 
            $count, 
            $offset, 
            $orderProperty, 
            $this->getFavouriteUsersJoins());
        
        return DataManager::records(User::class_name(), $parameters);
    }

    /**
     * Builds and merges the conditions for the given source user and condition
     * 
     * @param User $sourceUser
     * @param Condition $condition
     *
     * @return AndCondition
     */
    protected function getUserFavouriteCondition(User $sourceUser, $condition = null)
    {
        $conditions = array();
        
        $conditions[] = $this->getSourceUserCondition($sourceUser);
        
        if ($condition)
        {
            $conditions[] = $condition;
        }
        
        return new AndCondition($conditions);
    }

    /**
     * Returns the condition for the source user
     * 
     * @param User $sourceUser
     *
     * @return EqualityCondition
     */
    protected function getSourceUserCondition(User $sourceUser)
    {
        return new EqualityCondition(
            new PropertyConditionVariable(UserFavourite::class_name(), UserFavourite::PROPERTY_SOURCE_USER_ID), 
            new StaticConditionVariable($sourceUser->getId()));
    }

    /**
     * Gets the joins between the User and the UserFavourite dataclass
     * 
     * @return Joins
     */
    protected function getFavouriteUsersJoins()
    {
        $joins = new Joins();
        $joins->add(
            new Join(
                UserFavourite::class_name(), 
                new EqualityCondition(
                    new PropertyConditionVariable(User::class_name(), User::PROPERTY_ID), 
                    new PropertyConditionVariable(UserFavourite::class_name(), UserFavourite::PROPERTY_FAVOURITE_USER_ID))));
        
        return $joins;
    }
}
