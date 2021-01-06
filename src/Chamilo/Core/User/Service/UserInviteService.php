<?php

namespace Chamilo\Core\User\Service;

use Chamilo\Core\Group\Service\GroupService;
use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Core\User\Component\AcceptInviteComponent;
use Chamilo\Core\User\Domain\UserInvite\Exceptions\UserAlreadyExistsException;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Core\User\Storage\DataClass\UserInvite;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Mail\ValueObject\Mail;
use Chamilo\Libraries\Utilities\DatetimeUtilities;

/**
 * @package Chamilo\Core\User\Service
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class UserInviteService
{
    /**
     * @var \Chamilo\Core\User\Storage\Repository\UserInviteRepository
     */
    protected $userInviteRepository;

    /**
     * @var \Chamilo\Core\User\Service\UserService
     */
    protected $userService;

    /**
     * @var \Twig\Environment
     */
    protected $twig;

    /**
     * @var \Chamilo\Libraries\Mail\Mailer\MailerInterface
     */
    protected $mailer;

    /**
     * @var \Symfony\Component\Translation\Translator
     */
    protected $translator;

    /**
     * @var \Chamilo\Libraries\File\PathBuilder
     */
    protected $pathBuilder;

    /**
     * @var \Chamilo\Configuration\Service\ConfigurationConsulter
     */
    protected $configurationConsulter;

    /**
     * @var \Chamilo\Core\Group\Service\GroupService
     */
    protected $groupService;

    /**
     * UserInviteService constructor.
     *
     * @param \Chamilo\Core\User\Storage\Repository\UserInviteRepository $userInviteRepository
     * @param \Chamilo\Core\User\Service\UserService $userService
     * @param \Twig\Environment $twig
     * @param \Chamilo\Libraries\Mail\Mailer\MailerInterface $mailer
     * @param \Symfony\Component\Translation\Translator $translator
     * @param \Chamilo\Libraries\File\PathBuilder $pathBuilder
     * @param \Chamilo\Configuration\Service\ConfigurationConsulter $configurationConsulter
     * @param \Chamilo\Core\Group\Service\GroupService $groupService
     */
    public function __construct(
        \Chamilo\Core\User\Storage\Repository\UserInviteRepository $userInviteRepository,
        \Chamilo\Core\User\Service\UserService $userService, \Twig\Environment $twig,
        \Chamilo\Libraries\Mail\Mailer\MailerInterface $mailer, \Symfony\Component\Translation\Translator $translator,
        \Chamilo\Libraries\File\PathBuilder $pathBuilder,
        \Chamilo\Configuration\Service\ConfigurationConsulter $configurationConsulter,
        GroupService $groupService
    )
    {
        $this->userInviteRepository = $userInviteRepository;
        $this->userService = $userService;
        $this->twig = $twig;
        $this->mailer = $mailer;
        $this->translator = $translator;
        $this->pathBuilder = $pathBuilder;
        $this->configurationConsulter = $configurationConsulter;
        $this->groupService = $groupService;
    }

    /**
     * This method creates a new inactive user into the database and creates an invite for the user. The invitation is
     * then sent to the user by email. This method returns the created user so it can automatically be used to set
     * rights for that external user in the context.
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $invitedByUser
     * @param string $userEmail
     *
     * @param \DateTime $expirationDate
     *
     * @param string|null $personalMessage
     *
     * @return \Chamilo\Core\User\Storage\DataClass\User
     * @throws UserAlreadyExistsException
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     * @throws \Exception
     */
    public function inviteUser(
        User $invitedByUser, string $userEmail, \DateTime $expirationDate, string $personalMessage = null
    )
    {
        $this->validateUserEmail($userEmail);
        $user = $this->createUserByEmail($userEmail, $expirationDate);
        $userInvite = $this->createUserInvite($invitedByUser, $user);
//        $user = $this->userService->findUserByIdentifier(2);
//        $userInvite = $this->userInviteRepository->getUserInviteBySecurityKey(
//            '59f7b3e215c0497ba75588c71990635a7f31e27b267fe19aa341077c815e65ca'
//        );
        $this->addUserToExternalGroup($user);
        $this->sendInvitationEmail($invitedByUser, $user, $userInvite, $personalMessage);

        return $user;
    }

    /**
     * @param User $invitedByUser
     * @param UserInvite $userInvite
     *
     * @return UserInvite
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function extendUserInvite(User $invitedByUser, UserInvite $userInvite)
    {
        $currentDate = new \DateTime();
        $validUntil = $currentDate->add(new \DateInterval('P7D'));

        $userInvite->setValidUntil($validUntil);
        $userInvite->setStatus(UserInvite::STATUS_INVITED);

        if (!$this->userInviteRepository->updateUserInvite($userInvite))
        {
            throw new \InvalidArgumentException('Could not extend the user invite with id ' . $userInvite->getId());
        }

        $invitedUser = $this->userService->findUserByIdentifier($userInvite->getUserId());
        if(!$invitedUser instanceof User)
        {
            throw new \RuntimeException(sprintf('Invited user with id %s not found', $userInvite->getUserId()));
        }

        $this->sendInvitationEmail($invitedByUser, $invitedUser, $userInvite);

        return $userInvite;
    }

    /**
     * @param string|null $securityKey
     *
     * @return UserInvite
     * @throws \Exception
     */
    public function getUserInviteBySecurityKey(string $securityKey = null)
    {
        if (empty($securityKey))
        {
            throw new \InvalidArgumentException('The given security key can not be empty');
        }

        $userInvite = $this->userInviteRepository->getUserInviteBySecurityKey($securityKey);
        if ($userInvite->isOpen())
        {
            return $userInvite;
        }

        return null;
    }

    /**
     * @param int $userInviteId
     *
     * @return UserInvite
     */
    public function getUserInviteById(int $userInviteId)
    {
        return $this->userInviteRepository->getUserInviteById($userInviteId);
    }

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\UserInvite|null $userInvite
     *
     * @return string
     */
    public function getDefaultEmailFromUserInvite(UserInvite $userInvite = null)
    {
        if (!$userInvite instanceof UserInvite)
        {
            return null;
        }

        $user = $this->userService->findUserByIdentifier($userInvite->getUserId());
        if ($user instanceof User)
        {
            return $user->get_email();
        }

        return null;
    }

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\UserInvite $userInvite
     * @param string $firstName
     * @param string $lastName
     * @param string $password
     *
     * @return \Chamilo\Core\User\Storage\DataClass\User
     * @throws \Exception
     */
    public function acceptInvitation(
        UserInvite $userInvite, string $firstName, string $lastName, string $password
    )
    {
        if (!$userInvite->isOpen())
        {
            throw new \RuntimeException(
                sprintf(
                    'The given user invite with id %s is no longer valid and should not be accepted',
                    $userInvite->getId()
                )
            );
        }
        $user = $this->userService->findUserByIdentifier($userInvite->getUserId());
        if (!$user instanceof User)
        {
            throw new \RuntimeException(
                'The given user with id %s could not be found. The invitation can not be completed'
            );
        }

        $this->userService->updateUserByValues(
            $user, $firstName, $lastName, null, null, null, $password, null, true
        );

        $userInvite->setStatus(UserInvite::STATUS_ACCEPTED);
        if (!$this->userInviteRepository->updateUserInvite($userInvite))
        {
            throw new \InvalidArgumentException(
                'Could not change the status of the user invite ' . $userInvite->getId()
            );
        }

        return $user;
    }

    /**
     * Returns the invites made by the given user
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @return \Chamilo\Libraries\Storage\Iterator\RecordIterator
     */
    public function getInvitesFromUser(User $user)
    {
        $invites = $this->userInviteRepository->getUserInvitesFromUser($user);

        foreach ($invites as $index => $existingInvite)
        {
            if ($existingInvite['status'] == UserInvite::STATUS_INVITED)
            {
                if ($existingInvite['valid_until'] < time())
                {
                    $invites[$index]['status'] = UserInvite::STATUS_EXPIRED;
                }
            }

            $invites[$index]['valid_until'] =
                DatetimeUtilities::format_locale_date(null, $existingInvite['valid_until']);
        }

        return $invites;
    }

    /**
     * @param string $userEmail
     *
     * @throws \Chamilo\Core\User\Domain\UserInvite\Exceptions\UserAlreadyExistsException
     */
    protected function validateUserEmail(string $userEmail)
    {
        if (!$this->isStringValidEmail($userEmail))
        {
            throw new \InvalidArgumentException(sprintf('The given email %s is invalid', $userEmail));
        }

        $userByEmail = $this->userService->getUserByUsernameOrEmail($userEmail);
        if ($userByEmail instanceof User)
        {
            throw new UserAlreadyExistsException();
        }
    }

    /**
     * @param string $userEmail
     *
     * @return bool
     */
    protected function isStringValidEmail(string $userEmail)
    {
        return preg_match("/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix", $userEmail);
    }

    /**
     * @param string $userEmail
     * @param \DateTime|null $expirationDate
     *
     * @return \Chamilo\Core\User\Storage\DataClass\User
     */
    protected function createUserByEmail(string $userEmail, \DateTime $expirationDate):
    \Chamilo\Core\User\Storage\DataClass\User
    {
        $officialCode = $this->userService->generateUniqueUsername();

        $user = $this->userService->createUser(
            'Invited User', 'Invited User', $userEmail, $officialCode, $userEmail, null, 'Platform', true,
            $expirationDate
        );

        if (!$user instanceof User)
        {
            throw new \RuntimeException('The invited user could not be created for email ' . $userEmail);
        }

        return $user;
    }

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $invitedByUser
     * @param \Chamilo\Core\User\Storage\DataClass\User $invitedUser
     *
     * @return \Chamilo\Core\User\Storage\DataClass\UserInvite
     * @throws \Exception
     */
    protected function createUserInvite(User $invitedByUser, User $invitedUser)
    {
        $currentDate = new \DateTime();
        $validUntil = $currentDate->add(new \DateInterval('P7D'));

        $invite = new UserInvite();
        $invite->setUserId($invitedUser->getId());
        $invite->setInvitedByUserId($invitedByUser->getId());
        $invite->setSecurityKey(hash('sha256', uniqid() . $invitedUser->getId() . uniqid()));
        $invite->setValidUntil($validUntil);
        $invite->setStatus(UserInvite::STATUS_INVITED);

        if (!$this->userInviteRepository->createUserInvite($invite))
        {
            throw new \InvalidArgumentException('Could not create a new user invite for user ' . $invitedUser->getId());
        }

        return $invite;
    }

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     */
    protected function addUserToExternalGroup(User $user)
    {
        $externalUsersGroupId =
            $this->configurationConsulter->getSetting(['Chamilo\Core\User', 'external_users_group_id']);

        if (empty($externalUsersGroupId))
        {
            return;
        }

        $externalUsersGroup = $this->groupService->getGroupByIdentifier($externalUsersGroupId);
        if (!$externalUsersGroup instanceof Group)
        {
            return;
        }

        $this->groupService->subscribeUserToGroup($externalUsersGroup, $user);
    }

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $invitedByUser
     * @param \Chamilo\Core\User\Storage\DataClass\User $invitedUser
     * @param UserInvite|null $userInvite
     * @param string|null $personalMessage
     *
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    protected function sendInvitationEmail(
        User $invitedByUser, User $invitedUser, UserInvite $userInvite = null, string $personalMessage = null
    )
    {
        $siteUrl = $this->pathBuilder->getBasePath(true);
        $subject = $this->translator->trans('InviteUserMailSubject', ['{SITE_URL}' => $siteUrl], 'Chamilo\Core\User');

        $redirect = new Redirect(
            [
                Application::PARAM_CONTEXT => \Chamilo\Core\User\Manager::context(),
                Application::PARAM_ACTION => \Chamilo\Core\User\Manager::ACTION_ACCEPT_INVITE,
                AcceptInviteComponent::PARAM_SECURITY_KEY => $userInvite->getSecurityKey()
            ]
        );

        $inviteUrl = $redirect->getUrl();

        $content = $this->twig->render(
            'Chamilo\Core\User:InviteUser.eml.html.twig',
            [
                'INVITED_USER' => $invitedUser, 'INVITED_BY_USER' => $invitedByUser, 'USER_INVITE' => $userInvite,
                'SITE_URL' => $siteUrl, 'SUBJECT' => $subject,
                'PERSONAL_MESSAGE' => $personalMessage,
                'LOGO' => $this->configurationConsulter->getSetting(['Chamilo\Core\Menu', 'brand_image']),
                'ACCEPT_INVITE_URL' => $inviteUrl
            ]
        );

//        echo $content;

        $mail = new Mail(
            $subject, $content, [$invitedUser->get_email()], true, [], [], $invitedByUser->get_fullname(),
            $invitedByUser->get_email()
        );

        $this->mailer->sendMail($mail);
    }

}
