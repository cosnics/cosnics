<?php

namespace Chamilo\Libraries\Authentication\Platform;

use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Interfaces\ChangeablePassword;
use Chamilo\Libraries\Architecture\Interfaces\ChangeableUsername;
use Chamilo\Libraries\Authentication\AuthenticationException;
use Chamilo\Libraries\Authentication\AuthenticationInterface;
use Chamilo\Libraries\File\Redirect;

/**
 *
 * @package Chamilo\Libraries\Authentication\Platform
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class PlatformAuthentication implements AuthenticationInterface, ChangeablePassword, ChangeableUsername
{
    const PARAM_LOGIN = 'login';
    const PARAM_PASSWORD = 'password';

    /**
     * @var \Chamilo\Libraries\Platform\ChamiloRequest
     */
    protected $request;

    /**
     * @var \Chamilo\Libraries\Hashing\HashingUtilities
     */
    protected $hashingUtilities;

    /**
     * @var \Chamilo\Core\User\Service\UserService
     */
    protected $userService;

    /**
     * @var \Symfony\Component\Translation\Translator
     */
    protected $translator;

    /**
     * @return \Chamilo\Core\User\Storage\DataClass\User
     *
     * @throws \Chamilo\Libraries\Authentication\AuthenticationException
     */
    public function login()
    {
        $username = $this->request->getFromPost(self::PARAM_LOGIN);
        $password = $this->request->getFromPost(self::PARAM_PASSWORD);
        $passwordHash = $this->hashingUtilities->hashString($password);

        $user = $this->userService->findUserByUsername($username);
        if(!$user instanceof User || $user->getAuthenticationSource() != 'Platform')
        {
            return null;
        }

        if ($user->get_password() == $passwordHash)
        {
            return $user;
        }

        throw new AuthenticationException(
            $this->translator->trans('UsernameOrPasswordIncorrect', [], 'Chamilo\Libraries')
        );
    }

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     */
    public function logout(User $user)
    {
        $redirect = new Redirect(array(), array(Application::PARAM_ACTION, Application::PARAM_CONTEXT));
        $redirect->toUrl();
        exit();
    }

    /**
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param string $oldPassword
     * @param string $newPassword
     *
     * @return boolean
     *
     * @throws \Exception
     */
    public function changePassword(User $user, $oldPassword, $newPassword)
    {
        // Check whether we have an actual User object
        if (!$user instanceof User)
        {
            return false;
        }

        // Check whether the current password is different from the new password
        if ($oldPassword == $newPassword)
        {
            return false;
        }

        $oldPasswordHash = $this->hashingUtilities->hashString($oldPassword);

        // Verify that the entered old password matches the stored password
        if ($oldPasswordHash != $user->get_password())
        {
            return false;
        }

        // Set the password
        $user->set_password($this->hashingUtilities->hashString($newPassword));

        return $user->update();
    }

    /**
     *
     * @see \Chamilo\Libraries\Architecture\Interfaces\ChangeablePassword::getPasswordRequirements()
     */
    public function getPasswordRequirements()
    {
        return $this->translator->trans('GeneralPasswordRequirements', [], 'Chamilo\Libraries\Authentication\Platform');
    }
}
