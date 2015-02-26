<?php
namespace Chamilo\Libraries\Authentication\Platform;






use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Interfaces\ChangeablePassword;
use Chamilo\Libraries\Architecture\Interfaces\ChangeableUsername;
use Chamilo\Libraries\Authentication\Authentication;
use Chamilo\Libraries\Hashing\Hashing;
use Chamilo\Libraries\Platform\Translation;

/**
 * $Id: platform_authentication.class.php 128 2009-11-09 13:13:20Z vanpouckesven $
 *
 * @package common.authentication.platform
 */
/**
 * This authentication class implements the default authentication method for the platform using hashed passwords.
 */
class PlatformAuthentication extends Authentication implements ChangeablePassword, ChangeableUsername
{

    public function __construct()
    {
    }

    public function check_login($user, $username, $password = null)
    {
        $user_expiration_date = $user->get_expiration_date();
        $user_activation_date = $user->get_activation_date();

        if (($user_expiration_date != '0' && $user_expiration_date < time()) ||
             ($user_activation_date != '0' && $user_activation_date > time()) || ! $user->get_active())
        {
            $this->set_message(Translation :: get("AccountNotActive"));
            return false;
        }
        else
        {
            if ($user->get_username() == $username && $user->get_password() == Hashing :: hash($password))
            {
                return true;
            }

            $this->set_message(Translation :: get("UsernameOrPasswordIncorrect"));
            return false;
        }
    }

    /**
     * We're changing a local password, so just set the user's new password and it will be updated automatically when
     * the form is processed.
     *
     * @see Authentication :: change_password()
     */
    public function change_password($user, $old_password, $new_password)
    {
        // Check whether we have an actual User object
        if (! $user instanceof User)
        {
            return false;
        }

        // Check whether the current password is different from the new password
        if ($old_password == $new_password)
        {
            return false;
        }

        $user->set_password(Hashing :: hash($new_password));
        return true;
    }

    public function get_password_requirements()
    {
        return Translation :: get('GeneralPasswordRequirements');
    }

    public function is_configured()
    {
        return true;
    }
}
