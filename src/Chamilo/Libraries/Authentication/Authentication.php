<?php
namespace Chamilo\Libraries\Authentication;

use Chamilo\Libraries\Platform\Configuration\PlatformSetting;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Session\Session;
use Chamilo\Libraries\Utilities\StringUtilities;

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

    public static function is_valid()
    {
        // $context = Kernel :: getInstance()->getContext();

        // $is_registration = $context == 'Chamilo\Core\User' && Request :: get(Application :: PARAM_ACTION) ==
        // 'register';
        // $is_login = $context == 'Chamilo\Core\User' && Request :: get(Application :: PARAM_ACTION) == 'login';
        // $is_invitation = $context == 'Chamilo\Core\User' && Request :: get(Application :: PARAM_ACTION) == 'inviter';
        // $is_password_reset = $context == 'Chamilo\Core\User' &&
        // Request :: get(Application :: PARAM_ACTION) == 'reset_password';
        // $is_online_page = $context == 'Chamilo\Core\Admin' &&
        // Request :: get(Application :: PARAM_ACTION) == 'whois_online';
        // $is_download_page = $context == 'Chamilo\Core\Repository' &&
        // Request :: get(Application :: PARAM_ACTION) == 'document_downloader';
        // $is_home_page = $context == 'Chamilo\Core\Home' && Request :: get(Application :: PARAM_ACTION) == null;
        // $is_upgrade = $context == 'Chamilo\Core\Lynx' && (Request :: get(Application :: PARAM_ACTION) == 'upgrader'
        // ||
        // Request :: get(Application :: PARAM_ACTION) == 'content_object_upgrader' ||
        // Request :: get(Application :: PARAM_ACTION) == 'application_upgrader');

        // $is_authentication_exception = $is_home_page || $is_registration || $is_login || $is_invitation ||
        // $is_password_reset || $is_online_page || $is_download_page || $is_upgrade;
        $allow_external_authentication = PlatformSetting :: get('enable_external_authentication');
        // if (($is_login || $is_invitation) && $allow_external_authentication)
        // {
        // return true;
        // }

        // if ($is_download_page || $is_upgrade)
        // {
        // return true;
        // }

        // TODO: Add system here to allow authentication via encrypted user key ?
        if (! Session :: get_user_id())
        {
            // Check whether external authentication is enabled
            $allow_external_authentication = PlatformSetting :: get('enable_external_authentication');

            $no_external_authentication = Request :: get('noExtAuth');
            if ($allow_external_authentication && ! isset($no_external_authentication))
            {
                $external_authentication_types = self :: get_external_authentication_types();

                foreach ($external_authentication_types as $type)
                {
                    $allow_authentication = PlatformSetting :: get('enable_' . $type . '_authentication');
                    $no_authentication = Request :: get(
                        'no' . StringUtilities :: getInstance()->createString($type)->upperCamelize() . 'Auth');

                    if ($allow_authentication)
                    {
                        $authentication = self :: factory($type);
                        if ($authentication->check_login())
                        {
                            if (PlatformSetting :: get('prevent_double_login', \Chamilo\Core\User\Manager :: context()))
                            {
                                \Chamilo\Core\User\Storage\DataClass\UserLoginSession :: check_single_login();
                            }
                            return true;
                        }
                    }
                }

                return false;
            }
            else
            {
                return false;
            }
        }
        else
        {
            if (PlatformSetting :: get('prevent_double_login', \Chamilo\Core\User\Manager :: context()))
            {
                \Chamilo\Core\User\Storage\DataClass\UserLoginSession :: check_single_login(false);
            }
            return true;
        }
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
        $types[] = 'security_token';
        $types[] = 'cas';
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
