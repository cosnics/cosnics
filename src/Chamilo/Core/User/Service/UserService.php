<?php
namespace Chamilo\Core\User\Service;

use Chamilo\Configuration\Storage\DataClass\Setting;
use Chamilo\Core\Tracking\Storage\DataClass\Event;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Core\User\Storage\DataClass\UserSetting;
use Chamilo\Core\User\Storage\Repository\UserRepository;
use Chamilo\Libraries\Cache\Traits\CacheAdapterHandlerTrait;
use Chamilo\Libraries\Hashing\HashingUtilities;
use Chamilo\Libraries\Storage\DataClass\PropertyMapper;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Doctrine\Common\Collections\ArrayCollection;
use InvalidArgumentException;
use RuntimeException;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Core\User\Service
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 */
class UserService
{
    use CacheAdapterHandlerTrait;

    protected Translator $translator;

    protected FilesystemAdapter $userSettingsCacheAdapter;

    private HashingUtilities $hashingUtilities;

    private PropertyMapper $propertyMapper;

    private UserRepository $userRepository;

    public function __construct(
        UserRepository $userRepository, HashingUtilities $hashingUtilities, PropertyMapper $propertyMapper,
        Translator $translator, FilesystemAdapter $userSettingsCacheAdapter
    )
    {
        $this->userRepository = $userRepository;
        $this->hashingUtilities = $hashingUtilities;
        $this->propertyMapper = $propertyMapper;
        $this->translator = $translator;
        $this->userSettingsCacheAdapter = $userSettingsCacheAdapter;
    }

    public function countUsers(?Condition $condition = null): int
    {
        return $this->getUserRepository()->countUsers($condition);
    }

    public function countUsersForSearchQuery(?string $searchQuery = null): int
    {
        return $this->getUserRepository()->countUsersForSearchQuery($searchQuery);
    }

    /**
     * @param string[] $userIdentifiers
     */
    public function countUsersForSearchQueryAndUserIdentifiers(
        ?string $searchQuery = null, array $userIdentifiers = []
    ): int
    {
        return $this->getUserRepository()->countUsersForSearchQueryAndUserIdentifiers($searchQuery, $userIdentifiers);
    }

    public function countUsersWaitingForApproval(?Condition $condition = null): int
    {
        $conditions = [];

        if ($condition instanceof Condition)
        {
            $conditions[] = $condition;
        }

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(User::class, User::PROPERTY_APPROVED), new StaticConditionVariable(0)
        );

        $condition = new AndCondition($conditions);

        return $this->countUsers($condition);
    }

    /**
     * @throws \Exception
     */
    public function createUser(User $user): bool
    {
        $user->set_registration_date(time());
        $user->set_security_token(sha1(time() . uniqid()));

        return $this->getUserRepository()->createUser($user);
    }

    /**
     * @throws \Exception
     */
    public function createUserFromParameters(
        ?string $firstName, ?string $lastName, string $username, ?string $officialCode, string $emailAddress,
        string $password, ?string $authSource = 'Platform', ?int $status = User::STATUS_STUDENT
    ): User
    {
        $requiredParameters = [
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
     * @throws \Symfony\Component\Cache\Exception\CacheException
     */
    public function createUserSetting(UserSetting $userSetting): bool
    {
        if (!$this->getUserRepository()->createUserSetting($userSetting))
        {
            return false;
        }

        if (!$this->clearCacheDataForAdapterAndKeyParts(
            $this->getUserSettingsCacheAdapter(), [User::class, $userSetting->get_user_id()]
        ))
        {
            return false;
        }

        return true;
    }

    /**
     * @throws \Symfony\Component\Cache\Exception\CacheException
     */
    public function createUserSettingFromParameters(
        string $settingIdentifier, string $userIdentifier, ?string $value = null
    ): bool
    {
        $userSetting = new UserSetting();

        $userSetting->set_setting_id($settingIdentifier);
        $userSetting->set_user_id($userIdentifier);
        $userSetting->set_value($value);

        return $this->createUserSetting($userSetting);
    }

    public function deleteUserSettingsForSettingIdentifier(string $settingIdentifier): bool
    {
        return $this->getUserRepository()->deleteUserSettingsForSettingIdentifier($settingIdentifier);
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\User\Storage\DataClass\User>
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function findActiveStudents(): ArrayCollection
    {
        return $this->findActiveUsersByStatus(User::STATUS_STUDENT);
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\User\Storage\DataClass\User>
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function findActiveTeachers(): ArrayCollection
    {
        return $this->findActiveUsersByStatus(User::STATUS_TEACHER);
    }

    /**
     * @param int $status
     *
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\User\Storage\DataClass\User>
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function findActiveUsersByStatus(int $status): ArrayCollection
    {
        return $this->getUserRepository()->findActiveUsersByStatus($status);
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\User\Storage\DataClass\User>
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function findPlatformAdministrators(): ArrayCollection
    {
        return $this->getUserRepository()->findPlatformAdministrators();
    }

    /**
     * @return string[]
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function findSettingsForUser(User $user): array
    {
        $userSettings = $this->getUserRepository()->findSettingsForUser($user);

        $mappedUserSettings = [];

        foreach ($userSettings as $userSetting)
        {
            $mappedUserSettings[$userSetting[Setting::PROPERTY_CONTEXT]][$userSetting[Setting::PROPERTY_VARIABLE]] =
                $userSetting[UserSetting::PROPERTY_VALUE];
        }

        return $mappedUserSettings;
    }

    public function findUserByEmail(string $email): ?User
    {
        return $this->getUserRepository()->findUserByEmail($email);
    }

    public function findUserByIdentifier(string $identifier): ?User
    {
        return $this->getUserRepository()->findUserByIdentifier($identifier);
    }

    public function findUserByOfficialCode(string $officialCode): ?User
    {
        return $this->getUserRepository()->findUserByOfficialCode($officialCode);
    }

    public function findUserBySecurityToken(string $securityToken): ?User
    {
        return $this->getUserRepository()->findUserBySecurityToken($securityToken);
    }

    public function findUserByUsername(string $username): ?User
    {
        return $this->getUserRepository()->findUserByUsername($username);
    }

    public function findUserByUsernameOrEmail(string $usernameOrEmail): ?User
    {
        return $this->getUserRepository()->findUserByUsernameOrEmail($usernameOrEmail);
    }

    /**
     * @return string[]
     */
    public function findUserIdentifiers(): array
    {
        return $this->getUserRepository()->findUserIdentifiers();
    }

    /**
     * @param string[] $officialCodes
     *
     * @return string[]
     * @throws \Exception
     */
    public function findUserIdentifiersByOfficialCodes(array $officialCodes): array
    {
        return $this->getUserRepository()->findUserIdentifiersByOfficialCodes($officialCodes);
    }

    public function findUserSettingForSettingAndUser(Setting $setting, User $user): ?UserSetting
    {
        return $this->getUserRepository()->findUserSettingForSettingAndUser($setting, $user);
    }

    /**
     * @param ?\Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     * @param ?int $offset
     * @param ?int $count
     * @param ?\Chamilo\Libraries\Storage\Query\OrderBy $orderProperty
     *
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\User\Storage\DataClass\User>
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function findUsers(
        ?Condition $condition = null, ?int $offset = null, ?int $count = null, ?OrderBy $orderProperty = null
    ): ArrayCollection
    {
        return $this->getUserRepository()->findUsers($condition, $count, $offset, $orderProperty);
    }

    /**
     * @param string[] $userIdentifiers
     *
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\User\Storage\DataClass\User>
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function findUsersByIdentifiers(array $userIdentifiers = []): ArrayCollection
    {
        return $this->getUserRepository()->findUsersByIdentifiersOrderedByName($userIdentifiers);
    }

    /**
     * @param string[] $userIdentifiers
     *
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\User\Storage\DataClass\User>
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function findUsersByIdentifiersOrderedByName(array $userIdentifiers): ArrayCollection
    {
        return $this->getUserRepository()->findUsersByIdentifiersOrderedByName($userIdentifiers);
    }

    /**
     * @param ?string $searchQuery
     * @param ?int $offset
     * @param ?int $count
     *
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\User\Storage\DataClass\User>
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function findUsersForSearchQuery(?string $searchQuery = null, ?int $offset = null, ?int $count = null
    ): ArrayCollection
    {
        return $this->getUserRepository()->findUsersForSearchQuery($searchQuery, $offset, $count);
    }

    /**
     * @param ?string $searchQuery
     * @param string[] $userIdentifiers
     * @param ?int $offset
     * @param ?int $count
     *
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\User\Storage\DataClass\User>
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function findUsersForSearchQueryAndUserIdentifiers(
        ?string $searchQuery = null, array $userIdentifiers = [], ?int $offset = null, ?int $count = null
    ): ArrayCollection
    {
        return $this->getUserRepository()->findUsersForSearchQueryAndUserIdentifiers(
            $searchQuery, $userIdentifiers, $offset, $count
        );
    }

    /**
     * @return \Chamilo\Core\User\Storage\DataClass\User[]
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function findUsersMappedByOfficialCode(
        ?Condition $condition = null, ?int $offset = 0, ?int $count = - 1, ?OrderBy $orderProperty = null
    ): array
    {
        return $this->getPropertyMapper()->mapDataClassByProperty(
            $this->findUsers($condition, $offset, $count, $orderProperty), User::PROPERTY_OFFICIAL_CODE
        );
    }

    /**
     * @param ?\Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     * @param ?int $offset
     * @param ?int $count
     * @param ?\Chamilo\Libraries\Storage\Query\OrderBy $orderProperty
     *
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\User\Storage\DataClass\User>
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function findUsersWaitingForApproval(
        ?Condition $condition = null, ?int $offset = null, ?int $count = null, ?OrderBy $orderProperty = null
    ): ArrayCollection
    {
        $conditions = [];

        if ($condition instanceof Condition)
        {
            $conditions[] = $condition;
        }

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(User::class, User::PROPERTY_APPROVED), new StaticConditionVariable(0)
        );
        $condition = new AndCondition($conditions);

        return $this->findUsers($condition, $count, $offset, $orderProperty);
    }

    protected function getHashingUtilities(): HashingUtilities
    {
        return $this->hashingUtilities;
    }

    public function getPropertyMapper(): PropertyMapper
    {
        return $this->propertyMapper;
    }

    public function getTranslator(): Translator
    {
        return $this->translator;
    }

    public function getUserByOfficialCode(string $officialCode): ?User
    {
        return $this->getUserRepository()->findUserByOfficialCode($officialCode);
    }

    public function getUserBySecurityToken(string $securityToken): ?User
    {
        return $this->getUserRepository()->findUserBySecurityToken($securityToken);
    }

    public function getUserByUsernameOrEmail(string $usernameOrEmail): ?User
    {
        return $this->getUserRepository()->findUserByUsernameOrEmail($usernameOrEmail);
    }

    public function getUserFullNameByIdentifier(string $identifier): ?string
    {
        $user = $this->findUserByIdentifier($identifier);

        if (!$user instanceof User)
        {
            return $this->getTranslator()->trans('UserUnknown', [], 'Chamilo\Core\User');
        }

        return $user->get_fullname();
    }

    protected function getUserRepository(): UserRepository
    {
        return $this->userRepository;
    }

    public function getUserSettingsCacheAdapter(): FilesystemAdapter
    {
        return $this->userSettingsCacheAdapter;
    }

    public function isUsernameAvailable(string $username): bool
    {
        return !$this->findUserByUsername($username) instanceof User;
    }

    public function triggerImportEvent(User $actionUser, User $targetUser)
    {
        Event::trigger(
            'Import', 'Chamilo\Core\User',
            ['target_user_id' => $targetUser->getId(), 'action_user_id' => $actionUser->getId()]
        );
    }

    public function updateUser(User $user): bool
    {
        return $this->getUserRepository()->updateUser($user);
    }

    public function updateUserSetting(UserSetting $userSetting): bool
    {
        return $this->getUserRepository()->updateUserSetting($userSetting);
    }

    public function updateUserSettingValue(UserSetting $userSetting, ?string $value = null): bool
    {
        $userSetting->set_value($value);

        return $this->updateUserSetting($userSetting);
    }
}

