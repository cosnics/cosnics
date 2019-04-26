<?php

namespace Chamilo\Libraries\Authentication\Platform;

use Chamilo\Configuration\Service\ConfigurationConsulter;
use Chamilo\Core\User\Service\PasswordSecurity;
use Chamilo\Core\User\Service\UserService;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Interfaces\ChangeablePassword;
use Chamilo\Libraries\Architecture\Interfaces\ChangeableUsername;
use Chamilo\Libraries\Authentication\Authentication;
use Chamilo\Libraries\Authentication\AuthenticationException;
use Chamilo\Libraries\Authentication\AuthenticationInterface;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Hashing\HashingUtilities;
use Chamilo\Libraries\Platform\ChamiloRequest;
use Symfony\Component\Translation\Translator;

/**
 *
 * @package Chamilo\Libraries\Authentication\Platform
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class PlatformAuthentication extends Authentication
    implements AuthenticationInterface, ChangeablePassword, ChangeableUsername
{

    /**
     * @var \Chamilo\Libraries\Hashing\HashingUtilities
     */
    protected $hashingUtilities;

    /**
     * @var \Chamilo\Core\User\Service\PasswordSecurity
     */
    private $passwordSecurity;

    /**
     * Authentication constructor.
     *
     * @param \Chamilo\Configuration\Service\ConfigurationConsulter $configurationConsulter
     * @param \Symfony\Component\Translation\Translator $translator
     * @param \Chamilo\Libraries\Platform\ChamiloRequest $request
     * @param \Chamilo\Core\User\Service\UserService $userService
     * @param \Chamilo\Libraries\Hashing\HashingUtilities $hashingUtilities
     * @param \Chamilo\Core\User\Service\PasswordSecurity $passwordSecurity
     */
    public function __construct(
        ConfigurationConsulter $configurationConsulter, Translator $translator, ChamiloRequest $request,
        UserService $userService, HashingUtilities $hashingUtilities, PasswordSecurity $passwordSecurity
    )
    {
        parent::__construct($configurationConsulter, $translator, $request, $userService);
        $this->hashingUtilities = $hashingUtilities;
        $this->passwordSecurity = $passwordSecurity;
    }

    /**
     * @return \Chamilo\Core\User\Storage\DataClass\User
     *
     * @throws \Chamilo\Libraries\Authentication\AuthenticationException
     */
    public function login()
    {
        $user = $this->getUserFromCredentialsRequest();
        if(!$user instanceof User)
        {
            return null;
        }

        $password = $this->request->getFromPost(self::PARAM_PASSWORD);

        if($this->passwordSecurity->isPasswordValidForUser($user, $password))
        {
            return $user;
        }

        if($this->isPasswordValidWithOldHashingMethod($user, $password))
        {
            return $user;
        }

        throw new AuthenticationException(
            $this->translator->trans('UsernameOrPasswordIncorrect', [], 'Chamilo\Libraries')
        );
    }

    /**
     * Checks if the password is still valid with the old hashing method and silently converts the password to a new password
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param string $password
     *
     * @return bool
     */
    protected function isPasswordValidWithOldHashingMethod(User $user, string $password)
    {
        $passwordHash = $this->hashingUtilities->hashString($password);

        if ($user->get_password() == $passwordHash)
        {
            $this->passwordSecurity->convertPasswordForUser($user, $password);
            return true;
        }

        return false;
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
        $this->passwordSecurity->setPasswordForUser($user, $newPassword);

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

    /**
     * Returns the priority of the authentication, lower priorities come first
     *
     * @return int
     */
    public function getPriority()
    {
        return 200;
    }

    /**
     * Returns the short name of the authentication to check in the settings
     *
     * @return string
     */
    public function getAuthenticationType()
    {
        return 'Platform';
    }
}
