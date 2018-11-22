<?php
namespace Chamilo\Application\Portfolio\Favourite\Storage\Repository;

use Chamilo\Application\Portfolio\Favourite\Storage\DataClass\UserFavourite;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Storage\DataClass\Property\DataClassProperties;
use Chamilo\Libraries\Storage\DataManager\Repository\DataClassRepository;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;
use Chamilo\Libraries\Storage\Parameters\RecordRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Join;
use Chamilo\Libraries\Storage\Query\Joins;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\Variable\FixedPropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class FavouriteRepository
{
    const PROPERTY_USER_ID = 'user_id';

    /**
     *
     * @var \Chamilo\Libraries\Storage\DataManager\Repository\DataClassRepository
     */
    private $dataClassRepository;

    /**
     *
     * @param \Chamilo\Libraries\Storage\DataManager\Repository\DataClassRepository $dataClassRepository
     */
    public function __construct(DataClassRepository $dataClassRepository)
    {
        $this->dataClassRepository = $dataClassRepository;
    }

    /**
     *
     * @return \Chamilo\Libraries\Storage\DataManager\Repository\DataClassRepository
     */
    protected function getDataClassRepository()
    {
        return $this->dataClassRepository;
    }

    /**
     *
     * @param \Chamilo\Libraries\Storage\DataManager\Repository\DataClassRepository $dataClassRepository
     */
    protected function setDataClassRepository($dataClassRepository)
    {
        $this->dataClassRepository = $dataClassRepository;
    }

    /**
     * Finds a user favourite by a given id
     *
     * @param int $userFavouriteId
     *
     * @return \Chamilo\Application\Portfolio\Favourite\Storage\DataClass\UserFavourite
     */
    public function findUserFavouriteById($userFavouriteId)
    {
        return $this->getDataClassRepository()->retrieveById(UserFavourite::class, $userFavouriteId);
    }

    /**
     * Finds a user favourite by a given source and favourite user id
     *
     * @param int $sourceUserId
     * @param int $favouriteUserId
     *
     * @return \Chamilo\Application\Portfolio\Favourite\Storage\DataClass\UserFavourite
     */
    public function findUserFavouriteBySourceAndFavouriteUserId($sourceUserId, $favouriteUserId)
    {
        $conditions = array();

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(UserFavourite::class, UserFavourite::PROPERTY_SOURCE_USER_ID),
            new StaticConditionVariable($sourceUserId));

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(UserFavourite::class, UserFavourite::PROPERTY_FAVOURITE_USER_ID),
            new StaticConditionVariable($favouriteUserId));

        $condition = new AndCondition($conditions);

        $parameters = new DataClassRetrieveParameters($condition);

        return $this->getDataClassRepository()->retrieve(UserFavourite::class, $parameters);
    }

    /**
     * Counts the favourite users for a given user
     *
     * @param User $sourceUser
     * @param Condition $condition
     *
     * @return integer
     */
    public function countFavouriteUsers(User $sourceUser, $condition = null)
    {
        $parameters = new DataClassCountParameters(
            $this->getUserFavouriteCondition($sourceUser, $condition),
            $this->getFavouriteUsersJoins());

        return $this->getDataClassRepository()->count(User::class, $parameters);
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

        $properties[] = new PropertyConditionVariable(UserFavourite::class, UserFavourite::PROPERTY_ID);

        $properties[] = new FixedPropertyConditionVariable(User::class, User::PROPERTY_ID, self::PROPERTY_USER_ID);

        $properties[] = new PropertyConditionVariable(User::class, User::PROPERTY_FIRSTNAME);
        $properties[] = new PropertyConditionVariable(User::class, User::PROPERTY_LASTNAME);
        $properties[] = new PropertyConditionVariable(User::class, User::PROPERTY_OFFICIAL_CODE);

        $dataClassProperties = new DataClassProperties($properties);

        if (! $orderProperty)
        {
            $orderProperty = array(
                new OrderBy(new PropertyConditionVariable(User::class, User::PROPERTY_LASTNAME)),
                new OrderBy(new PropertyConditionVariable(User::class, User::PROPERTY_FIRSTNAME)));
        }

        $parameters = new RecordRetrievesParameters(
            $dataClassProperties,
            $this->getUserFavouriteCondition($sourceUser, $condition),
            $count,
            $offset,
            $orderProperty,
            $this->getFavouriteUsersJoins());

        return $this->getDataClassRepository()->records(User::class, $parameters);
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
            new PropertyConditionVariable(UserFavourite::class, UserFavourite::PROPERTY_SOURCE_USER_ID),
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
                UserFavourite::class,
                new EqualityCondition(
                    new PropertyConditionVariable(User::class, User::PROPERTY_ID),
                    new PropertyConditionVariable(UserFavourite::class, UserFavourite::PROPERTY_FAVOURITE_USER_ID))));

        return $joins;
    }
}