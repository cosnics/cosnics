<?php
namespace Chamilo\Libraries\Authentication;

use Chamilo\Core\Tracking\Storage\DataClass\Event;
use Chamilo\Libraries\Platform\Session\Session;

/**
 *
 * @package Chamilo\Libraries\Authentication
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
abstract class Authentication
{

    /**
     *
     * @return string[]
     */
    public static function getExternalAuthenticationTypes()
    {
        return array('Cas');
    }

    /**
     *
     * @return string[]
     */
    public static function getCredentialsAuthenticationTypes()
    {
        return array('Ldap', 'Platform');
    }

    /**
     *
     * @return string[]
     */
    public static function getQueryAuthenticationTypes()
    {
        return array('SecurityToken');
    }

    public static function anonymous_user_exists()
    {
        $anonymous_user = \Chamilo\Core\User\Storage\DataManager :: retrieve_anonymous_user();
        return $anonymous_user instanceof \Chamilo\Core\User\Storage\DataClass\User;
    }

    public static function as_anonymous_user()
    {
        if (self :: anonymous_user_exists())
        {
            $anonymous_user = \Chamilo\Core\User\Storage\DataManager :: retrieve_anonymous_user();
            Session :: register('_uid', $anonymous_user->get_id());
            return $anonymous_user;
        }
        else
        {
            return null;
        }
    }

    /**
     *
     * @param string $authenticationMethod
     * @return \Chamilo\Libraries\Authentication\Authentication
     */
    public static function factory($authenticationMethod)
    {
        $authenticationClass = __NAMESPACE__ . '\\' . $authenticationMethod . '\\' . $authenticationMethod .
             'Authentication';
        return new $authenticationClass();
    }

    public function logout($user)
    {
        $this->trackLogout($user);
        Session :: destroy();
    }

    public function trackLogout($user)
    {
        Event :: trigger(
            'Logout',
            \Chamilo\Core\User\Manager :: context(),
            array('server' => $_SERVER, 'user' => $user));
    }
}
