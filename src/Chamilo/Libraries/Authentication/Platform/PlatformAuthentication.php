<?php
namespace Chamilo\Libraries\Authentication\Platform;

use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Interfaces\ChangeablePassword;
use Chamilo\Libraries\Architecture\Interfaces\ChangeableUsername;
use Chamilo\Libraries\Authentication\AuthenticationException;
use Chamilo\Libraries\Authentication\CredentialsAuthentication;
use Chamilo\Libraries\Hashing\Hashing;
use Chamilo\Libraries\Platform\Translation;

/**
 *
 * @package Chamilo\Libraries\Authentication\Platform
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class PlatformAuthentication extends CredentialsAuthentication implements ChangeablePassword, ChangeableUsername
{

    /**
     *
     * @see \Chamilo\Libraries\Authentication\CredentialsAuthentication::login()
     */
    public function login($password)
    {
        if ($this->getUser() instanceof User && $this->getUser()->get_password() == Hashing :: hash($password))
        {

            return true;
        }
        else
        {
            throw new AuthenticationException(Translation :: get('UsernameOrPasswordIncorrect'));
        }
    }

    /**
     *
     * @param User $user
     * @param string $oldPassword
     * @param string $newPassword
     *
     * @return bool
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

        // Verify that the entered old password matches the stored password
        if (Hashing :: hash($oldPassword) != $user->get_password())
        {
            return false;
        }

        // Set the password
        $user->set_password(Hashing :: hash($newPassword));

        return $user->update();
    }

    public function getPasswordRequirements()
    {
        return Translation :: get('GeneralPasswordRequirements');
    }
}
