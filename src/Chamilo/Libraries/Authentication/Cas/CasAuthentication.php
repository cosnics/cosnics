<?php
namespace Chamilo\Libraries\Authentication\Cas;

use Chamilo\Core\Tracking\Storage\DataClass\Event;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Interfaces\UserRegistrationSupport;
use Chamilo\Libraries\Authentication\Authentication;
use Chamilo\Libraries\Format\Display;
use Chamilo\Libraries\Platform\Configuration\PlatformSetting;
use Chamilo\Libraries\Platform\Session\Session;
use Chamilo\Libraries\Platform\Translation;
use phpCAS;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\File\Redirect;

/**
 * $Id: cas_authentication.class.php 166 2009-11-12 11:03:06Z vanpouckesven $
 *
 * @package common.authentication.cas
 */

/*
 * Extension enabling the CAS-authentication system. The official phpCAS client is used for communication with the CAS
 * server. More info on CAS and phpCAS can be found at: - http://www.jasig.org/cas -
 * http://www.jasig.org/wiki/display/CASC/phpCAS Requirements - SSL-connection to the CAS-server - Provide this class
 * with a link to your CAS certificate for server verification If you use attributes, the following have to be
 * available: - email - last_name - first_name Available settings: - host (Address of the host, e.g.
 * http://www.mycompany.com) - port (Port CAS is running on, e.g. typically 443) - uri (Possible subfolder CAS is
 * located in) - certificate (Path of CAS' certificate file) - enable_log (Whether or not to enable the log) - log (Path
 * of the log file)
 */
class CasAuthentication extends Authentication implements UserRegistrationSupport
{

    private $cas_settings;

    private static $has_already_been_called = false;

    public function check_login($user, $username, $password = null)
    {
        if (! self :: $has_already_been_called)
        {
            self :: initialize_cas_client();
        }

        $user_attributes = phpCAS :: getAttributes();
        if (is_numeric($user_attributes['person_id']) || strpos($user_attributes['person_id'], 'EXT') !== false)
        {
            $user = \Chamilo\Core\User\Storage\DataManager :: retrieve_user_by_official_code(
                $user_attributes['person_id']);

            if (! $user instanceof User)
            {
                $user = $this->register_new_user();
            }

            if ($user instanceof User)
            {
                Session :: register('_uid', $user->get_id());
                Event :: trigger(
                    'login',
                    \Chamilo\Core\User\Manager :: context(),
                    array('server' => $_SERVER, 'user' => $user));

                $redirect = new Redirect(
                    array(Application :: PARAM_CONTEXT => PlatformSetting :: get('page_after_login')));
                $redirect->toUrl();

                return true;
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

    /**
     * Always returns false as the user's password is not stored in the Chamilo datasource.
     *
     * @return bool false
     */
    public function change_password($user, $old_password, $new_password)
    {
        return false;
    }

    /**
     * Determine the authentication handler type used by CAS for this user.
     * WARNING: Determination of the authentication
     * handler depends on your specific configuration. This might be achieved by analysing the username, email address
     * or a specific attribute made available for this purpose.
     *
     * @param $user User
     * @return String The type of authentication handler
     */
    public function determine_cas_password_type()
    {
        /**
         * Use this in case the type is determined via a CAS attribute.
         * Change the user attributes key to whatever is
         * defined in your CAS setup.
         */
        if (! self :: $has_already_been_called)
        {
            self :: initialize_cas_client();
        }

        $user_attributes = phpCAS :: getAttributes();
        $authentication_type = $user_attributes['authentication_type'];

        if (! isset($authentication_type))
        {
            return 'default';
        }
        else
        {
            return $authentication_type;
        }
    }

    public function register_new_user($user_id, $password = null)
    {
        if (! self :: $has_already_been_called)
        {
            self :: initialize_cas_client();
        }

        $user_attributes = phpCAS :: getAttributes();

        $user = new User();
        $user->set_username($user_attributes['email']);
        $user->set_password('PLACEHOLDER');
        $user->set_status(5);
        $user->set_auth_source('Cas');
        $user->set_platformadmin(0);
        // $user->set_language('english');
        $user->set_email($user_attributes['email']);
        $user->set_lastname($user_attributes['last_name']);
        $user->set_firstname($user_attributes['first_name']);
        $user->set_official_code($user_attributes['person_id']);

        if (! $user->create())
        {
            return false;
        }
        else
        {
            return $user;
        }
    }

    public function logout($user)
    {
        if (! $this->is_configured())
        {
            throw new \Exception(Translation :: get('CheckCASConfiguration'));
        }
        else
        {
            if (! self :: $has_already_been_called)
            {
                self :: initialize_cas_client();
            }

            // Do the logout
            phpCAS :: logout();

            Session :: destroy();
        }
    }

    public function get_configuration()
    {
        if (! isset($this->cas_settings))
        {
            $cas = array();
            $cas['host'] = PlatformSetting :: get('cas_host');
            $cas['port'] = PlatformSetting :: get('cas_port');
            $cas['uri'] = PlatformSetting :: get('cas_uri');
            $cas['certificate'] = PlatformSetting :: get('cas_certificate');
            $cas['log'] = PlatformSetting :: get('cas_log');
            $cas['enable_log'] = PlatformSetting :: get('cas_enable_log');
            // $cas['allow_change_password'] = PlatformSetting :: get('cas_allow_change_password');

            $this->cas_settings = $cas;
        }

        return $this->cas_settings;
    }

    public function is_configured()
    {
        $settings = $this->get_configuration();

        foreach ($settings as $setting => $value)
        {
            if ((empty($value) || ! isset($value)) && ! in_array(
                $setting,
                array('uri', 'certificate', 'log', 'enable_log')))
            {
                return false;
            }
        }

        return true;
    }

    public function initialize_cas_client()
    {
        if (! $this->is_configured())
        {
            Display :: error_message(Translation :: get('CheckCASConfiguration'));

            // exit();
        }
        else
        {
            $settings = $this->get_configuration();

            // initialize phpCAS
            if ($settings['enable_log'])
            {
                phpCAS :: setDebug($settings['log']);
            }

            $uri = ($settings['uri'] ? $settings['uri'] : '');

            phpCAS :: client(
                SAML_VERSION_1_1,
                $settings['host'],
                (int) $settings['port'],
                (string) $settings['uri'],
                false);

            self :: $has_already_been_called = true;

            // SSL validation for the CAS server
            $crt_path = $settings['certificate'];
            phpCAS :: setExtraCurlOption(CURLOPT_SSLVERSION, 3);
            phpCAS :: setCasServerCACert($crt_path);
            // phpCAS :: setNoCasServerValidation();

            // force CAS authentication
            phpCAS :: forceAuthentication();
        }
    }

    public function get_password_requirements()
    {
        return null;
    }
}
