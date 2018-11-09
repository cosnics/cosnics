<?php
namespace Chamilo\Core\User\Service;

use Chamilo\Configuration\Service\ConfigurationService;
use Chamilo\Core\Tracking\Storage\DataClass\Event;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Core\User\Storage\DataClass\UserSetting;
use Chamilo\Core\User\Storage\Repository\UserRepository;
use Chamilo\Libraries\Hashing\HashingUtilities;

/**
 *
 * @package Chamilo\Core\User\Service
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 */
class UserService
{

    /**
     *
     * @var \Chamilo\Core\User\Storage\Repository\UserRepository
     */
    private $userRepository;

    /**
     *
     * @var \Chamilo\Libraries\Hashing\HashingUtilities
     */
    private $hashingUtilities;

    /**
     *
     * @var \Chamilo\Configuration\Service\ConfigurationService
     */
    private $configurationService;

    /**
     *
     * @param \Chamilo\Core\User\Storage\Repository\UserRepository $userRepository
     * @param \Chamilo\Libraries\Hashing\HashingUtilities $hashingUtilities
     * @param \Chamilo\Configuration\Service\ConfigurationService $configurationService
     */
    public function __construct(UserRepository $userRepository, HashingUtilities $hashingUtilities,
        ConfigurationService $configurationService)
    {
        $this->userRepository = $userRepository;
        $this->hashingUtilities = $hashingUtilities;
        $this->configurationService = $configurationService;
    }

    /**
     *
     * @return \Chamilo\Core\User\Storage\Repository\UserRepository
     */
    protected function getUserRepository()
    {
        return $this->userRepository;
    }

    /**
     *
     * @param \Chamilo\Core\User\Storage\Repository\UserRepository $archiveRepository
     */
    protected function setUserRepository(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     *
     * @return \Chamilo\Libraries\Hashing\HashingUtilities
     */
    protected function getHashingUtilities()
    {
        return $this->hashingUtilities;
    }

    /**
     *
     * @param \Chamilo\Libraries\Hashing\HashingUtilities $hashingUtilities
     */
    protected function setHashingUtilities(HashingUtilities $hashingUtilities)
    {
        $this->hashingUtilities = $hashingUtilities;
    }

    /**
     *
     * @return \Chamilo\Configuration\Service\ConfigurationService
     */
    public function getConfigurationService()
    {
        return $this->configurationService;
    }

    /**
     *
     * @param \Chamilo\Configuration\Service\ConfigurationService $configurationService
     */
    public function setConfigurationService(ConfigurationService $configurationService)
    {
        $this->configurationService = $configurationService;
    }

    /**
     *
     * @param integer $identifier
     *
     * @return \Chamilo\Core\User\Storage\DataClass\User
     */
    public function findUserByIdentifier($identifier)
    {
        return $this->getUserRepository()->findUserByIdentifier($identifier);
    }

    /**
     *
     * @param string $securityToken
     * @return \Chamilo\Core\User\Storage\DataClass\User
     */
    public function findUserBySecurityToken($securityToken)
    {
        return $this->getUserRepository()->findUserBySecurityToken($securityToken);
    }

    /**
     *
     * @param string $officialCode
     * @return \Chamilo\Core\User\Storage\DataClass\User
     */
    public function findUserByOfficialCode($officialCode)
    {
        return $this->getUserRepository()->findUserByOfficialCode($officialCode);
    }

    /**
     *
     * @param integer[] $userIdentifiers
     * @return \Chamilo\Libraries\Storage\Iterator\DataClassIterator
     */
    public function findUsersByIdentifiersOrderedByName($userIdentifiers)
    {
        return $this->getUserRepository()->findUsersByIdentifiersOrderedByName($userIdentifiers);
    }

    /**
     *
     * @param string $email
     * @return \Chamilo\Core\User\Storage\DataClass\User
     */
    public function findUserByEmail($email)
    {
        return $this->getUserRepository()->findUserByEmail($email);
    }

    /**
     *
     * @param string $usernameOrEmail
     * @return \Chamilo\Core\User\Storage\DataClass\User
     */
    public function findUserByUsernameOrEmail($usernameOrEmail)
    {
        return $this->getUserRepository()->findUserByUsernameOrEmail($usernameOrEmail);
    }

    /**
     *
     * @param int $identifier
     *
     * @return null|string
     */
    public function getUserFullNameByIdentifier($identifier)
    {
        $user = $this->findUserByIdentifier($identifier);

        if (! $user instanceof User)
        {
            return null;
        }

        return $user->get_fullname();
    }

    /**
     * Retrieves a user by a given security token
     *
     * @param string $securityToken
     *
     * @return \Chamilo\Core\User\Storage\DataClass\User
     */
    public function getUserBySecurityToken($securityToken)
    {
        return $this->getUserRepository()->findUserBySecurityToken($securityToken);
    }

    /**
     * Retrieves a user by a given official code
     *
     * @param string $officialCode
     *
     * @return \Chamilo\Core\User\Storage\DataClass\User
     */
    public function getUserByOfficialCode($officialCode)
    {
        return $this->getUserRepository()->findUserByOfficialCode($officialCode);
    }

    /**
     *
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition|null $condition
     * @param integer $offset
     * @param integer $count
     * @param \Chamilo\Libraries\Storage\Query\OrderBy[] $orderProperty
     *
     * @return \Chamilo\Core\User\Storage\DataClass\User[]
     */
    public function findUsers($condition, $offset = 0, $count = - 1, $orderProperty = null)
    {
        return $this->getUserRepository()->findUsers($condition, $count, $offset, $orderProperty);
    }

    /**
     *
     * @param int[] $userIdentifiers
     *
     * @return \Chamilo\Core\User\Storage\DataClass\User[]
     */
    public function findUsersByIdentifiers($userIdentifiers)
    {
        if (empty($userIdentifiers))
        {
            return [];
        }

        return $this->getUserRepository()->findUsersByIdentifiersOrderedByName($userIdentifiers);
    }

    /**
     *
     * @param string $username
     *
     * @return \Chamilo\Core\User\Storage\DataClass\User
     */
    public function findUserByUsername($username)
    {
        return $this->getUserRepository()->findUserByUsername($username);
    }

    /**
     *
     * @param $usernameOrEmail
     * @return User
     */
    public function getUserByUsernameOrEmail($usernameOrEmail)
    {
        return $this->getUserRepository()->findUserByUsernameOrEmail($usernameOrEmail);
    }

    /**
     *
     * @param string $username
     *
     * @return bool
     */
    public function isUsernameAvailable($username)
    {
        return ! $this->findUserByUsername($username) instanceof User;
    }

    /**
     *
     * @param string $firstName
     * @param string $lastName
     * @param string $username
     * @param string $officialCode
     * @param string $emailAddress
     * @param string $password
     * @param string $authSource
     *
     * @return \Chamilo\Core\User\Storage\DataClass\User
     */
    public function createUserFromParameters($firstName, $lastName, $username, $officialCode, $emailAddress, $password,
        $authSource = 'Platform')
    {
        $requiredParameters = [
            'firstName' => $firstName,
            'lastName' => $lastName,
            'username' => $username,
            'officialCode' => $officialCode,
            'emailAddress' => $emailAddress,
            'password' => $password];

        foreach ($requiredParameters as $parameterName => $parameterValue)
        {
            if (empty($parameterValue))
            {
                throw new \InvalidArgumentException('The ' . $parameterName . ' can not be empty');
            }
        }

        if (! $this->isUsernameAvailable($username))
        {
            throw new \RuntimeException('The given username is already taken');
        }

        $user = new User();

        $user->set_firstname($firstName);
        $user->set_lastname($lastName);
        $user->set_username($username);
        $user->set_official_code($officialCode);
        $user->set_email($emailAddress);
        $user->set_auth_source($authSource);

        $user->set_password($this->getHashingUtilities()->hashString($password));

        if (! $this->createUser($user))
        {
            throw new \RuntimeException('Could not create the user');
        }

        return $user;
    }

    /**
     *
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition|null $condition
     *
     * @return integer
     */
    public function countUsers($condition)
    {
        return $this->getUserRepository()->countUsers($condition);
    }

    /**
     *
     * @param string $context
     * @param string $variable
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @return \Chamilo\Core\User\Storage\DataClass\UserSetting
     */
    public function getUserSettingForSettingContextVariableAndUser($context, $variable, User $user)
    {
        return $this->getUserRepository()->getUserSettingForSettingAndUser(
            $this->getConfigurationService()->findSettingByContextAndVariableName($context, $variable),
            $user);
    }

    /**
     *
     * @param string $context
     * @param string $variable
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param string $value
     * @return boolean
     */
    public function createUserSettingForSettingAndUser($context, $variable, User $user, $value = null)
    {
        $userSetting = $this->getUserSettingForSettingContextVariableAndUser($context, $variable, $user);

        if (! $userSetting instanceof UserSetting)
        {
            $setting = $this->getConfigurationService()->findSettingByContextAndVariableName($context, $variable);

            $userSetting = new UserSetting();
            $userSetting->set_setting_id($setting->getId());
            $userSetting->set_user_id($user->getId());
            $userSetting->set_value($value);

            return $this->getUserRepository()->createUserSetting($userSetting);
        }
        else
        {
            $userSetting->set_value($value);

            return $this->getUserRepository()->updateUserSetting($userSetting);
        }
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
     * @param integer $status
     * @return \Chamilo\Core\User\Storage\DataClass\User[]
     */
    public function findActiveUsersByStatus($status)
    {
        return $this->getUserRepository()->findActiveUsersByStatus($status);
    }

    /**
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $actionUser
     * @param \Chamilo\Core\User\Storage\DataClass\User $targetUser
     */
    public function triggerImportEvent(User $actionUser, User $targetUser)
    {
        event::trigger(
            'Import',
            'Chamilo\Core\User',
            ['target_user_id' => $targetUser->getId(), 'action_user_id' => $actionUser->getId()]);
    }

    /**
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @return boolean
     */
    public function createUser(User $user)
    {
        $user->set_registration_date(time());
        $user->set_security_token(sha1(time() . uniqid()));

        return $this->getUserRepository()->createUser($user);
    }

    /**
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @return boolean
     */
    public function updateUser(User $user)
    {
        return $this->getUserRepository()->updateUser($user);
    }
}

