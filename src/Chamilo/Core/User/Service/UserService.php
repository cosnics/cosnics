<?php
namespace Chamilo\Core\User\Service;

use Chamilo\Core\User\Architecture\Enum\ActionEnum;
use Chamilo\Core\User\Architecture\EventDispatcher\Event\AfterUserCreateEvent;
use Chamilo\Core\User\Architecture\EventDispatcher\Event\AfterUserDeleteEvent;
use Chamilo\Core\User\Architecture\EventDispatcher\Event\AfterUserPasswordResetEvent;
use Chamilo\Core\User\Architecture\EventDispatcher\Event\AfterUserRegistrationEvent;
use Chamilo\Core\User\Architecture\EventDispatcher\Event\AfterUserUpdateEvent;
use Chamilo\Core\User\Architecture\EventDispatcher\Event\BeforeUserDeleteEvent;
use Chamilo\Core\User\Manager;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Core\User\Storage\Repository\UserRepository;
use Chamilo\Libraries\Architecture\Interface\ApplicationInterface;
use Chamilo\Libraries\Filesystem\Service\WebPathBuilder;
use Chamilo\Libraries\Protocol\Authentication\Architecture\Interface\ChangeablePasswordInterface;
use Chamilo\Libraries\Protocol\Authentication\Service\AuthenticationValidator;
use Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\UserException;
use Chamilo\Libraries\Protocol\Mail\Architecture\Domain\Mail;
use Chamilo\Libraries\Protocol\Mail\Architecture\Interface\MailerInterface;
use Chamilo\Libraries\Protocol\Security\Service\HashingAlgorithm;
use Chamilo\Libraries\Service\Routing\UrlGenerator;
use Chamilo\Libraries\Storage\Architecture\Domain\DataClass;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\OrderBy;
use Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException;
use Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException;
use Chamilo\Libraries\Storage\Architecture\Interface\ConditionInterface;
use Chamilo\Libraries\Storage\Service\PropertyMapper;
use Doctrine\Common\Collections\ArrayCollection;
use Exception;
use Hackzilla\PasswordGenerator\Generator\PasswordGeneratorInterface;
use InvalidArgumentException;
use RuntimeException;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Core\User\Service
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 */
readonly class UserService
{
    public function __construct(
        private UserRepository $userRepository, private HashingAlgorithm $hashingUtilities,
        private PropertyMapper $propertyMapper, protected Translator $translator,
        protected WebPathBuilder $webPathBuilder, protected MailerInterface $activeMailer,
        protected PasswordGeneratorInterface $passwordGenerator,
        protected AuthenticationValidator $authenticationValidator, protected UrlGenerator $urlGenerator,
        protected EventDispatcherInterface $eventDispatcher, private string $securityKey, protected string $siteName,
        protected string $administratorName, protected string $administratorEmail, protected bool $allowRegistration
    )
    {
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function countUsers(?ConditionInterface $condition = null): int
    {
        return $this->userRepository->countUsers($condition);
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function countUsersForSearchQuery(?string $searchQuery = null): int
    {
        return $this->userRepository->countUsersForSearchQuery($searchQuery);
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
        return $this->userRepository->countUsersForSearchQueryAndUserIdentifiers($searchQuery, $userIdentifiers);
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function createNewPasswordForUser(User $user, ?User $executingUser = null): bool
    {
        $translator = $this->translator;

        $newPassword = $this->passwordGenerator->generatePassword();

        $user->setPassword($this->hashingUtilities->hashString($newPassword));

        if (!$this->updateUser($user)) {
            return false;
        }

        $this->eventDispatcher->dispatch(new AfterUserPasswordResetEvent($user, $executingUser));

        try {
            $mailSubject = $translator->trans('LoginRequest', [], Manager::CONTEXT);

            $mailBody = [];

            $mailBody[] = '<div style="font-family:arial, sans-serif">';
            $mailBody[] = '<p>' .
                $translator->trans('MailResetPasswordDear', ['%User%' => $user->getFullName()], Manager::CONTEXT) .
                '</p>';
            $mailBody[] = '<p>' . $translator->trans('MailResetPasswordDoneBody', [], Manager::CONTEXT) . '</p>';
            $mailBody[] =
                '<p>' . $translator->trans('Username', [], Manager::CONTEXT) . ': ' . $user->getUsername() . '<br/>';
            $mailBody[] =
                $translator->trans('MailResetPasswordNew', [], Manager::CONTEXT) . ': ' . $newPassword . '</p>';
            $mailBody[] = '<p>' . $translator->trans(
                    'MailResetPasswordLogIn', [
                    '%LoginLink%' => '<a href="' . $this->webPathBuilder->getBasePath() . '">' .
                        $this->webPathBuilder->getBasePath() . '</a>'
                ], Manager::CONTEXT
                ) . '</p>';
            $mailBody[] = '<p>' . $translator->trans('MailResetPasswordCloser', [], Manager::CONTEXT) . '<br/>';
            $mailBody[] = $translator->trans(
                    'MailResetPasswordSender', [
                    '%AdminName%' => $this->administratorName
                ], Manager::CONTEXT
                ) . '</p>';
            $mailBody[] = '</div>';

            $this->activeMailer->sendMail(new Mail($mailSubject, implode(PHP_EOL, $mailBody), [$user->getEmail()]));

            return true;
        }
        catch (Exception) {
            return false;
        }
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageLastInsertedIdentifierException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function createUser(User $user, ?User $executingUser = null): bool
    {
        $user->setRegistrationDate(time());
        $user->setSecurityToken(sha1(time() . uniqid()));

        if (!$this->userRepository->createUser($user)) {
            return false;
        }

        $this->eventDispatcher->dispatch(new AfterUserCreateEvent($user, $executingUser));

        return true;
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageLastInsertedIdentifierException
     */
    public function createUserFromParameters(
        ?string $firstName, ?string $lastName, string $username, ?string $officialCode, string $emailAddress,
        bool $generatePassword, ?string $password,
        ?string $authSource = 'Chamilo\Libraries\Protocol\Authentication\Service\PlatformAuthentication',
        bool $isPlatformAdmin = false, bool $active = true, bool $sendEmail = false, ?User $executingUser = null
    ): User
    {
        $requiredParameters = [
            'username' => $username,
            'officialCode' => $officialCode,
            'emailAddress' => $emailAddress,
            'password' => $password
        ];

        foreach ($requiredParameters as $parameterName => $parameterValue) {
            if (empty($parameterValue)) {
                throw new InvalidArgumentException('The ' . $parameterName . ' can not be empty');
            }
        }

        if (!$this->isUsernameAvailable($username)) {
            throw new RuntimeException('The given username is already taken');
        }

        $user = new User();

        $user->setGivenName($firstName);
        $user->setSurname($lastName);
        $user->setUsername($username);
        $user->setOfficialCode($officialCode);
        $user->setEmail($emailAddress);
        $user->setAuthenticationSource($authSource);
        $user->setPlatformAdministrator($isPlatformAdmin);
        $user->setActive($active);

        $password = $generatePassword ? $this->passwordGenerator->generatePassword() : $password;
        $user->setPassword($this->hashingUtilities->hashString($password));

        if (!$this->createUser($user, $executingUser)) {
            throw new RuntimeException('Could not create the user');
        }

        if ($sendEmail && !$this->sendRegistrationEmailToUser($user, $password)) {
            throw new RuntimeException('Could not send an email to the new user');
        }

        return $user;
    }

    public function deleteUser(User $user, ?User $executingUser = null): bool
    {
        // TODO: This needs to be implemented some day
        //        if (!$this->canUserBeDeleted($user))
        //        {
        //        return false;
        //        }

        $this->eventDispatcher->dispatch(new BeforeUserDeleteEvent($user));
        //
        //        if (!$this->userRepository->deleteUser($user))
        //        {
        //            return false;
        //        }
        //
        $this->eventDispatcher->dispatch(new AfterUserDeleteEvent($user, $executingUser));

        //
        return false;
    }

    /** @noinspection PhpUnusedParameterInspection */
    public function determineUserKey(User $user): string
    {
        return $this->hashingUtilities->hashString($this->securityKey . $user->getEmail());
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\User\Storage\DataClass\User>
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function findActiveUsers(
        ?ConditionInterface $condition = null, ?int $offset = null, ?int $count = null, OrderBy $orderBy = new OrderBy()
    ): ArrayCollection
    {
        return $this->userRepository->findActiveUsers($condition, $offset, $count, $orderBy);
    }

    /**
     * @param string[] $userIdentifiers
     *
     * @return string[]
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function findEmailAddressesForUserIdentifiers(array $userIdentifiers): array
    {
        return $this->userRepository->findEmailAddressesForUserIdentifiers($userIdentifiers);
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\User\Storage\DataClass\User>
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function findPlatformAdministrators(): ArrayCollection
    {
        return $this->userRepository->findPlatformAdministrators();
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException
     */
    public function findUserByEmail(string $email): ?User
    {
        return $this->userRepository->findUserByEmail($email);
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function findUserByIdentifier(string $identifier): ?User
    {
        return $this->userRepository->findUserByIdentifier($identifier);
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException
     */
    public function findUserByOfficialCode(string $officialCode): ?User
    {
        return $this->userRepository->findUserByOfficialCode($officialCode);
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException
     */
    public function findUserBySecurityToken(string $securityToken): ?User
    {
        return $this->userRepository->findUserBySecurityToken($securityToken);
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException
     */
    public function findUserByUsername(string $username): ?User
    {
        return $this->userRepository->findUserByUsername($username);
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException
     */
    public function findUserByUsernameOrEmail(string $usernameOrEmail): ?User
    {
        return $this->userRepository->findUserByUsernameOrEmail($usernameOrEmail);
    }

    /**
     * @return string[]
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function findUserIdentifiers(): array
    {
        return $this->userRepository->findUserIdentifiers();
    }

    /**
     * @param string[] $officialCodes
     *
     * @return string[]
     * @throws \Exception
     */
    public function findUserIdentifiersByOfficialCodes(array $officialCodes): array
    {
        return $this->userRepository->findUserIdentifiersByOfficialCodes($officialCodes);
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function findUserProperties(
        array $retrieveProperties, ?ConditionInterface $condition = null, OrderBy $orderBy = new OrderBy()
    ): array
    {
        return $this->userRepository->findUserProperties($retrieveProperties, $condition, $orderBy);
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\User\Storage\DataClass\User>
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function findUsers(
        ?ConditionInterface $condition = null, ?int $offset = null, ?int $count = null, OrderBy $orderBy = new OrderBy()
    ): ArrayCollection
    {
        return $this->userRepository->findUsers($condition, $count, $offset, $orderBy);
    }

    /**
     * @param string[] $userIdentifiers
     *
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\User\Storage\DataClass\User>
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function findUsersByIdentifiers(array $userIdentifiers = []): ArrayCollection
    {
        return $this->userRepository->findUsersByIdentifiers($userIdentifiers);
    }

    /**
     * @param string[] $userIdentifiers
     *
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\User\Storage\DataClass\User>
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function findUsersByIdentifiersOrderedByName(array $userIdentifiers): ArrayCollection
    {
        return $this->userRepository->findUsersByIdentifiersOrderedByName($userIdentifiers);
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\User\Storage\DataClass\User>
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function findUsersForSearchQuery(?string $searchQuery = null, ?int $offset = null, ?int $count = null
    ): ArrayCollection
    {
        return $this->userRepository->findUsersForSearchQuery($searchQuery, $offset, $count);
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
        return $this->userRepository->findUsersForSearchQueryAndUserIdentifiers(
            $searchQuery, $userIdentifiers, $offset, $count
        );
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\User\Storage\DataClass\User>
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function findUsersMappedByOfficialCode(
        ?ConditionInterface $condition = null, ?int $offset = 0, ?int $count = - 1, OrderBy $orderBy = new OrderBy()
    ): ArrayCollection
    {
        return $this->propertyMapper->mapDataClassByProperty(
            $this->findUsers($condition, $offset, $count, $orderBy), User::PROPERTY_OFFICIAL_CODE
        );
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException
     */
    public function getUserByOfficialCode(string $officialCode): ?User
    {
        return $this->userRepository->findUserByOfficialCode($officialCode);
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException
     */
    public function getUserBySecurityToken(string $securityToken): ?User
    {
        return $this->userRepository->findUserBySecurityToken($securityToken);
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException
     */
    public function getUserByUsernameOrEmail(string $usernameOrEmail): ?User
    {
        return $this->userRepository->findUserByUsernameOrEmail($usernameOrEmail);
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException
     */
    public function getUserFullNameByIdentifier(string $identifier, ?string $unknownUserTranslation = null): ?string
    {
        $user = $this->findUserByIdentifier($identifier);

        if (!$user instanceof User) {
            return $unknownUserTranslation ?: $this->translator->trans('UserUnknown', [], Manager::CONTEXT);
        }

        return $user->getFullName();
    }

    public function isUsernameAvailable(string $username): bool
    {
        try {
            $this->findUserByUsername($username);

            return false;
        }
        catch (StorageNoResultException) {
            return true;
        }
        catch (StorageMethodException) {
            return false;
        }
    }

    public function isUsernameAvailableForUser(User $user, string $username): bool
    {
        if ($user->getUsername() == $username) {
            return true;
        }

        return $this->isUsernameAvailable($username);
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
        bool $generatePassword, ?string $password = null, ?string $authSource = 'Platform', bool $sendEmail = false,
        ?User $executingUser = null
    ): User
    {
        $user = $this->createUserFromParameters(
            $firstName, $lastName, $username, $officialCode, $emailAddress, $generatePassword, $password, $authSource,
            false, $this->allowRegistration, $sendEmail, $executingUser
        );

        $this->eventDispatcher->dispatch(new AfterUserRegistrationEvent($user));

        return $user;
    }

    /**
     * @throws \Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\UserException
     * @throws \Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchClassException
     */
    public function sendPasswordResetLinkforUser(User $user): bool
    {
        if (!$user->getActive()) {
            throw new UserException(
                $this->translator->trans(
                    'ResetPasswordNotPossibleForInactiveUser',
                    ['%User%' => $user->getFullName() . ' (' . $user->getUsername() . ')'], Manager::CONTEXT
                )
            );
        }

        $authentication = $this->authenticationValidator->getAuthenticationByType($user->getAuthenticationSource());

        if (!$authentication instanceof ChangeablePasswordInterface) {
            throw new UserException(
                $this->translator->trans(
                    'ResetPasswordNotPossibleForThisUser',
                    ['%User%' => $user->getFullName() . ' (' . $user->getUsername() . ')'], Manager::CONTEXT
                )
            );
        }

        try {
            $resetLink = $this->urlGenerator->fromParameters(
                [
                    ApplicationInterface::PARAM_CONTEXT => Manager::CONTEXT,
                    ApplicationInterface::PARAM_ACTION => ActionEnum::RESET_PASSWORD->value,
                    Manager::PARAM_RESET_KEY => $this->determineUserKey($user),
                    DataClass::PROPERTY_ID => $user->getId()
                ]
            );

            $mailSubject = $this->translator->trans('LoginRequest', [], Manager::CONTEXT);

            $mailBody = [];
            $mailBody[] = '<div style="font-family:arial, sans-serif">';
            $mailBody[] = '<p>' .
                $this->translator->trans('MailResetPasswordDear', ['%User%' => $user->getFullName()], Manager::CONTEXT
                ) . '</p>';
            $mailBody[] = '<p>' . $this->translator->trans('MailResetPasswordAskBody', [], Manager::CONTEXT) . '</p>';
            $mailBody[] =
                '<p>' . $this->translator->trans('Username', [], Manager::CONTEXT) . ': ' . $user->getUsername() .
                '<br/>';
            $mailBody[] =
                $this->translator->trans('MailResetPasswordLink', [], Manager::CONTEXT) . ': <a href="' . $resetLink .
                '">' . $resetLink . '</a></p>';
            $mailBody[] = '<p>' . $this->translator->trans('MailResetPasswordCloser', [], Manager::CONTEXT) . '<br/>';
            $mailBody[] = $this->translator->trans(
                    'MailResetPasswordSender', [
                    '%AdminName%' => $this->administratorName
                ], Manager::CONTEXT
                ) . '</p>';
            $mailBody[] = '</div>';

            $this->activeMailer->sendMail(
                new Mail($mailSubject, implode(PHP_EOL, $mailBody), [$user->getEmail()])
            );

            return true;
        }
        catch (Exception) {
            throw new UserException(
                $this->translator->trans(
                    'SendingPasswordResetLinkNotPossibleForThisUser',
                    ['%User%' => $user->getFullName() . ' (' . $user->getUsername() . ')'], Manager::CONTEXT
                )
            );
        }
    }

    public function sendRegistrationEmailToUser(User $user, string $password): bool
    {
        $options = [];
        $options['firstname'] = $user->getGivenName();
        $options['lastname'] = $user->getSurname();
        $options['username'] = $user->getUsername();
        $options['password'] = $password;
        $options['site_name'] = $this->siteName;
        $options['site_url'] = $this->webPathBuilder->getBasePath();
        $options['admin_name'] = $this->administratorName;
        $options['admin_email'] = $this->administratorEmail;

        $subject = $this->translator->trans('YourRegistrationOn', [], Manager::CONTEXT) . ' ' . $options['site_name'];

        $body = $this->translator->trans('EmailTemplate', $options);

        $mail = new Mail(
            $subject, $body, [$user->getEmail()], true, [], [], $options['admin_name'], $options['admin_email']
        );

        try {
            $this->activeMailer->sendMail($mail);
        }
        catch (Exception) {
            return false;
        }

        return true;
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchClassException
     */
    public function updateAccountFromParameters(
        User $user, ?string $firstName, ?string $lastName, string $username, ?string $officialCode,
        string $emailAddress, ?string $currentPassword, ?string $newPassword, ?User $executingUser = null
    ): bool
    {
        $authentication = $this->authenticationValidator->getAuthenticationByType($user->getAuthenticationSource());

        $user->setGivenName($firstName);
        $user->setSurname($lastName);
        $user->setOfficialCode($officialCode);
        $user->setEmail($emailAddress);

        if ($user->getUsername() != $username && !$this->isUsernameAvailable($username)) {
            throw new RuntimeException('The given username is already taken');
        }

        $user->setUsername($username);

        if (strlen($currentPassword) && $authentication instanceof ChangeablePasswordInterface) {
            if (!$authentication->changePassword($user, $currentPassword, $newPassword, $executingUser)) {
                return false;
            }
        }

        return $this->updateUser($user);
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function updateUser(User $user, ?User $executingUser = null): bool
    {
        if (!$this->userRepository->updateUser($user)) {
            return false;
        }

        $this->eventDispatcher->dispatch(new AfterUserUpdateEvent($user, $executingUser));

        return true;
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function updateUserFromParameters(
        User $user, ?string $firstName, ?string $lastName, ?string $username, ?string $officialCode,
        ?string $emailAddress, bool $generatePassword, ?string $password, ?bool $isPlatformAdmin, ?bool $active,
        bool $sendEmail = false
    ): bool
    {
        if (!is_null($firstName)) {
            $user->setGivenName($firstName);
        }

        if (!is_null($lastName)) {
            $user->setSurname($lastName);
        }

        if (!is_null($officialCode)) {
            $user->setOfficialCode($officialCode);
        }

        if (!is_null($emailAddress)) {
            $user->setEmail($emailAddress);
        }

        if (!is_null($username) && $user->getUsername() != $username && $this->isUsernameAvailable($username)) {
            $user->setUsername($username);
        }

        if (!is_null($isPlatformAdmin)) {
            $user->setPlatformAdministrator($isPlatformAdmin);
        }

        if (!is_null($active)) {
            $user->setActive($active);
        }

        $password = $generatePassword ? $this->passwordGenerator->generatePassword() : $password;

        if (!is_null($password)) {
            $user->setPassword($this->hashingUtilities->hashString($password));
        }

        if (!$this->updateUser($user)) {
            throw new RuntimeException('Could not update the user');
        }

        if ($sendEmail && !$this->sendRegistrationEmailToUser($user, $password)) {
            throw new RuntimeException('Could not send an email to the updated user');
        }

        return $this->updateUser($user);
    }
}

