<?php
namespace Chamilo\Libraries\Authentication;

use Chamilo\Libraries\Platform\Session\Session;

/**
 *
 * @package common.libraries.authentication
 */
/**
 * An abstract class for handling authentication.
 * Impement new authentication methods by creating a class which extends
 * this abstract class.
 */
abstract class Authentication
{

    /**
     * Stores the (error) message for the login procedure
     *
     * @var String
     */
    private $message;

    /**
     * Returns the error message
     *
     * @return String
     */
    public function get_message()
    {
        return $this->message;
    }

    /**
     * Sets the message for the login procedure
     *
     * @param String $message
     */
    public function set_message($message)
    {
        $this->message = $message;
    }

    /**
     * Checks if the given username and password are valid
     *
     * @param string $username
     * @param string $password
     * @return true
     */
    abstract public function check_login($user, $username, $password = null);

    /**
     * Changes the user's password
     *
     * @param \core\user\storage\data_class\User The current user object
     * @param string The user's current password
     * @param string The desired new password
     * @return boolean True if changed, false if not
     */
    abstract public function change_password($user, $old_password, $new_password);

    /**
     * Get any and every kind of password requirements for the authentication method
     *
     * @return string Instructions for the password
     */
    abstract public function get_password_requirements();

    /**
     * Registers a new user
     *
     * @param string $username
     * @param string $password
     * @return boolean True on success, false if not
     */
    public function register_new_user($username, $password = null)
    {
        return false;
    }

    /**
     * Logs the current user out of the platform.
     * The different authentication methods can overwrite this function if
     * additional operations are needed before a user can be logged out.
     *
     * @param \core\user\storage\data_class\User $user The user which is logging out
     */
    public function logout($user)
    {
        Session :: destroy();
    }

    /**
     * Creates an instance of an authentication class
     *
     * @param string $authentication_method
     * @return Authentication An object of a class implementing this abstract class.
     */
    public function factory($authentication_method)
    {
        $authentication_class = __NAMESPACE__ . '\\' . $authentication_method . '\\' . $authentication_method .
             'Authentication';
        return new $authentication_class();
    }

    public static function get_external_authentication_types()
    {
        $types = array();
        $types[] = 'SecurityToken';
        $types[] = 'Cas';
        return $types;
    }

    public static function getExternalTypes()
    {
        return array('SecurityToken', 'Cas');
    }

    public static function get_internal_authentication_types()
    {
        $types = array();
        $types[] = 'Ldap';
        $types[] = 'Platform';
        return $types;
    }

    public static function is_valid_authentication_type($type)
    {
        return in_array($type, self :: get_external_authentication_types()) ||
             in_array($type, self :: get_internal_authentication_types());
    }

    public function get_configuration()
    {
        return array();
    }

    public function is_configured()
    {
        $settings = $this->get_configuration();

        foreach ($settings as $setting => $value)
        {
            if (empty($value) || ! isset($value))
            {
                return false;
            }
        }

        return true;
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
}
