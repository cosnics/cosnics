<?php
namespace Chamilo\Libraries\Authentication\Ldap;

use Chamilo\Libraries\Architecture\Interfaces\UserRegistrationSupport;
use Chamilo\Libraries\Authentication\Authentication;
use Chamilo\Libraries\Platform\Configuration\PlatformSetting;
use Chamilo\Libraries\Platform\Translation;

/**
 * $Id: ldap_authentication.class.php 128 2009-11-09 13:13:20Z vanpouckesven $
 *
 * @package common.authentication.ldap
 */
/**
 * This authentication class uses LDAP to authenticate users.
 * When you want to use LDAP, you might want to change this
 * implementation to match your institutions LDAP structure. You may consider to copy the ldap- directory to something
 * like myldap and to rename the class files. Then you can change your LDAP-implementation without changing this
 * default. Please note that the users in your database should have myldap as auth_source also in that case.
 */
class LdapAuthentication extends Authentication implements UserRegistrationSupport
{

    private $ldap_settings;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->get_configuration();
    }

    public function check_login($user, $username, $password = null)
    {
        if (! $this->is_configured())
        {
            throw new \Exception(Translation :: get('CheckLDAPConfiguration'));
        }
        else
        {
            $settings = $this->get_configuration();

            // include __DIR__.'/ldap_authentication_config.inc.php';
            $ldap_connect = ldap_connect($settings['host'], $settings['port']);
            if ($ldap_connect)
            {
                ldap_set_option($ldap_connect, LDAP_OPT_PROTOCOL_VERSION, 3);
                $filter = "(uid=$username)";
                $result = ldap_bind($ldap_connect, $settings['rdn'], $settings['password']);
                $search_result = ldap_search($ldap_connect, $settings['search_dn'], $filter);
                $info = ldap_get_entries($ldap_connect, $search_result);
                $dn = ($info[0]["dn"]);
                ldap_close($ldap_connect);
            }
            else
            {
                $this->set_message(Translation :: get("CouldNotConnectToLDAPServer"));
                return false;
            }

            if ($dn == '')
            {
                $this->set_message(Translation :: get("UserNotFoundInLDAP"));
                return false;
            }
            if ($password == '')
            {
                return false;
            }

            /*
             * disabled/locked account specific error messages This is for MS Active Directory, but if this field is not
             * present then this code will not do anything (conditions are false)
             */
            if ($info[0]['useraccountcontrol'][0] & 2) // account is disabled
            {
                $this->set_message(Translation :: get("AccountDisabled"));
                return false;
            }
            if ($info[0]['useraccountcontrol'][0] & 16) // account is locked out
            {
                $this->set_message(Translation :: get("AccountLocked"));
                return false;
            }

            $ldap_connect = ldap_connect($settings['host'], $settings['port']);
            ldap_set_option($ldap_connect, LDAP_OPT_PROTOCOL_VERSION, 3);
            if (! (@ldap_bind($ldap_connect, $dn, $password)) == true)
            {
                ldap_close($ldap_connect);
                $this->set_message(Translation :: get("UsernameOrPasswordIncorrect"));
                return false;
            }
            else
            {
                ldap_close($ldap_connect);
                return true;
            }
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

    public function get_password_requirements()
    {
        return null;
    }

    public function register_new_user($username, $password = null)
    {
        if ($this->check_login(null, $username, $password))
        {
            $settings = $this->get_configuration();

            include __DIR__ . '/ldap_parser.class.php';
            $ldap_connect = ldap_connect($settings['host'], $settings['port']);
            if ($ldap_connect)
            {
                ldap_set_option($ldap_connect, LDAP_OPT_PROTOCOL_VERSION, 3);
                $ldap_bind = ldap_bind($ldap_connect, $settings['rdn'], $settings['password']);
                $filter = "(uid=$username)";
                $search_result = ldap_search($ldap_connect, $settings['search_dn'], $filter);
                $info = ldap_get_entries($ldap_connect, $search_result);

                $parser = new LdapParser();
                return $parser->parse($info, $username);
            }
            ldap_close($ldap_connect);
        }
        return false;
    }

    public function get_configuration()
    {
        if (! isset($this->ldap_settings))
        {
            $ldap = array();
            $ldap['host'] = PlatformSetting :: get('ldap_host');
            $ldap['port'] = PlatformSetting :: get('ldap_port');
            $ldap['rdn'] = PlatformSetting :: get('ldap_remote_dn');
            $ldap['password'] = PlatformSetting :: get('ldap_password');
            $ldap['search_dn'] = PlatformSetting :: get('ldap_search_dn');

            $this->ldap_settings = $ldap;
        }

        return $this->ldap_settings;
    }
}
