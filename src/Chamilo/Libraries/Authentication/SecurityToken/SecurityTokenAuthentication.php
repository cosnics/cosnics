<?php
namespace Chamilo\Libraries\Authentication\SecurityToken;

use Chamilo\Core\Tracking\Storage\DataClass\Event;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Authentication\Authentication;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Session\Session;

/**
 *
 * @author Magali Gillard
 */
class SecurityTokenAuthentication extends Authentication
{

    /**
     *
     * @param User The current user object
     * @param string The user's current password
     * @param string The desired new password
     * @return boolean True if changed, false if not
     * @see Authentication::change_password()
     */
    public function change_password($user, $old_password, $new_password)
    {
        return false;
    }

    /**
     *
     * @return string Instructions for the password
     * @see Authentication::get_password_requirements()
     */
    public function get_password_requirements()
    {
    }

    /**
     *
     * @param string $username
     * @param string $password
     * @return true
     * @see Authentication::check_login()
     */
    public function check_login($user, $username, $password = null)
    {
        $security_token = Request :: get(User :: PROPERTY_SECURITY_TOKEN);

        if ($security_token)
        {
            $user = \Chamilo\Core\User\Storage\DataManager :: retrieve_user_by_security_token($security_token);

            if ($user instanceof User && $user->is_active())
            {
                Session :: register('_uid', $user->get_id());
                Event :: trigger(
                    'Login',
                    \Chamilo\Core\User\Manager :: context(),
                    array('server' => $_SERVER, 'user' => $user));
                return $user;
            }
            else
            {
                return false;
            }
        }
        else
        {
            return false;
        }
    }
}
