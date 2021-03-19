<?php

namespace Chamilo\Core\User\Storage\Repository;

use Chamilo\Core\Tracking\Storage\DataClass\Event;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Core\User\Storage\DataClass\UserSetting;
use Chamilo\Core\User\Storage\DataManager;
use Chamilo\Core\User\Storage\Repository\Interfaces\UserRepositoryInterface;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\DataClass\Property\DataClassProperties;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\FilterParameters\FilterParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\ComparisonCondition;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\Condition\OrCondition;
use Chamilo\Libraries\Storage\Query\FilterParametersTranslator;
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
     * @var FilterParametersTranslator
     */
    protected $filterParametersTranslator;

    /**
     * UserRepository constructor.
     *
     * @param FilterParametersTranslator $filterParametersTranslator
     */
    public function __construct(FilterParametersTranslator $filterParametersTranslator = null)
    {
        if(empty($filterParametersTranslator))
        {
            $filterParametersTranslator = new FilterParametersTranslator();
        }

        $this->filterParametersTranslator = $filterParametersTranslator;
    }

    /**
     * Finds a user by a given id
     *
     * @param int $id
     *
     * @return \Chamilo\Core\User\Storage\DataClass\User|DataClass
     */
    public function findUserById($id)
    {
        return DataManager::retrieve_by_id(User::class_name(), $id);
    }

    /**
     * @param string $securityToken
     *
     * @return \Chamilo\Core\User\Storage\DataClass\User
     */
    public function findUserBySecurityToken($securityToken)
    {
        return DataManager::retrieve_user_by_security_token($securityToken);
    }

    /**
     * @param string $officialCode
     *
     * @return \Chamilo\Core\User\Storage\DataClass\User
     */
    public function findUserByOfficialCode($officialCode)
    {
        return DataManager::retrieve_user_by_official_code($officialCode);
    }

    /**
     * Finds a user by a list of parameters
     *
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     * @param int $count
     * @param int $offset
     * @param OrderBy[] $order_by
     *
     * @return User[]
     */
    public function findUsers(Condition $condition = null, $count = null, $offset = null, $order_by = array())
    {
        $parameters = new DataClassRetrievesParameters($condition, $count, $offset, $order_by);

        return DataManager::retrieves(User::class_name(), $parameters)->as_array();
    }

    /**
     * @param int[] $userIdentifiers
     *
     * @return \Chamilo\Core\User\Storage\DataClass\User[]
     */
    public function findUsersByIdentifiersOrderedByName($userIdentifiers)
    {
        $orderProperties = array();
        $orderProperties[] = new OrderBy(new PropertyConditionVariable(User::class_name(), User::PROPERTY_LASTNAME));
        $orderProperties[] = new OrderBy(new PropertyConditionVariable(User::class_name(), User::PROPERTY_FIRSTNAME));

        $condition =
            new InCondition(new PropertyConditionVariable(User::class_name(), User::PROPERTY_ID), $userIdentifiers);

        /** @var User[] $users */
        $users = DataManager::retrieves(
            User::class_name(),
            new DataClassRetrievesParameters($condition, null, null, $orderProperties)
        )->as_array();

        return $users;
    }

    /**
     *
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     *
     * @return integer
     */
    public function countUsers(Condition $condition = null)
    {
        $parameters = new DataClassCountParameters($condition);

        return DataManager::count(User::class_name(), $parameters);
    }

    /**
     * Finds a user by a given email
     *
     * @param string $email
     *
     * @return User;
     */
    public function findUserByEmail($email)
    {
        $condition = new ComparisonCondition(
            new PropertyConditionVariable(User::class_name(), User::PROPERTY_EMAIL),
            ComparisonCondition::EQUAL,
            new StaticConditionVariable($email)
        );

        $users = $this->findUsers($condition);

        return $users[0];
    }

    /**
     * Finds a user by a given username
     *
     * @param string $username
     *
     * @return \Chamilo\Core\User\Storage\DataClass\User
     */
    public function findUserByUsername($username)
    {
        return \Chamilo\Core\User\Storage\DataManager::retrieve_user_by_username($username);
    }

    /**
     * @param string $usernameOrEmail
     *
     * @return \Chamilo\Core\User\Storage\DataClass\User
     */
    public function findUserByUsernameOrEmail($usernameOrEmail)
    {
        return \Chamilo\Core\User\Storage\DataManager::retrieveUserByUsernameOrEmail($usernameOrEmail);
    }

    /**
     *
     * @return User[]
     */
    public function findActiveStudents()
    {
        return $this->findActiveUsersByStatus(User::STATUS_STUDENT);
    }

    /**
     *
     * @return User[]
     */
    public function findActiveTeachers()
    {
        return $this->findActiveUsersByStatus(User::STATUS_TEACHER);
    }

    /**
     *
     * @param $status
     *
     * @return User[]
     */
    protected function findActiveUsersByStatus($status)
    {
        $conditions = array();
        $conditions[] = new ComparisonCondition(
            new PropertyConditionVariable(User::class_name(), User::PROPERTY_STATUS),
            ComparisonCondition::EQUAL,
            new StaticConditionVariable($status)
        );
        $conditions[] = new ComparisonCondition(
            new PropertyConditionVariable(User::class_name(), User::PROPERTY_ACTIVE),
            ComparisonCondition::EQUAL,
            new StaticConditionVariable(1)
        );

        $parameters = new DataClassRetrievesParameters(new AndCondition($conditions));

        /**
         *
         * @var User[] $users
         */
        $users = DataManager::retrieves(User::class_name(), $parameters)->as_array();

        return $users;
    }

    /**
     * @param DataClass $dataClass
     *
     * @return bool
     */
    public function create(DataClass $dataClass)
    {
        return $dataClass->create();
    }

    /**
     * @param DataClass $dataClass
     *
     * @return bool
     * @throws \Exception
     */
    public function update(DataClass $dataClass)
    {
        return $dataClass->update();
    }

    public function delete(DataClass $dataClass)
    {
        return $dataClass->delete();
    }

    /**
     * @param $context
     * @param $variable
     * @param User $user
     *
     * @return DataClass|UserSetting
     */
    public function getUserSettingForSettingAndUser($context, $variable, User $user)
    {
        $setting = \Chamilo\Configuration\Storage\DataManager::retrieve_setting_from_variable_name($variable, $context);

        $conditions = array();

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(UserSetting::class_name(), UserSetting::PROPERTY_USER_ID),
            new StaticConditionVariable($user->getId())
        );

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(UserSetting::class_name(), UserSetting::PROPERTY_SETTING_ID),
            new StaticConditionVariable($setting->getId())
        );

        $condition = new AndCondition($conditions);

        return \Chamilo\Core\User\Storage\DataManager::retrieve(
            UserSetting::class_name(),
            new DataClassRetrieveParameters($condition)
        );
    }

    /**
     * @param $context
     * @param $variable
     * @param User $user
     * @param null $value
     *
     * @return bool
     * @throws \Exception
     */
    public function createUserSettingForSettingAndUser($context, $variable, User $user, $value = null)
    {
        $userSetting = $this->getUserSettingForSettingAndUser($context, $variable, $user);
        if (!$userSetting instanceof UserSetting)
        {
            $setting = \Chamilo\Configuration\Storage\DataManager::retrieve_setting_from_variable_name(
                $variable,
                $context
            );

            $userSetting = new UserSetting();
            $userSetting->set_setting_id($setting->getId());
            $userSetting->set_user_id($user->getId());
            $userSetting->set_value($value);

            return $this->create($userSetting);
        }
        else
        {
            $userSetting->set_value($value);

            return $this->update($userSetting);
        }
    }

    public function triggerImportEvent(User $actionUser, User $targetUser)
    {
        Event::trigger(
            'Import',
            'Chamilo\Core\User',
            ['target_user_id' => $targetUser->getId(), 'action_user_id' => $actionUser->getId()]
        );
    }

    /**
     * @param FilterParameters $filterParameters
     *
     * @return \Chamilo\Libraries\Storage\ResultSet\ResultSet|User[]
     */
    public function findUsersByParameters(FilterParameters $filterParameters)
    {
        $searchProperties = new DataClassProperties();
        $searchProperties->add(new PropertyConditionVariable(User::class_name(), User::PROPERTY_FIRSTNAME));
        $searchProperties->add(new PropertyConditionVariable(User::class_name(), User::PROPERTY_LASTNAME));
        $searchProperties->add(new PropertyConditionVariable(User::class_name(), User::PROPERTY_USERNAME));
        $searchProperties->add(new PropertyConditionVariable(User::class_name(), User::PROPERTY_OFFICIAL_CODE));
        $searchProperties->add(new PropertyConditionVariable(User::class_name(), User::PROPERTY_EMAIL));

        $dataClassParameters = new DataClassRetrievesParameters();

        $this->filterParametersTranslator->translateFilterParameters(
            $filterParameters, $searchProperties, $dataClassParameters
        );

        return DataManager::retrieves(User::class, $dataClassParameters)->as_array();
    }

    /**
     * @param string $usernameOfficialCodeOrEmail
     *
     * @return DataClass|User
     * @throws \Chamilo\Libraries\Architecture\Exceptions\UserException
     */
    public function findUserByUsernameOfficialCodeOrEmail(string $usernameOfficialCodeOrEmail)
    {
        $conditions = [];

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(User::class, User::PROPERTY_USERNAME),
            new StaticConditionVariable($usernameOfficialCodeOrEmail)
        );

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(User::class, User::PROPERTY_EMAIL),
            new StaticConditionVariable($usernameOfficialCodeOrEmail)
        );

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(User::class, User::PROPERTY_OFFICIAL_CODE),
            new StaticConditionVariable($usernameOfficialCodeOrEmail)
        );

        $condition = new OrCondition($conditions);

        $dataClassParameters = new DataClassRetrieveParameters($condition);

        return DataManager::retrieve(User::class, $dataClassParameters);
    }
}
