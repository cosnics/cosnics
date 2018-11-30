<?php
namespace Chamilo\Core\User\Storage\Repository;

use Chamilo\Configuration\Storage\DataClass\Setting;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Core\User\Storage\DataClass\UserSetting;
use Chamilo\Core\User\Storage\Repository\Interfaces\UserRepositoryInterface;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\DataClass\Property\DataClassProperties;
use Chamilo\Libraries\Storage\DataManager\Repository\DataClassRepository;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassDistinctParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Parameters\RecordRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\ComparisonCondition;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\Condition\OrCondition;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 * The repository wrapper for the user data manager
 *
 * @package user
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class UserRepository implements UserRepositoryInterface
{

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
     * @param integer $id
     *
     * @return \Chamilo\Core\User\Storage\DataClass\User|\Chamilo\Libraries\Storage\DataClass\DataClass
     */
    public function findUserByIdentifier($id)
    {
        return $this->getDataClassRepository()->retrieveById(User::class, $id);
    }

    /**
     * @param string $securityToken
     *
     * @return \Chamilo\Core\User\Storage\DataClass\User
     */
    public function findUserBySecurityToken($securityToken)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(User::class, User::PROPERTY_SECURITY_TOKEN),
            new StaticConditionVariable($securityToken)
        );

        return $this->getDataClassRepository()->retrieve(User::class, new DataClassRetrieveParameters($condition));
    }

    /**
     * @param string $officialCode
     *
     * @return \Chamilo\Core\User\Storage\DataClass\User
     */
    public function findUserByOfficialCode($officialCode)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(User::class, User::PROPERTY_OFFICIAL_CODE),
            new StaticConditionVariable($officialCode)
        );

        return $this->getDataClassRepository()->retrieve(User::class, new DataClassRetrieveParameters($condition));
    }

    /**
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition|null $condition
     * @param null $count
     * @param null $offset
     * @param array $orderBy
     *
     * @return \Chamilo\Core\User\Storage\DataClass\User[]
     */
    public function findUsers(Condition $condition = null, $count = null, $offset = null, $orderBy = array())
    {
        $parameters = new DataClassRetrievesParameters($condition, $count, $offset, $orderBy);

        return $this->getDataClassRepository()->retrieves(User::class, $parameters);
    }

    /**
     * @return integer[]
     * @throws \Exception
     */
    public function findUserIdentifiers()
    {
        $dataClassProperties = new DataClassProperties();
        $dataClassProperties->add(new PropertyConditionVariable(User::class, User::PROPERTY_ID));

        return $this->getDataClassRepository()->distinct(
            User::class, new DataClassDistinctParameters(null, $dataClassProperties)
        );
    }

    /**
     * @param integer[] $userIdentifiers
     *
     * @return \Chamilo\Core\User\Storage\DataClass\User[]|\Chamilo\Libraries\Storage\Iterator\DataClassIterator
     */
    public function findUsersByIdentifiersOrderedByName($userIdentifiers)
    {
        $orderProperties = array();
        $orderProperties[] = new OrderBy(new PropertyConditionVariable(User::class, User::PROPERTY_LASTNAME));
        $orderProperties[] = new OrderBy(new PropertyConditionVariable(User::class, User::PROPERTY_FIRSTNAME));

        $condition = new InCondition(new PropertyConditionVariable(User::class, User::PROPERTY_ID), $userIdentifiers);

        return $this->getDataClassRepository()->retrieves(
            User::class, new DataClassRetrievesParameters($condition, null, null, $orderProperties)
        );
    }

    /**
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     *
     * @return integer
     */
    public function countUsers(Condition $condition = null)
    {
        return $this->getDataClassRepository()->count(User::class, new DataClassCountParameters($condition));
    }

    /**
     * @param string $email
     *
     * @return \Chamilo\Core\User\Storage\DataClass\User
     */
    public function findUserByEmail($email)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(User::class, User::PROPERTY_EMAIL), new StaticConditionVariable($email)
        );

        return $this->getDataClassRepository()->retrieve(User::class, new DataClassRetrieveParameters($condition));
    }

    /**
     * @param string $username
     *
     * @return \Chamilo\Core\User\Storage\DataClass\User
     */
    public function findUserByUsername($username)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(User::class, User::PROPERTY_USERNAME), new StaticConditionVariable($username)
        );

        return $this->getDataClassRepository()->retrieve(User::class, new DataClassRetrieveParameters($condition));
    }

    /**
     * @param string $usernameOrEmail
     *
     * @return \Chamilo\Core\User\Storage\DataClass\User
     */
    public function findUserByUsernameOrEmail($usernameOrEmail)
    {
        $conditions = array();

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(User::class_name(), User::PROPERTY_EMAIL),
            new StaticConditionVariable($usernameOrEmail)
        );
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(User::class_name(), User::PROPERTY_USERNAME),
            new StaticConditionVariable($usernameOrEmail)
        );

        return $this->getDataClassRepository()->retrieve(
            User::class_name(), new DataClassRetrieveParameters(new OrCondition($conditions))
        );
    }

    /**
     * @param integer $status
     *
     * @return \Chamilo\Core\User\Storage\DataClass\User[]
     */
    public function findActiveUsersByStatus($status)
    {
        $conditions = array();
        $conditions[] = new ComparisonCondition(
            new PropertyConditionVariable(User::class, User::PROPERTY_STATUS), ComparisonCondition::EQUAL,
            new StaticConditionVariable($status)
        );
        $conditions[] = new ComparisonCondition(
            new PropertyConditionVariable(User::class, User::PROPERTY_ACTIVE), ComparisonCondition::EQUAL,
            new StaticConditionVariable(1)
        );

        return $this->getDataClassRepository()->retrieves(
            User::class, new DataClassRetrievesParameters(new AndCondition($conditions))
        );
    }

    public function create(DataClass $dataClass)
    {
        return $dataClass->create();
    }

    public function update(DataClass $dataClass)
    {
        return $dataClass->update();
    }

    public function delete(DataClass $dataClass)
    {
        return $dataClass->delete();
    }

    /**
     * @param \Chamilo\Configuration\Storage\DataClass\Setting $setting
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @return \Chamilo\Core\User\Storage\DataClass\UserSetting
     */
    public function getUserSettingForSettingAndUser(Setting $setting, User $user)
    {
        $conditions = array();

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(UserSetting::class, UserSetting::PROPERTY_USER_ID),
            new StaticConditionVariable($user->getId())
        );

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(UserSetting::class, UserSetting::PROPERTY_SETTING_ID),
            new StaticConditionVariable($setting->getId())
        );

        $condition = new AndCondition($conditions);

        return $this->getDataClassRepository()->retrieve(
            UserSetting::class, new DataClassRetrieveParameters($condition)
        );
    }

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\UserSetting $userSetting
     *
     * @return boolean
     * @throws \Exception
     */
    public function createUserSetting(UserSetting $userSetting)
    {
        return $this->getDataClassRepository()->create($userSetting);
    }

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\UserSetting $userSetting
     *
     * @return boolean
     */
    public function updateUserSetting(UserSetting $userSetting)
    {
        return $this->getDataClassRepository()->update($userSetting);
    }

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\UserSetting $userSetting
     *
     * @return boolean
     */
    public function deleteUserSetting(UserSetting $userSetting)
    {
        return $this->getDataClassRepository()->delete($userSetting);
    }

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @return boolean
     * @throws \Exception
     */
    public function createUser(User $user)
    {
        return $this->getDataClassRepository()->create($user);
    }

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @return boolean
     */
    public function updateUser(User $user)
    {
        return $this->getDataClassRepository()->update($user);
    }

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @return boolean
     */
    public function deleteUser(User $user)
    {
        // TODO: $user->delete() still implements some business logic
        // return $this->getDataClassRepository()->delete($user);
        return $user->delete();
    }
}