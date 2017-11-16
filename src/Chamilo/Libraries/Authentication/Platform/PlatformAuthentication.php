<?php
namespace Chamilo\Libraries\Authentication\Platform;

use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Interfaces\ChangeablePassword;
use Chamilo\Libraries\Architecture\Interfaces\ChangeableUsername;
use Chamilo\Libraries\Architecture\Traits\DependencyInjectionContainerTrait;
use Chamilo\Libraries\Authentication\AuthenticationException;
use Chamilo\Libraries\Authentication\CredentialsAuthentication;
use Chamilo\Libraries\Translation\Translation;

/**
 *
 * @package Chamilo\Libraries\Authentication\Platform
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class PlatformAuthentication extends CredentialsAuthentication implements ChangeablePassword, ChangeableUsername
{
    use DependencyInjectionContainerTrait;

    /**
     *
     * @param string $userName
     */
    public function __construct($userName = null)
    {
        parent::__construct($userName);
        $this->initializeContainer();
    }

    /**
     *
     * @return \Chamilo\Libraries\Hashing\HashingUtilities
     */
    public function getHashingUtilities()
    {
        return $this->getService('chamilo.libraries.hashing.hashing_utilities');
    }

    /**
     *
     * @see \Chamilo\Libraries\Authentication\CredentialsAuthentication::login()
     */
    public function login($password)
    {
        $passwordHash = $this->getHashingUtilities()->hashString($password);

        if ($this->getUser() instanceof User && $this->getUser()->get_password() == $passwordHash)
        {

            return true;
        }
        else
        {
            throw new AuthenticationException(Translation::get('UsernameOrPasswordIncorrect'));
        }
    }

    /**
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param string $oldPassword
     * @param string $newPassword
     *
     * @return boolean
     */
    public function changePassword(User $user, $oldPassword, $newPassword)
    {
        // Check whether we have an actual User object
        if (! $user instanceof User)
        {
            return false;
        }

        // Check whether the current password is different from the new password
        if ($oldPassword == $newPassword)
        {
            return false;
        }

        $oldPasswordHash = $this->getHashingUtilities()->hashString($oldPassword);

        // Verify that the entered old password matches the stored password
        if ($oldPasswordHash != $user->get_password())
        {
            return false;
        }

        // Set the password
        $user->set_password($this->getHashingUtilities()->hashString($newPassword));

        return $user->update();
    }

    /**
     *
     * @see \Chamilo\Libraries\Architecture\Interfaces\ChangeablePassword::getPasswordRequirements()
     */
    public function getPasswordRequirements()
    {
        return Translation::get('GeneralPasswordRequirements');
    }
}
