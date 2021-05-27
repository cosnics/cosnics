<?php

namespace Chamilo\Libraries\Authentication\Ldap;

use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Authentication\Authentication;
use Chamilo\Libraries\Authentication\AuthenticationException;
use Chamilo\Libraries\Authentication\AuthenticationInterface;
use Chamilo\Libraries\File\Redirect;
use Exception;

/**
 * This authentication class uses LDAP to authenticate users.
 * When you want to use LDAP, you might want to change this
 * implementation to match your institutions LDAP structure. You may consider to copy the ldap- directory to something
 * like myldap and to rename the class files. Then you can change your LDAP-implementation without changing this
 * default. Please note that the users in your database should have myldap as auth_source also in that case.
 *
 * @package \Chamilo\Libraries\Authentication\Ldap
 */
class LdapAuthentication extends Authentication implements AuthenticationInterface
{
    /**
     *
     * @var string[]
     */
    private $ldapSettings;

    /**
     * Returns the short name of the authentication to check in the settings
     *
     * @return string
     */
    public function getAuthenticationType()
    {
        return __NAMESPACE__;
    }

    /**
     *
     * @return string[]
     */
    protected function getConfiguration()
    {
        if (!isset($this->ldapSettings))
        {
            $ldap = [];
            $ldap['host'] = $this->configurationConsulter->getSetting(array('Chamilo\Core\Admin', 'ldap_host'));
            $ldap['port'] = $this->configurationConsulter->getSetting(array('Chamilo\Core\Admin', 'ldap_port'));
            $ldap['rdn'] = $this->configurationConsulter->getSetting(array('Chamilo\Core\Admin', 'ldap_remote_dn'));
            $ldap['password'] = $this->configurationConsulter->getSetting(array('Chamilo\Core\Admin', 'ldap_password'));
            $ldap['search_dn'] = $this->configurationConsulter->getSetting(
                array('Chamilo\Core\Admin', 'ldap_search_dn')
            );

            $this->ldapSettings = $ldap;
        }

        return $this->ldapSettings;
    }

    /**
     * Returns the priority of the authentication, lower priorities come first
     *
     * @return int
     */
    public function getPriority()
    {
        return 100;
    }

    /**
     *
     * @return boolean
     */
    protected function isConfigured()
    {
        $settings = $this->getConfiguration();

        foreach ($settings as $setting => $value)
        {
            if (empty($value) || !isset($value))
            {
                return false;
            }
        }

        return true;
    }

    /**
     * @return \Chamilo\Core\User\Storage\DataClass\User
     *
     * @throws \Chamilo\Libraries\Authentication\AuthenticationException
     * @throws \Exception
     */
    public function login()
    {
        $user = $this->getUserFromCredentialsRequest();
        if (!$user)
        {
            return null;
        }

        if (!$this->isConfigured())
        {
            throw new Exception($this->translator->trans('CheckLDAPConfiguration', [], 'Chamilo\Libraries'));
        }

        $password = $this->request->getFromPost(self::PARAM_PASSWORD);

        $settings = $this->getConfiguration();

        $ldapConnect = ldap_connect($settings['host'], $settings['port']);

        if ($ldapConnect)
        {
            ldap_set_option($ldapConnect, LDAP_OPT_PROTOCOL_VERSION, 3);
            $filter = '(uid=' . $user->get_username() . ')';

            $result = ldap_bind($ldapConnect, $settings['rdn'], $settings['password']);
            $search_result = ldap_search($ldapConnect, $settings['search_dn'], $filter);
            $info = ldap_get_entries($ldapConnect, $search_result);

            $dn = ($info[0]["dn"]);

            ldap_close($ldapConnect);
        }
        else
        {
            throw new AuthenticationException(
                $this->translator->trans('CouldNotConnectToLDAPServer', [], 'Chamilo\Libraries')
            );
        }

        if (!$dn)
        {
            throw new AuthenticationException($this->translator->trans('UserNotFoundInLDAP', [], 'Chamilo\Libraries'));
        }

        if (!$password)
        {
            throw new AuthenticationException(
                $this->translator->trans('UsernameOrPasswordIncorrect', [], 'Chamilo\Libraries')
            );
        }

        /*
         * disabled/locked account specific error messages This is for MS Active Directory, but if this field is not
         * present then this code will not do anything (conditions are false)
         */
        if ($info[0]['useraccountcontrol'][0] & 2) // account is disabled
        {
            throw new AuthenticationException($this->translator->trans('AccountDisabled', [], 'Chamilo\Libraries'));
        }

        if ($info[0]['useraccountcontrol'][0] & 16) // account is locked out
        {
            throw new AuthenticationException($this->translator->trans('AccountLocked', [], 'Chamilo\Libraries'));
        }

        $ldapConnect = ldap_connect($settings['host'], $settings['port']);
        ldap_set_option($ldapConnect, LDAP_OPT_PROTOCOL_VERSION, 3);

        if (!(@ldap_bind($ldapConnect, $dn, $password)) == true)
        {
            ldap_close($ldapConnect);

            throw new AuthenticationException(
                $this->translator->trans('UsernameOrPasswordIncorrect', [], 'Chamilo\Libraries')
            );
        }
        else
        {
            ldap_close($ldapConnect);

            return $user;
        }
    }

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     */
    public function logout(User $user)
    {
        $redirect = new Redirect([], array(Application::PARAM_ACTION, Application::PARAM_CONTEXT));
        $redirect->toUrl();
        exit();
    }
}
