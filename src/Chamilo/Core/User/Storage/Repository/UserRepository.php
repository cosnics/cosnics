<?php
namespace Chamilo\Core\User\Storage\Repository;

use Chamilo\Configuration\Storage\DataClass\Setting;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Core\User\Storage\DataClass\UserSetting;
use Chamilo\Core\User\Storage\DataManager;
use Chamilo\Core\User\Storage\Repository\Interfaces\UserRepositoryInterface;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\DataManager\Repository\DataClassRepository;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\ComparisonCondition;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
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
     *
     * @see \Chamilo\Core\User\Storage\Repository\Interfaces\UserRepositoryInterface::findUserByIdentifier()
     */
    public function findUserByIdentifier($id)
    {
        return $this->getDataClassRepository()->retrieveById(User::class, $id);
    }

    /**
     *
     * @see \Chamilo\Core\User\Storage\Repository\Interfaces\UserRepositoryInterface::findUserBySecurityToken()
     */
    public function findUserBySecurityToken($securityToken)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(User::class, User::PROPERTY_SECURITY_TOKEN),
            new StaticConditionVariable($securityToken));

        return $this->getDataClassRepository()->retrieve(User::class, new DataClassRetrieveParameters($condition));
    }

    /**
     *
     * @see \Chamilo\Core\User\Storage\Repository\Interfaces\UserRepositoryInterface::findUserByOfficialCode()
     */
    public function findUserByOfficialCode($officialCode)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(User::class, User::PROPERTY_OFFICIAL_CODE),
            new StaticConditionVariable($officialCode));

        return $this->getDataClassRepository()->retrieve(User::class, new DataClassRetrieveParameters($condition));
    }

    /**
     *
     * @see \Chamilo\Core\User\Storage\Repository\Interfaces\UserRepositoryInterface::findUsers()
     */
    public function findUsers(Condition $condition = null, $count = null, $offset = null, $orderBy = array())
    {
        $parameters = new DataClassRetrievesParameters($condition, $count, $offset, $orderBy);

        return $this->getDataClassRepository()->retrieves(User::class, $parameters);
    }

    /**
     *
     * @see \Chamilo\Core\User\Storage\Repository\Interfaces\UserRepositoryInterface::findUsersByIdentifiersOrderedByName()
     */
    public function findUsersByIdentifiersOrderedByName($userIdentifiers)
    {
        $orderProperties = array();
        $orderProperties[] = new OrderBy(new PropertyConditionVariable(User::class, User::PROPERTY_LASTNAME));
        $orderProperties[] = new OrderBy(new PropertyConditionVariable(User::class, User::PROPERTY_FIRSTNAME));

        $condition = new InCondition(new PropertyConditionVariable(User::class, User::PROPERTY_ID), $userIdentifiers);

        return $this->getDataClassRepository()->retrieves(
            User::class,
            new DataClassRetrievesParameters($condition, null, null, $orderProperties));
    }

    /**
     *
     * @see \Chamilo\Core\User\Storage\Repository\Interfaces\UserRepositoryInterface::countUsers()
     */
    public function countUsers(Condition $condition = null)
    {
        return $this->getDataClassRepository()->count(User::class, new DataClassCountParameters($condition));
    }

    /**
     *
     * @see \Chamilo\Core\User\Storage\Repository\Interfaces\UserRepositoryInterface::findUserByEmail()
     */
    public function findUserByEmail($email)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(User::class, User::PROPERTY_EMAIL),
            new StaticConditionVariable($email));

        return $this->getDataClassRepository()->retrieve(User::class, new DataClassRetrieveParameters($condition));
    }

    /**
     *
     * @see \Chamilo\Core\User\Storage\Repository\Interfaces\UserRepositoryInterface::findUserByUsername()
     */
    public function findUserByUsername($username)
    {
        return \Chamilo\Core\User\Storage\DataManager::retrieve_user_by_username($username);
    }

    /**
     *
     * @see \Chamilo\Core\User\Storage\Repository\Interfaces\UserRepositoryInterface::findUserByUsernameOrEmail()
     */
    public function findUserByUsernameOrEmail($usernameOrEmail)
    {
        return \Chamilo\Core\User\Storage\DataManager::retrieveUserByUsernameOrEmail($usernameOrEmail);
    }

    /**
     *
     * @see \Chamilo\Core\User\Storage\Repository\Interfaces\UserRepositoryInterface::findActiveUsersByStatus()
     */
    public function findActiveUsersByStatus($status)
    {
        $conditions = array();
        $conditions[] = new ComparisonCondition(
            new PropertyConditionVariable(User::class, User::PROPERTY_STATUS),
            ComparisonCondition::EQUAL,
            new StaticConditionVariable($status));
        $conditions[] = new ComparisonCondition(
            new PropertyConditionVariable(User::class, User::PROPERTY_ACTIVE),
            ComparisonCondition::EQUAL,
            new StaticConditionVariable(1));

        return $this->getDataClassRepository()->retrieves(
            User::class,
            new DataClassRetrievesParameters(new AndCondition($conditions)));
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
     *
     * @see \Chamilo\Core\User\Storage\Repository\Interfaces\UserRepositoryInterface::getUserSettingForSettingAndUser()
     */
    public function getUserSettingForSettingAndUser(Setting $setting, User $user)
    {
        $conditions = array();

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(UserSetting::class, UserSetting::PROPERTY_USER_ID),
            new StaticConditionVariable($user->getId()));

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(UserSetting::class, UserSetting::PROPERTY_SETTING_ID),
            new StaticConditionVariable($setting->getId()));

        $condition = new AndCondition($conditions);

        return $this->getDataClassRepository()->retrieve(
            UserSetting::class,
            new DataClassRetrieveParameters($condition));
    }

    /**
     *
     * @see \Chamilo\Core\User\Storage\Repository\Interfaces\UserRepositoryInterface::createUserSetting()
     */
    public function createUserSetting(UserSetting $userSetting)
    {
        return $this->getDataClassRepository()->create($userSetting);
    }

    /**
     *
     * @see \Chamilo\Core\User\Storage\Repository\Interfaces\UserRepositoryInterface::updateUserSetting()
     */
    public function updateUserSetting(UserSetting $userSetting)
    {
        return $this->getDataClassRepository()->update($userSetting);
    }

    /**
     *
     * @see \Chamilo\Core\User\Storage\Repository\Interfaces\UserRepositoryInterface::createUser()
     */
    public function createUser(User $user)
    {
        return $this->getDataClassRepository()->create($user);
    }

    /**
     *
     * @see \Chamilo\Core\User\Storage\Repository\Interfaces\UserRepositoryInterface::updateUser()
     */
    public function updateUser(User $user)
    {
        return $this->getDataClassRepository()->update($user);
    }
}