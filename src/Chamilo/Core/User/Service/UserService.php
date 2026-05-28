<?php
namespace Chamilo\Core\User\Service;

use Chamilo\Core\User\Architecture\Enum\ActionEnum;
use Chamilo\Core\User\Architecture\EventDispatcher\Event\AfterUserCreateEvent;
use Chamilo\Core\User\Architecture\EventDispatcher\Event\AfterUserDeleteEvent;
use Chamilo\Core\User\Architecture\EventDispatcher\Event\AfterUserPasswordResetEvent;
use Chamilo\Core\User\Architecture\EventDispatcher\Event\AfterUserRegistrationEvent;
use Chamilo\Core\User\Architecture\EventDispatcher\Event\AfterUserUpdateEvent;
use Chamilo\Core\User\Architecture\EventDispatcher\Event\BeforeUserDeleteEvent;
use Chamilo\Core\User\Architecture\Exception\NoSuchUserException;
use Chamilo\Core\User\Manager;
use Chamilo\Core\User\Storage\Entity\User;
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
use Chamilo\Libraries\Storage\Architecture\Exception\EntityAlreadyExistsException;
use Chamilo\Libraries\Storage\Architecture\Interface\ConditionInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Exception;
use Hackzilla\PasswordGenerator\Generator\PasswordGeneratorInterface;
use InvalidArgumentException;
use RuntimeException;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Translation\Translator;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Uid\UuidV7;
use Throwable;

/**
 * @package Chamilo\Core\User\Service
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 */
readonly class UserService
{
    public function __construct(
        protected HashingAlgorithm $hashingUtilities, protected Translator $translator,
        protected WebPathBuilder $webPathBuilder, protected MailerInterface $activeMailer,
        protected PasswordGeneratorInterface $passwordGenerator,
        protected AuthenticationValidator $authenticationValidator, protected UrlGenerator $urlGenerator,
        protected EventDispatcherInterface $eventDispatcher, private string $securityKey, protected string $siteName,
        protected string $administratorName, protected string $administratorEmail, protected bool $allowRegistration,
        protected UserRepository $userRepository
    )
    {
    }

    /**
     * @throws \Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchClassException
     */
    public function countUsers(?ConditionInterface $condition = null): int
    {
        return $this->userRepository->countUsers($condition);
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\EntityAlreadyExistsException
     */
    public function createNewPasswordForUser(User $user, ?User $executingUser = null, bool $flush = true): void
    {
        $translator = $this->translator;

        $newPassword = $this->passwordGenerator->generatePassword();

        $user->setPassword($this->hashingUtilities->hashString($newPassword));
        $this->updateUser($user);
        $this->eventDispatcher->dispatch(new AfterUserPasswordResetEvent($user, $executingUser, $flush));

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
        }
        catch (Exception) {
        }
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\EntityAlreadyExistsException
     */
    public function createUser(User $user, ?User $executingUser = null, bool $flush = true): void
    {
        $user->setRegistrationDate(time());
        $user->setSecurityToken(sha1(time() . uniqid()));

        $this->userRepository->saveUser($user, $flush);
        $this->eventDispatcher->dispatch(new AfterUserCreateEvent($user, $executingUser, $flush));
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\ObjectAlreadyExistsException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\EntityAlreadyExistsException
     */
    public function createUserFromParameters(
        ?string $givenName, ?string $surname, string $username, ?string $officialCode, string $emailAddress,
        bool $generatePassword, ?string $password,
        ?string $authSource = 'Chamilo\Libraries\Protocol\Authentication\Service\PlatformAuthentication',
        bool $isPlatformAdmin = false, bool $active = true, bool $sendEmail = false, ?User $executingUser = null,
        bool $flush = true
    ): User
    {
        $requiredParameters = [
            'username' => $username,
            'emailAddress' => $emailAddress
        ];

        foreach ($requiredParameters as $parameterName => $parameterValue) {
            if (empty($parameterValue)) {
                throw new InvalidArgumentException('The ' . $parameterName . ' can not be empty');
            }
        }

        if (!$this->isUsernameAvailable($username)) {
            throw new EntityAlreadyExistsException(entityClassname: User::class, criteria: ['username' => $username]);
        }

        if ($officialCode && !$this->isOfficialCodeAvailable($officialCode)) {
            throw new EntityAlreadyExistsException(
                entityClassname: User::class, criteria: ['emailAddress' => $emailAddress]
            );
        }

        $user = new User();

        $user->setIdentifier(new UuidV7());
        $user->setGivenName($givenName);
        $user->setSurname($surname);
        $user->setUsername($username);
        $user->setOfficialCode($officialCode);
        $user->setEmail($emailAddress);
        $user->setAuthenticationSource($authSource);
        $user->setPlatformAdministrator($isPlatformAdmin);
        $user->setActive($active);

        $password = $generatePassword ? $this->passwordGenerator->generatePassword() : $password;
        $user->setPassword($this->hashingUtilities->hashString($password));

        $this->createUser($user, $executingUser, $flush);

        if ($sendEmail) {
            try {
                $this->sendRegistrationEmailToUser($user, $password);
            }
            catch (Throwable) {
                throw new RuntimeException('Could not send an email to the new user');
            }
        }

        return $user;
    }

    public function deleteUser(User $user, ?User $executingUser = null, bool $flush = true): void
    {
        $this->eventDispatcher->dispatch(new BeforeUserDeleteEvent($user, $executingUser, $flush));
        $this->userRepository->removeUser($user, $flush);
        $this->eventDispatcher->dispatch(new AfterUserDeleteEvent($user, $executingUser, $flush));
    }

    public function determineUserKey(User $user): string
    {
        return $this->hashingUtilities->hashString($this->securityKey . $user->getEmail());
    }

    /**
     * @throws \Chamilo\Core\User\Architecture\Exception\NoSuchUserException
     */
    public function findUserByEmail(string $email): ?User
    {
        return $this->userRepository->findUserByEmail($email);
    }

    /**
     * @throws \Chamilo\Core\User\Architecture\Exception\NoSuchUserException
     */
    public function findUserByIdentifier(Uuid $identifier): User
    {
        return $this->userRepository->findUserByIdentifier($identifier);
    }

    /**
     * @throws \Chamilo\Core\User\Architecture\Exception\NoSuchUserException
     */
    public function findUserByOfficialCode(string $officialCode): ?User
    {
        return $this->userRepository->findUserByOfficialCode($officialCode);
    }

    /**
     * @throws \Chamilo\Core\User\Architecture\Exception\NoSuchUserException
     */
    public function findUserBySecurityToken(string $securityToken): ?User
    {
        return $this->userRepository->findUserBySecurityToken($securityToken);
    }

    /**
     * @throws \Chamilo\Core\User\Architecture\Exception\NoSuchUserException
     */
    public function findUserByUsername(string $username): ?User
    {
        return $this->userRepository->findUserByUsername($username);
    }

    /**
     * @throws \Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchClassException
     * @throws \Chamilo\Core\User\Architecture\Exception\NoSuchUserException
     */
    public function findUserByUsernameOrEmail(string $usernameOrEmail): ?User
    {
        return $this->userRepository->findUserByUsernameOrEmail($usernameOrEmail);
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\User\Storage\Entity\User>
     * @throws \Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchClassException
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
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\User\Storage\Entity\User>
     * @throws \Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchClassException
     */
    public function findUsersByIdentifiers(array $userIdentifiers = []): ArrayCollection
    {
        return $this->userRepository->findUsersByIdentifiers($userIdentifiers);
    }

    public function flushEntities(): void
    {
        $this->userRepository->flush();
    }

    /**
     * @throws \Doctrine\ORM\Exception\ORMException
     */
    public function getUserReference(Uuid $userIdentifier): User
    {
        return $this->userRepository->getUserReference($userIdentifier);
    }

    public function isOfficialCodeAvailable(string $officialCode): bool
    {
        try {
            $this->findUserByOfficialCode($officialCode);

            return false;
        }
        catch (NoSuchUserException) {
            return true;
        }
        catch (Throwable) {
            return false;
        }
    }

    public function isUsernameAvailable(string $username): bool
    {
        try {
            $this->findUserByUsername($username);

            return false;
        }
        catch (NoSuchUserException) {
            return true;
        }
        catch (Throwable) {
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
        ?User $executingUser = null, bool $flush = true
    ): User
    {
        $user = $this->createUserFromParameters(
            $firstName, $lastName, $username, $officialCode, $emailAddress, $generatePassword, $password, $authSource,
            false, $this->allowRegistration, $sendEmail, $executingUser
        );

        $this->eventDispatcher->dispatch(new AfterUserRegistrationEvent($user, $executingUser, $flush));

        return $user;
    }

    /**
     * @throws \Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\UserException
     * @throws \Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchClassException
     */
    public function sendPasswordResetLinkforUser(User $user): void
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
                    DataClass::PROPERTY_ID => $user->getIdentifier()->toString()
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

    public function sendRegistrationEmailToUser(User $user, string $password): void
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

        $this->activeMailer->sendMail($mail);
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\EntityAlreadyExistsException
     * @throws \Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchClassException
     */
    public function updateAccountFromParameters(
        User $user, ?string $firstName, ?string $lastName, string $username, ?string $officialCode,
        string $emailAddress, ?string $currentPassword, ?string $newPassword, ?User $executingUser = null
    ): void
    {
        $authentication = $this->authenticationValidator->getAuthenticationByType($user->getAuthenticationSource());

        $user->setGivenName($firstName);
        $user->setSurname($lastName);
        $user->setOfficialCode($officialCode);
        $user->setEmail($emailAddress);

        if (!$this->isUsernameAvailableForUser($user, $username)) {
            throw new RuntimeException('The given username is already taken');
        }

        $user->setUsername($username);

        if (strlen($currentPassword) && $authentication instanceof ChangeablePasswordInterface) {
            $authentication->changePassword($user, $currentPassword, $newPassword, $executingUser);
        }

        $this->updateUser($user);
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\EntityAlreadyExistsException
     */
    public function updateUser(User $user, ?User $executingUser = null, bool $flush = true): void
    {
        $this->userRepository->saveUser($user, $flush);
        $this->eventDispatcher->dispatch(new AfterUserUpdateEvent($user, $executingUser, $flush));
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\EntityAlreadyExistsException
     */
    public function updateUserFromParameters(
        User $user, ?string $firstName, ?string $lastName, ?string $username, ?string $officialCode,
        ?string $emailAddress, bool $generatePassword, ?string $password, ?bool $isPlatformAdmin, ?bool $active,
        bool $sendEmail = false
    ): void
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

        if (!is_null($username) && $this->isUsernameAvailableForUser($user, $username)) {
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

        $this->updateUser($user);

        if ($sendEmail) {
            $this->sendRegistrationEmailToUser($user, $password);
        }
    }
}

