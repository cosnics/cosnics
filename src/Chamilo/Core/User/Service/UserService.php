<?php
namespace Chamilo\Core\User\Service;

use Chamilo\Configuration\Service\ConfigurationService;
use Chamilo\Core\Tracking\Storage\DataClass\Event;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Core\User\Storage\DataClass\UserSetting;
use Chamilo\Core\User\Storage\Repository\UserRepository;
use Chamilo\Libraries\Hashing\HashingUtilities;
use Chamilo\Libraries\Storage\DataClass\PropertyMapper;
use InvalidArgumentException;
use RuntimeException;

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
     * @var \Chamilo\Libraries\Storage\DataClass\PropertyMapper
     */
    private $propertyMapper;

    /**
     *
     * @param \Chamilo\Core\User\Storage\Repository\UserRepository $userRepository
     * @param \Chamilo\Libraries\Hashing\HashingUtilities $hashingUtilities
     * @param \Chamilo\Configuration\Service\ConfigurationService $configurationService
     * @param \Chamilo\Libraries\Storage\DataClass\PropertyMapper $propertyMapper
     */
    public function __construct(
        UserRepository $userRepository, HashingUtilities $hashingUtilities, ConfigurationService $configurationService,
        PropertyMapper $propertyMapper
    )
    {
        $this->userRepository = $userRepository;
        $this->hashingUtilities = $hashingUtilities;
        $this->configurationService = $configurationService;
        $this->propertyMapper = $propertyMapper;
    }

    /**
     *
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     *
     * @return integer
     */
    public function countUsers($condition)
    {
        return $this->getUserRepository()->countUsers($condition);
    }

    /**
     * @param string $searchQuery
     *
     * @return integer
     */
    public function countUsersForSearchQuery(string $searchQuery = null)
    {
        return $this->getUserRepository()->countUsersForSearchQuery($searchQuery);
    }

    /**
     * @param string $searchQuery
     * @param integer[] $userIdentifiers
     *
     * @return integer
     */
    public function countUsersForSearchQueryAndUserIdentifiers(
        string $searchQuery = null, array $userIdentifiers = array()
    )
    {
        return $this->getUserRepository()->countUsersForSearchQueryAndUserIdentifiers($searchQuery, $userIdentifiers);
    }

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @return boolean
     * @throws \Exception
     */
    public function createUser(User $user)
    {
        $user->set_registration_date(time());
        $user->set_security_token(sha1(time() . uniqid()));

        return $this->getUserRepository()->createUser($user);
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
     * @param string $status
     *
     * @return \Chamilo\Core\User\Storage\DataClass\User
     * @throws \Exception
     */
    public function createUserFromParameters(
        $firstName, $lastName, $username, $officialCode, $emailAddress, $password, $authSource = 'Platform',
        $status = User::STATUS_STUDENT
    )
    {
        $requiredParameters = [
            'firstName' => $firstName,
            'lastName' => $lastName,
            'username' => $username,
            'officialCode' => $officialCode,
            'emailAddress' => $emailAddress,
            'password' => $password
        ];

        foreach ($requiredParameters as $parameterName => $parameterValue)
        {
            if (empty($parameterValue))
            {
                throw new InvalidArgumentException('The ' . $parameterName . ' can not be empty');
            }
        }

        if (!$this->isUsernameAvailable($username))
        {
            throw new RuntimeException('The given username is already taken');
        }

        $user = new User();

        $user->set_firstname($firstName);
        $user->set_lastname($lastName);
        $user->set_username($username);
        $user->set_official_code($officialCode);
        $user->set_email($emailAddress);
        $user->set_auth_source($authSource);
        $user->set_status($status);

        $user->set_password($this->getHashingUtilities()->hashString($password));

        if (!$this->createUser($user))
        {
            throw new RuntimeException('Could not create the user');
        }

        return $user;
    }

    /**
     *
     * @param string $context
     * @param string $variable
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param string $value
     *
     * @return boolean
     * @throws \Exception
     */
    public function createUserSettingForSettingAndUser($context, $variable, User $user, $value = null)
    {
        $userSetting = $this->getUserSettingForSettingContextVariableAndUser($context, $variable, $user);

        if (!$userSetting instanceof UserSetting)
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
     *
     * @return \Chamilo\Core\User\Storage\DataClass\User[]
     */
    public function findActiveUsersByStatus($status)
    {
        return $this->getUserRepository()->findActiveUsersByStatus($status);
    }

    /**
     *
     * @return \Chamilo\Core\User\Storage\DataClass\User[]
     */
    public function findPlatformAdministrators()
    {
        return $this->getUserRepository()->findPlatformAdministrators();
    }

    /**
     *
     * @param string $email
     *
     * @return \Chamilo\Core\User\Storage\DataClass\User
     */
    public function findUserByEmail($email)
    {
        return $this->getUserRepository()->findUserByEmail($email);
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
     * @param string $officialCode
     *
     * @return \Chamilo\Core\User\Storage\DataClass\User
     */
    public function findUserByOfficialCode($officialCode)
    {
        return $this->getUserRepository()->findUserByOfficialCode($officialCode);
    }

    /**
     *
     * @param string $securityToken
     *
     * @return \Chamilo\Core\User\Storage\DataClass\User
     */
    public function findUserBySecurityToken($securityToken)
    {
        return $this->getUserRepository()->findUserBySecurityToken($securityToken);
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
     * @param string $usernameOrEmail
     *
     * @return \Chamilo\Core\User\Storage\DataClass\User
     */
    public function findUserByUsernameOrEmail($usernameOrEmail)
    {
        return $this->getUserRepository()->findUserByUsernameOrEmail($usernameOrEmail);
    }

    /**
     * @return integer[]
     * @throws \Exception
     */
    public function findUserIdentifiers()
    {
        return $this->getUserRepository()->findUserIdentifiers();
    }

    /**
     *
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     * @param integer $offset
     * @param integer $count
     * @param \Chamilo\Libraries\Storage\Query\OrderBy[] $orderProperty
     *
     * @return \Chamilo\Core\User\Storage\DataClass\User[]
     */
    public function findUsers($condition = null, $offset = 0, $count = - 1, $orderProperty = null)
    {
        return $this->getUserRepository()->findUsers($condition, $count, $offset, $orderProperty);
    }

    /**
     *
     * @param integer[] $userIdentifiers
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
     * @param integer[] $userIdentifiers
     *
     * @return \Chamilo\Core\User\Storage\DataClass\User[]
     */
    public function findUsersByIdentifiersOrderedByName($userIdentifiers)
    {
        return $this->getUserRepository()->findUsersByIdentifiersOrderedByName($userIdentifiers);
    }

    /**
     * @param string $searchQuery
     * @param integer $offset
     * @param integer $count
     *
     * @return \Chamilo\Core\User\Storage\DataClass\User[]
     */
    public function findUsersForSearchQuery(string $searchQuery = null, int $offset = null, int $count = null)
    {
        return $this->getUserRepository()->findUsersForSearchQuery($searchQuery, $offset, $count);
    }

    /**
     * @param string $searchQuery
     * @param integer[] $userIdentifiers
     * @param integer $offset
     * @param integer $count
     *
     * @return \Chamilo\Core\User\Storage\DataClass\User[]
     */
    public function findUsersForSearchQueryAndUserIdentifiers(
        string $searchQuery = null, array $userIdentifiers = array(), int $offset = null, int $count = null
    )
    {
        return $this->getUserRepository()->findUsersForSearchQueryAndUserIdentifiers(
            $searchQuery, $userIdentifiers, $offset, $count
        );
    }

    /**
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     * @param integer $offset
     * @param integer $count
     * @param \Chamilo\Libraries\Storage\Query\OrderBy[] $orderProperty
     *
     * @return \Chamilo\Core\User\Storage\DataClass\User[]
     */
    public function findUsersMappedByOfficialCode($condition = null, $offset = 0, $count = - 1, $orderProperty = null)
    {
        return $this->getPropertyMapper()->mapDataClassByProperty(
            $this->findUsers($condition = null, $offset = 0, $count = - 1, $orderProperty = null),
            User::PROPERTY_OFFICIAL_CODE
        );
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
     * @return \Chamilo\Libraries\Storage\DataClass\PropertyMapper
     */
    public function getPropertyMapper(): PropertyMapper
    {
        return $this->propertyMapper;
    }

    /**
     * @param \Chamilo\Libraries\Storage\DataClass\PropertyMapper $propertyMapper
     */
    public function setPropertyMapper(PropertyMapper $propertyMapper): void
    {
        $this->propertyMapper = $propertyMapper;
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
     *
     * @param $usernameOrEmail
     *
     * @return User
     */
    public function getUserByUsernameOrEmail($usernameOrEmail)
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

        if (!$user instanceof User)
        {
            return null;
        }

        return $user->get_fullname();
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
     * @param \Chamilo\Core\User\Storage\Repository\UserRepository $userRepository
     */
    protected function setUserRepository(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     *
     * @param string $context
     * @param string $variable
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @return \Chamilo\Core\User\Storage\DataClass\UserSetting
     */
    public function getUserSettingForSettingContextVariableAndUser($context, $variable, User $user)
    {
        return $this->getUserRepository()->getUserSettingForSettingAndUser(
            $this->getConfigurationService()->findSettingByContextAndVariableName($context, $variable), $user
        );
    }

    /**
     *
     * @param string $username
     *
     * @return boolean
     */
    public function isUsernameAvailable($username)
    {
        return !$this->findUserByUsername($username) instanceof User;
    }

    /**
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $actionUser
     * @param \Chamilo\Core\User\Storage\DataClass\User $targetUser
     */
    public function triggerImportEvent(User $actionUser, User $targetUser)
    {
        event::trigger(
            'Import', 'Chamilo\Core\User',
            ['target_user_id' => $targetUser->getId(), 'action_user_id' => $actionUser->getId()]
        );
    }

    /**
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @return boolean
     */
    public function updateUser(User $user)
    {
        return $this->getUserRepository()->updateUser($user);
    }
}

