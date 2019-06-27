<?php

namespace Chamilo\Core\User\Service;

use Chamilo\Core\User\Component\AcceptInviteComponent;
use Chamilo\Core\User\Domain\UserInvite\Exceptions\UserAlreadyExistsException;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Core\User\Storage\DataClass\UserInvite;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Mail\ValueObject\Mail;

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
     * @var \Twig_Environment
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
     * UserInviteService constructor.
     *
     * @param \Chamilo\Core\User\Storage\Repository\UserInviteRepository $userInviteRepository
     * @param \Chamilo\Core\User\Service\UserService $userService
     * @param \Twig_Environment $twig
     * @param \Chamilo\Libraries\Mail\Mailer\MailerInterface $mailer
     * @param \Symfony\Component\Translation\Translator $translator
     * @param \Chamilo\Libraries\File\PathBuilder $pathBuilder
     * @param \Chamilo\Configuration\Service\ConfigurationConsulter $configurationConsulter
     */
    public function __construct(
        \Chamilo\Core\User\Storage\Repository\UserInviteRepository $userInviteRepository,
        \Chamilo\Core\User\Service\UserService $userService, \Twig_Environment $twig,
        \Chamilo\Libraries\Mail\Mailer\MailerInterface $mailer, \Symfony\Component\Translation\Translator $translator,
        \Chamilo\Libraries\File\PathBuilder $pathBuilder,
        \Chamilo\Configuration\Service\ConfigurationConsulter $configurationConsulter
    )
    {
        $this->userInviteRepository = $userInviteRepository;
        $this->userService = $userService;
        $this->twig = $twig;
        $this->mailer = $mailer;
        $this->translator = $translator;
        $this->pathBuilder = $pathBuilder;
        $this->configurationConsulter = $configurationConsulter;
    }

    /**
     * This method creates a new inactive user into the database and creates an invite for the user. The invitation is
     * then sent to the user by email. This method returns the created user so it can automatically be used to set
     * rights for that external user in the context.
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $invitedByUser
     * @param string $userEmail
     *
     * @param string $personalMessage
     *
     * @return \Chamilo\Core\User\Storage\DataClass\User
     * @throws \Chamilo\Core\User\Domain\UserInvite\Exceptions\UserAlreadyExistsException
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     * @throws \Exception
     */
    public function inviteUser(User $invitedByUser, string $userEmail, string $personalMessage = '')
    {
        $this->validateUserEmail($userEmail);
        $user = $this->createUserByEmail($userEmail);
        $userInvite = $this->createUserInvite($invitedByUser, $user);
        $this->sendInvitationEmail($invitedByUser, $user, $userInvite, $personalMessage);

        return $user;
    }

    /**
     * @param string $securityKey
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
        if ($userInvite->isValid())
        {
            return $userInvite;
        }
        else
        {
            $this->userInviteRepository->deleteUserInvite($userInvite);
        }

        return null;
    }

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\UserInvite|null $userInvite
     *
     * @return string
     */
    public function getDefaultEmailFromUserInvite(UserInvite $userInvite = null)
    {
        if(!$userInvite instanceof UserInvite)
        {
            return null;
        }

        $user = $this->userService->findUserByIdentifier($userInvite->getUserId());
        if($user instanceof User)
        {
            return $user->get_email();
        }

        return null;
    }
    /**
     * @param \Chamilo\Core\User\Storage\DataClass\UserInvite $userInvite
     * @param string $firstName
     * @param string $lastName
     * @param string $email
     * @param string $password
     *
     * @return \Chamilo\Core\User\Storage\DataClass\User
     */
    public function acceptInvitation(
        UserInvite $userInvite, string $firstName, string $lastName, string $password
    )
    {
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

        $this->userInviteRepository->deleteUserInvite($userInvite);

        return $user;
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
     *
     * @return \Chamilo\Core\User\Storage\DataClass\User
     */
    protected function createUserByEmail(string $userEmail): \Chamilo\Core\User\Storage\DataClass\User
    {
        $officialCode = $this->userService->generateUniqueUsername();
        $user = $this->userService->createUser('Invited User', 'Invited User', $userEmail, $officialCode, $userEmail);
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

        if (!$this->userInviteRepository->createUserInvite($invite))
        {
            throw new \InvalidArgumentException('Could not create a new user invite for user ' . $invitedUser->getId());
        }

        return $invite;
    }

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $invitedByUser
     * @param \Chamilo\Core\User\Storage\DataClass\User $invitedUser
     * @param \Chamilo\Core\User\Storage\DataClass\UserInvite $userInvite
     * @param string $personalMessage
     *
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    protected function sendInvitationEmail(
        User $invitedByUser, User $invitedUser, UserInvite $userInvite = null, string $personalMessage = ''
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

//        $mail = new Mail(
//            $subject, $content, [$invitedUser->get_email()], true, [], [], $invitedByUser->get_fullname(),
//            $invitedByUser->get_email()
//        );

//        echo($content);
//        $this->mailer->sendMail($mail);
    }

}