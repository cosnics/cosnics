<?php
namespace Chamilo\Libraries\Authentication\Ldap;

use Chamilo\Configuration\Configuration;
use Chamilo\Libraries\Authentication\AuthenticationException;
use Chamilo\Libraries\Authentication\CredentialsAuthentication;
use Chamilo\Libraries\Platform\Translation;

/**
 * This authentication class uses LDAP to authenticate users.
 * When you want to use LDAP, you might want to change this
 * implementation to match your institutions LDAP structure. You may consider to copy the ldap- directory to something
 * like myldap and to rename the class files. Then you can change your LDAP-implementation without changing this
 * default. Please note that the users in your database should have myldap as auth_source also in that case.
 *
 * @package \Chamilo\Libraries\Authentication\Ldap
 */
class LdapAuthentication extends CredentialsAuthentication
{

    /**
     *
     * @var string[]
     */
    private $ldapSettings;

    /**
     *
     * @see \Chamilo\Libraries\Authentication\CredentialsAuthentication::login()
     */
    public function login($password)
    {
        if (! $this->isConfigured())
        {
            throw new \Exception(Translation::get('CheckLDAPConfiguration'));
        }
        else
        {
            $settings = $this->getConfiguration();

            $ldapConnect = ldap_connect($settings['host'], $settings['port']);

            if ($ldapConnect)
            {
                ldap_set_option($ldapConnect, LDAP_OPT_PROTOCOL_VERSION, 3);
                $filter = '(uid=' . $this->getUserName() . ')';

                $result = ldap_bind($ldapConnect, $settings['rdn'], $settings['password']);
                $search_result = ldap_search($ldapConnect, $settings['search_dn'], $filter);
                $info = ldap_get_entries($ldapConnect, $search_result);

                $dn = ($info[0]["dn"]);

                ldap_close($ldapConnect);
            }
            else
            {
                throw new AuthenticationException(Translation::get('CouldNotConnectToLDAPServer'));
            }

            if (! $dn)
            {
                throw new AuthenticationException(Translation::get('UserNotFoundInLDAP'));
            }

            if (! $password)
            {
                throw new AuthenticationException(Translation::get('UsernameOrPasswordIncorrect'));
            }

            /*
             * disabled/locked account specific error messages This is for MS Active Directory, but if this field is not
             * present then this code will not do anything (conditions are false)
             */
            if ($info[0]['useraccountcontrol'][0] & 2) // account is disabled
            {
                throw new AuthenticationException(Translation::get('AccountDisabled'));
            }

            if ($info[0]['useraccountcontrol'][0] & 16) // account is locked out
            {
                throw new AuthenticationException(Translation::get('AccountLocked'));
            }

            $ldapConnect = ldap_connect($settings['host'], $settings['port']);
            ldap_set_option($ldapConnect, LDAP_OPT_PROTOCOL_VERSION, 3);

            if (! (@ldap_bind($ldapConnect, $dn, $password)) == true)
            {
                ldap_close($ldapConnect);

                throw new AuthenticationException(Translation::get('UsernameOrPasswordIncorrect'));
            }
            else
            {
                ldap_close($ldapConnect);
                return true;
            }
        }
    }

    /**
     * Unused for now class should implement UserRegistrationSupport to support this
     *
     * @see \Chamilo\Libraries\Architecture\Interfaces\UserRegistrationSupport::registerUser()
     * @return \Chamilo\Core\User\Storage\DataClass\User
     */
    public function registerUser()
    {
        $settings = $this->getConfiguration();

        $ldapConnect = ldap_connect($settings['host'], $settings['port']);

        if ($ldapConnect)
        {
            ldap_set_option($ldapConnect, LDAP_OPT_PROTOCOL_VERSION, 3);
            $ldapBind = ldap_bind($ldapConnect, $settings['rdn'], $settings['password']);
            $filter = '(uid=' . $this->getUserName() . ')';
            $search_result = ldap_search($ldapConnect, $settings['search_dn'], $filter);
            $info = ldap_get_entries($ldapConnect, $search_result);

            $parser = new LdapParser();
            return $parser->parse($info, $this->getUserName());
        }

        ldap_close($ldapConnect);
    }

    /**
     *
     * @return boolean
     */
    public function isConfigured()
    {
        $settings = $this->getConfiguration();

        foreach ($settings as $setting => $value)
        {
            if (empty($value) || ! isset($value))
            {
                return false;
            }
        }

        return true;
    }

    /**
     *
     * @return string[]
     */
    public function getConfiguration()
    {
        if (! isset($this->ldapSettings))
        {
            $ldap = array();
            $ldap['host'] = Configuration::getInstance()->get_setting(array('Chamilo\Core\Admin', 'ldap_host'));
            $ldap['port'] = Configuration::getInstance()->get_setting(array('Chamilo\Core\Admin', 'ldap_port'));
            $ldap['rdn'] = Configuration::getInstance()->get_setting(array('Chamilo\Core\Admin', 'ldap_remote_dn'));
            $ldap['password'] = Configuration::getInstance()->get_setting(array('Chamilo\Core\Admin', 'ldap_password'));
            $ldap['search_dn'] = Configuration::getInstance()->get_setting(
                array('Chamilo\Core\Admin', 'ldap_search_dn'));

            $this->ldapSettings = $ldap;
        }

        return $this->ldapSettings;
    }
}
