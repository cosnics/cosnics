<?php
namespace Chamilo\Core\User\Service;

use Chamilo\Core\Admin\Service\Consulter\ConfigurationConsulter;
use Chamilo\Core\Admin\Storage\DataClass\Setting;
use Chamilo\Core\User\Architecture\EventDispatcher\Event\AfterUserCreateEvent;
use Chamilo\Core\User\Architecture\EventDispatcher\Event\AfterUserPasswordResetEvent;
use Chamilo\Core\User\Architecture\EventDispatcher\Event\AfterUserRegistrationEvent;
use Chamilo\Core\User\Architecture\EventDispatcher\Event\AfterUserUpdateEvent;
use Chamilo\Core\User\Manager;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Core\User\Storage\DataClass\UserSetting;
use Chamilo\Core\User\Storage\Repository\UserRepository;
use Chamilo\Libraries\Architecture\Domain\Application;
use Chamilo\Libraries\Architecture\Exception\UserException;
use Chamilo\Libraries\Filesystem\Service\WebPathBuilder;
use Chamilo\Libraries\Protocol\Authentication\Architecture\Interface\ChangeablePasswordInterface;
use Chamilo\Libraries\Protocol\Authentication\Service\AuthenticationValidator;
use Chamilo\Libraries\Protocol\Mail\Architecture\Domain\Mail;
use Chamilo\Libraries\Protocol\Mail\Architecture\Interface\MailerInterface;
use Chamilo\Libraries\Protocol\Security\Service\HashingUtilities;
use Chamilo\Libraries\Service\Routing\UrlGenerator;
use Chamilo\Libraries\Storage\Architecture\Domain\DataClass;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\OrderBy;
use Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException;
use Chamilo\Libraries\Storage\Architecture\Trait\CacheAdapterHandlerTrait;
use Chamilo\Libraries\Storage\Service\PropertyMapper;
use Doctrine\Common\Collections\ArrayCollection;
use Exception;
use Hackzilla\PasswordGenerator\Generator\PasswordGeneratorInterface;
use InvalidArgumentException;
use RuntimeException;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Core\User\Service
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 */
class UserService
{
    use CacheAdapterHandlerTrait;

    protected MailerInterface $activeMailer;

    protected AuthenticationValidator $authenticationValidator;

    protected ConfigurationConsulter $configurationConsulter;

    protected EventDispatcherInterface $eventDispatcher;

    protected PasswordGeneratorInterface $passwordGenerator;

    protected Translator $translator;

    protected UrlGenerator $urlGenerator;

    protected FilesystemAdapter $userSettingsCacheAdapter;

    protected WebPathBuilder $webPathBuilder;

    private HashingUtilities $hashingUtilities;

    private PropertyMapper $propertyMapper;

    private string $securityKey;

    private UserRepository $userRepository;

    public function __construct(
        UserRepository $userRepository, HashingUtilities $hashingUtilities, PropertyMapper $propertyMapper,
        Translator $translator, FilesystemAdapter $userSettingsCacheAdapter,
        ConfigurationConsulter $configurationConsulter, WebPathBuilder $webPathBuilder, MailerInterface $activeMailer,
        PasswordGeneratorInterface $passwordGenerator, AuthenticationValidator $authenticationValidator,
        UrlGenerator $urlGenerator, EventDispatcherInterface $eventDispatcher, string $securityKey
    )
    {
        $this->userRepository = $userRepository;
        $this->hashingUtilities = $hashingUtilities;
        $this->propertyMapper = $propertyMapper;
        $this->translator = $translator;
        $this->userSettingsCacheAdapter = $userSettingsCacheAdapter;
        $this->configurationConsulter = $configurationConsulter;
        $this->webPathBuilder = $webPathBuilder;
        $this->activeMailer = $activeMailer;
        $this->passwordGenerator = $passwordGenerator;
        $this->authenticationValidator = $authenticationValidator;
        $this->urlGenerator = $urlGenerator;
        $this->eventDispatcher = $eventDispatcher;
        $this->securityKey = $securityKey;
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function countUsers(?Condition $condition = null): int
    {
        return $this->getUserRepository()->countUsers($condition);
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function countUsersForSearchQuery(?string $searchQuery = null): int
    {
        return $this->getUserRepository()->countUsersForSearchQuery($searchQuery);
    }

    /**
     * @param string[] $userIdentifiers
     *
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function countUsersForSearchQueryAndUserIdentifiers(
        ?string $searchQuery = null, array $userIdentifiers = []
    ): int
    {
        return $this->getUserRepository()->countUsersForSearchQueryAndUserIdentifiers($searchQuery, $userIdentifiers);
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function createNewPasswordForUser(User $user): bool
    {
        $configurationConsulter = $this->getConfigurationConsulter();
        $translator = $this->getTranslator();

        $newPassword = $this->getPasswordGenerator()->generatePassword();

        $user->setPassword($this->getHashingUtilities()->hashString($newPassword));

        if (!$this->updateUser($user))
        {
            return false;
        }

        $this->getEventDispatcher()->dispatch(new AfterUserPasswordResetEvent($user));

        try
        {
            $mailSubject = $translator->trans('LoginRequest', [], Manager::CONTEXT);

            $mailBody = [];

            $mailBody[] = '<div style="font-family:arial, sans-serif">';
            $mailBody[] = '<p>' .
                $translator->trans('MailResetPasswordDear', ['USER' => $user->getFullName()], Manager::CONTEXT) .
                '</p>';
            $mailBody[] = '<p>' . $translator->trans('MailResetPasswordDoneBody', [], Manager::CONTEXT) . '</p>';
            $mailBody[] =
                '<p>' . $translator->trans('UserName', [], Manager::CONTEXT) . ': ' . $user->getUsername() . '<br/>';
            $mailBody[] =
                $translator->trans('MailResetPasswordNew', [], Manager::CONTEXT) . ': ' . $newPassword . '</p>';
            $mailBody[] = '<p>' . $translator->trans(
                    'MailResetPasswordLogIn', [
                    'LOGINLINK' => '<a href="' . $this->getWebPathBuilder()->getBasePath() . '">' .
                        $this->getWebPathBuilder()->getBasePath() . '</a>'
                ], Manager::CONTEXT
                ) . '</p>';
            $mailBody[] = '<p>' . $translator->trans('MailResetPasswordCloser', [], Manager::CONTEXT) . '<br/>';
            $mailBody[] = $translator->trans(
                    'MailResetPasswordSender', [
                    'ADMINNAME' => $configurationConsulter->getSetting(
                        ['Chamilo\Core\Admin', 'administrator_name']
                    )
                ], Manager::CONTEXT
                ) . '</p>';
            $mailBody[] = '</div>';

            $this->getActiveMailer()->sendMail(new Mail($mailSubject, implode(PHP_EOL, $mailBody), $user->getEmail()));

            return true;
        }
        catch (Exception)
        {
            return false;
        }
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageLastInsertedIdentifierException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function createUser(User $user): bool
    {
        $user->setRegistrationDate(time());
        $user->setSecurityToken(sha1(time() . uniqid()));

        if (!$this->getUserRepository()->createUser($user))
        {
            return false;
        }

        $this->getEventDispatcher()->dispatch(new AfterUserCreateEvent($user));

        return true;
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageLastInsertedIdentifierException
     */
    public function createUserFromParameters(
        ?string $firstName, ?string $lastName, string $username, ?string $officialCode, string $emailAddress,
        bool $generatePassword, ?string $password, ?string $authSource = 'Chamilo\Libraries\Authentication\Platform',
        bool $isPlatformAdmin = false, int $status = User::STATUS_STUDENT, bool $active = true, bool $sendEmail = false
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

        $user->setGivenName($firstName);
        $user->setSurname($lastName);
        $user->setUsername($username);
        $user->setOfficialCode($officialCode);
        $user->setEmail($emailAddress);
        $user->setAuthenticationSource($authSource);
        $user->setStatus($status);
        $user->setPlatformAdministrator($isPlatformAdmin);
        $user->setActive($active);

        $password = $generatePassword ? $this->getPasswordGenerator()->generatePassword() : $password;
        $user->setPassword($this->getHashingUtilities()->hashString($password));

        if (!$this->createUser($user))
        {
            throw new RuntimeException('Could not create the user');
        }

        if ($sendEmail && !$this->sendRegistrationEmailToUser($user, $password))
        {
            throw new RuntimeException('Could not send an email to the new user');
        }

        return $user;
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageLastInsertedIdentifierException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Symfony\Component\Cache\Exception\CacheException
     */
    public function createUserSetting(UserSetting $userSetting): bool
    {
        if (!$this->getUserRepository()->createUserSetting($userSetting))
        {
            return false;
        }

        if (!$this->clearCacheDataForAdapterAndKeyParts(
            $this->getUserSettingsCacheAdapter(), [User::class, $userSetting->getUserIdentifier()]
        ))
        {
            return false;
        }

        return true;
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageLastInsertedIdentifierException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Symfony\Component\Cache\Exception\CacheException
     */
    public function createUserSettingFromParameters(
        string $settingIdentifier, string $userIdentifier, ?string $value = null
    ): bool
    {
        $userSetting = new UserSetting();

        $userSetting->setSettingIdentifier($settingIdentifier);
        $userSetting->setUserIdentifier($userIdentifier);
        $userSetting->setValue($value);

        return $this->createUserSetting($userSetting);
    }

    public function deleteUser(User $user): bool
    {
        return false;

        // TODO: This needs to be implemented some day
        //if (!$this->canUserBeDeleted($user))
        //{
        //return false;
        //}

        //        $this->getEventDispatcher()->dispatch(new BeforeUserDeleteEvent($user));
        //
        //        if (!$this->getUserRepository()->deleteUser($user))
        //        {
        //            return false;
        //        }
        //
        //        $this->getEventDispatcher()->dispatch(new AfterUserDeleteEvent($user));
        //
        //        return true;
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function deleteUserSettingsForSettingIdentifier(string $settingIdentifier): bool
    {
        return $this->getUserRepository()->deleteUserSettingsForSettingIdentifier($settingIdentifier);
    }

    public function determineUserKey(User $user): string
    {
        return $this->getHashingUtilities()->hashString($this->getSecurityKey() . $user->getEmail());
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\User\Storage\DataClass\User>
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function findActiveStudents(): ArrayCollection
    {
        return $this->findActiveUsersByStatus(User::STATUS_STUDENT);
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\User\Storage\DataClass\User>
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function findActiveTeachers(): ArrayCollection
    {
        return $this->findActiveUsersByStatus(User::STATUS_TEACHER);
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\User\Storage\DataClass\User>
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function findActiveUsers(
        ?Condition $condition = null, ?int $offset = null, ?int $count = null, OrderBy $orderBy = new OrderBy()
    ): ArrayCollection
    {
        return $this->getUserRepository()->findActiveUsers($condition, $offset, $count, $orderBy);
    }

    /**
     * @param int $status
     *
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\User\Storage\DataClass\User>
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function findActiveUsersByStatus(int $status): ArrayCollection
    {
        return $this->getUserRepository()->findActiveUsersByStatus($status);
    }

    /**
     * @param string[] $userIdentifiers
     *
     * @return string[]
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function findEmailAddressesForUserIdentifiers(array $userIdentifiers): array
    {
        return $this->getUserRepository()->findEmailAddressesForUserIdentifiers($userIdentifiers);
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\User\Storage\DataClass\User>
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function findPlatformAdministrators(): ArrayCollection
    {
        return $this->getUserRepository()->findPlatformAdministrators();
    }

    /**
     * @return string[]
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
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

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException
     */
    public function findUserByEmail(string $email): ?User
    {
        return $this->getUserRepository()->findUserByEmail($email);
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function findUserByIdentifier(string $identifier): ?User
    {
        return $this->getUserRepository()->findUserByIdentifier($identifier);
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException
     */
    public function findUserByOfficialCode(string $officialCode): ?User
    {
        return $this->getUserRepository()->findUserByOfficialCode($officialCode);
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException
     */
    public function findUserBySecurityToken(string $securityToken): ?User
    {
        return $this->getUserRepository()->findUserBySecurityToken($securityToken);
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException
     */
    public function findUserByUsername(string $username): ?User
    {
        return $this->getUserRepository()->findUserByUsername($username);
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException
     */
    public function findUserByUsernameOrEmail(string $usernameOrEmail): ?User
    {
        return $this->getUserRepository()->findUserByUsernameOrEmail($usernameOrEmail);
    }

    /**
     * @return string[]
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
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

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function findUserProperties(
        array $retrieveProperties, ?Condition $condition = null, OrderBy $orderBy = new OrderBy()
    ): array
    {
        return $this->getUserRepository()->findUserProperties($retrieveProperties, $condition, $orderBy);
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException
     */
    public function findUserSettingForSettingAndUser(Setting $setting, User $user): ?UserSetting
    {
        return $this->getUserRepository()->findUserSettingForSettingAndUser($setting, $user);
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\User\Storage\DataClass\User>
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function findUsers(
        ?Condition $condition = null, ?int $offset = null, ?int $count = null, OrderBy $orderBy = new OrderBy()
    ): ArrayCollection
    {
        return $this->getUserRepository()->findUsers($condition, $count, $offset, $orderBy);
    }

    /**
     * @param string[] $userIdentifiers
     *
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\User\Storage\DataClass\User>
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function findUsersByIdentifiers(array $userIdentifiers = []): ArrayCollection
    {
        return $this->getUserRepository()->findUsersByIdentifiers($userIdentifiers);
    }

    /**
     * @param string[] $userIdentifiers
     *
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\User\Storage\DataClass\User>
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function findUsersByIdentifiersOrderedByName(array $userIdentifiers): ArrayCollection
    {
        return $this->getUserRepository()->findUsersByIdentifiersOrderedByName($userIdentifiers);
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\User\Storage\DataClass\User>
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function findUsersForSearchQuery(?string $searchQuery = null, ?int $offset = null, ?int $count = null
    ): ArrayCollection
    {
        return $this->getUserRepository()->findUsersForSearchQuery($searchQuery, $offset, $count);
    }

    /**
     * @param string[] $userIdentifiers
     *
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\User\Storage\DataClass\User>
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
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
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\User\Storage\DataClass\User>
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function findUsersMappedByOfficialCode(
        ?Condition $condition = null, ?int $offset = 0, ?int $count = - 1, OrderBy $orderBy = new OrderBy()
    ): ArrayCollection
    {
        return $this->getPropertyMapper()->mapDataClassByProperty(
            $this->findUsers($condition, $offset, $count, $orderBy), User::PROPERTY_OFFICIAL_CODE
        );
    }

    public function getActiveMailer(): MailerInterface
    {
        return $this->activeMailer;
    }

    public function getAuthenticationValidator(): AuthenticationValidator
    {
        return $this->authenticationValidator;
    }

    public function getConfigurationConsulter(): ConfigurationConsulter
    {
        return $this->configurationConsulter;
    }

    public function getEventDispatcher(): EventDispatcherInterface
    {
        return $this->eventDispatcher;
    }

    protected function getHashingUtilities(): HashingUtilities
    {
        return $this->hashingUtilities;
    }

    public function getPasswordGenerator(): PasswordGeneratorInterface
    {
        return $this->passwordGenerator;
    }

    public function getPropertyMapper(): PropertyMapper
    {
        return $this->propertyMapper;
    }

    public function getSecurityKey(): string
    {
        return $this->securityKey;
    }

    public function getTranslator(): Translator
    {
        return $this->translator;
    }

    public function getUrlGenerator(): UrlGenerator
    {
        return $this->urlGenerator;
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException
     */
    public function getUserByOfficialCode(string $officialCode): ?User
    {
        return $this->getUserRepository()->findUserByOfficialCode($officialCode);
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException
     */
    public function getUserBySecurityToken(string $securityToken): ?User
    {
        return $this->getUserRepository()->findUserBySecurityToken($securityToken);
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException
     */
    public function getUserByUsernameOrEmail(string $usernameOrEmail): ?User
    {
        return $this->getUserRepository()->findUserByUsernameOrEmail($usernameOrEmail);
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException
     */
    public function getUserFullNameByIdentifier(string $identifier, ?string $unknownUserTranslation = null): ?string
    {
        $user = $this->findUserByIdentifier($identifier);

        if (!$user instanceof User)
        {
            return $unknownUserTranslation ?: $this->getTranslator()->trans('UserUnknown', [], 'Chamilo\Core\User');
        }

        return $user->getFullName();
    }

    protected function getUserRepository(): UserRepository
    {
        return $this->userRepository;
    }

    public function getUserSettingsCacheAdapter(): FilesystemAdapter
    {
        return $this->userSettingsCacheAdapter;
    }

    public function getWebPathBuilder(): WebPathBuilder
    {
        return $this->webPathBuilder;
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function isUsernameAvailable(string $username): bool
    {
        try
        {
            $this->findUserByUsername($username);

            return false;
        }
        catch (StorageNoResultException)
        {
            return true;
        }
    }

    public function isValidKeyForUser(string $requestKey, User $user): bool
    {
        return $this->determineUserKey($user) == $requestKey;
    }

    /**
     * @throws \Exception
     */
    public function registerUserFromParameters(
        ?string $firstName, ?string $lastName, string $username, ?string $officialCode, string $emailAddress,
        bool $generatePassword, ?string $password = null, ?string $authSource = 'Platform',
        ?int $status = User::STATUS_STUDENT, bool $sendEmail = false
    ): User
    {
        $configurationConsulter = $this->getConfigurationConsulter();

        if ($configurationConsulter->getSetting([Manager::CONTEXT, 'allow_registration']) == 0)
        {
            $active = false;
        }
        else
        {
            $active = true;
        }

        $user = $this->createUserFromParameters(
            $firstName, $lastName, $username, $officialCode, $emailAddress, $generatePassword, $password, $authSource,
            false, $status, $active, $sendEmail
        );

        $this->getEventDispatcher()->dispatch(new AfterUserRegistrationEvent($user));

        return $user;
    }

    /**
     * @throws \Chamilo\Libraries\Architecture\Exception\UserException
     */
    public function sendPasswordResetLinkforUser(User $user): bool
    {
        $translator = $this->getTranslator();

        if (!$user->getActive())
        {
            throw new UserException(
                $translator->trans(
                    'ResetPasswordNotPossibleForInactiveUser',
                    ['USER' => $user->getFullName() . ' (' . $user->getUsername() . ')'], Manager::CONTEXT
                )
            );
        }

        $authentication =
            $this->getAuthenticationValidator()->getAuthenticationByType($user->getAuthenticationSource());

        if (!$authentication instanceof ChangeablePasswordInterface)
        {
            throw new UserException(
                $translator->trans(
                    'ResetPasswordNotPossibleForThisUser',
                    ['USER' => $user->getFullName() . ' (' . $user->getUsername() . ')'], Manager::CONTEXT
                )
            );
        }

        try
        {
            $configurationConsulter = $this->getConfigurationConsulter();

            $resetLink = $this->getUrlGenerator()->fromParameters(
                [
                    Application::PARAM_CONTEXT => Manager::CONTEXT,
                    Application::PARAM_ACTION => Manager::ACTION_RESET_PASSWORD,
                    Manager::PARAM_RESET_KEY => $this->determineUserKey($user),
                    DataClass::PROPERTY_ID => $user->getId()
                ]
            );

            $mailSubject = $translator->trans('LoginRequest', [], Manager::CONTEXT);

            $mailBody = [];
            $mailBody[] = '<div style="font-family:arial, sans-serif">';
            $mailBody[] = '<p>' .
                $translator->trans('MailResetPasswordDear', ['USER' => $user->getFullName()], Manager::CONTEXT) .
                '</p>';
            $mailBody[] = '<p>' . $translator->trans('MailResetPasswordAskBody', [], Manager::CONTEXT) . '</p>';
            $mailBody[] =
                '<p>' . $translator->trans('UserName', [], Manager::CONTEXT) . ': ' . $user->getUsername() . '<br/>';
            $mailBody[] =
                $translator->trans('MailResetPasswordLink', [], Manager::CONTEXT) . ': <a href="' . $resetLink . '">' .
                $resetLink . '</a></p>';
            $mailBody[] = '<p>' . $translator->trans('MailResetPasswordCloser', [], Manager::CONTEXT) . '<br/>';
            $mailBody[] = $translator->trans(
                    'MailResetPasswordSender', [
                    'ADMINNAME' => $configurationConsulter->getSetting(
                        ['Chamilo\Core\Admin', 'administrator_name']
                    )
                ], Manager::CONTEXT
                ) . '</p>';
            $mailBody[] = '</div>';

            $this->getActiveMailer()->sendMail(
                new Mail($mailSubject, implode(PHP_EOL, $mailBody), $user->getEmail())
            );

            return true;
        }
        catch (Exception)
        {
            throw new UserException(
                $translator->trans(
                    'SendingPasswordResetLinkNotPossibleForThisUser',
                    ['USER' => $user->getFullName() . ' (' . $user->getUsername() . ')'], Manager::CONTEXT
                )
            );
        }
    }

    public function sendRegistrationEmailToUser(User $user, string $password): bool
    {
        $configurationConsulter = $this->getConfigurationConsulter();

        $options = [];
        $options['firstname'] = $user->getGivenName();
        $options['lastname'] = $user->getSurname();
        $options['username'] = $user->getUsername();
        $options['password'] = $password;
        $options['site_name'] = $configurationConsulter->getSetting(['Chamilo\Core\Admin', 'site_name']);
        $options['site_url'] = $this->getWebPathBuilder()->getBasePath();
        $options['admin_name'] = $configurationConsulter->getSetting(
            ['Chamilo\Core\Admin', 'administrator_name']
        );
        $options['admin_email'] = $configurationConsulter->getSetting(
            ['Chamilo\Core\Admin', 'administrator_email']
        );

        $subject =
            $this->getTranslator()->trans('YourRegistrationOn', [], Manager::CONTEXT) . ' ' . $options['site_name'];

        $body = $configurationConsulter->getSetting([Manager::CONTEXT, 'email_template']);
        foreach ($options as $option => $value)
        {
            $body = str_replace('[' . $option . ']', $value, $body);
        }

        $mail = new Mail(
            $subject, $body, $user->getEmail(), true, [], [], $options['admin_name'], $options['admin_email']
        );

        try
        {
            $this->getActiveMailer()->sendMail($mail);
        }
        catch (Exception)
        {
            return false;
        }

        return true;
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function updateAccountFromParameters(
        User $user, ?string $firstName, ?string $lastName, string $username, ?string $officialCode,
        string $emailAddress, ?string $currentPassword, ?string $newPassword
    ): bool
    {
        $authentication = $this->authenticationValidator->getAuthenticationByType($user->getAuthenticationSource());

        $user->setGivenName($firstName);
        $user->setSurname($lastName);
        $user->setOfficialCode($officialCode);
        $user->setEmail($emailAddress);

        if ($user->getUsername() != $username && !$this->isUsernameAvailable($username))
        {
            throw new RuntimeException('The given username is already taken');
        }

        $user->setUsername($username);

        if (strlen($currentPassword) && $authentication instanceof ChangeablePasswordInterface)
        {
            if (!$authentication->changePassword($user, $currentPassword, $newPassword))
            {
                return false;
            }
        }

        return $this->updateUser($user);
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function updateUser(User $user): bool
    {
        if (!$this->getUserRepository()->updateUser($user))
        {
            return false;
        }

        $this->getEventDispatcher()->dispatch(new AfterUserUpdateEvent($user));

        return true;
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function updateUserFromParameters(
        User $user, ?string $firstName, ?string $lastName, ?string $username, ?string $officialCode,
        ?string $emailAddress, bool $generatePassword, ?string $password, ?bool $isPlatformAdmin, ?int $status,
        ?bool $active, bool $sendEmail = false
    ): bool
    {
        if (!is_null($firstName))
        {
            $user->setGivenName($firstName);
        }

        if (!is_null($lastName))
        {
            $user->setSurname($lastName);
        }

        if (!is_null($officialCode))
        {
            $user->setOfficialCode($officialCode);
        }

        if (!is_null($emailAddress))
        {
            $user->setEmail($emailAddress);
        }

        if (!is_null($username) && $user->getUsername() != $username && $this->isUsernameAvailable($username))
        {
            $user->setUsername($username);
        }

        if (!is_null($status))
        {
            $user->setStatus($status);
        }

        if (!is_null($isPlatformAdmin))
        {
            $user->setPlatformAdministrator($isPlatformAdmin);
        }

        if (!is_null($active))
        {
            $user->setActive($active);
        }

        $password = $generatePassword ? $this->getPasswordGenerator()->generatePassword() : $password;

        if (!is_null($password))
        {
            $user->setPassword($this->getHashingUtilities()->hashString($password));
        }

        if (!$this->updateUser($user))
        {
            throw new RuntimeException('Could not update the user');
        }

        if ($sendEmail && !$this->sendRegistrationEmailToUser($user, $password))
        {
            throw new RuntimeException('Could not send an email to the updated user');
        }

        return $this->updateUser($user);
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function updateUserSetting(UserSetting $userSetting): bool
    {
        return $this->getUserRepository()->updateUserSetting($userSetting);
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function updateUserSettingValue(UserSetting $userSetting, ?string $value = null): bool
    {
        $userSetting->setValue($value);

        return $this->updateUserSetting($userSetting);
    }

}

