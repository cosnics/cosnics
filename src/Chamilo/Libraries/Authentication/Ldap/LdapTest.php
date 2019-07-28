<?php

namespace Chamilo\Libraries\Authentication\Ldap;

/**
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class LdapTest
{
    use \Chamilo\Libraries\Architecture\Traits\DependencyInjectionContainerTrait;

    /**
     * @var \Chamilo\Configuration\Service\ConfigurationConsulter
     */
    protected $configurationConsulter;

    /**
     * LdapTest constructor.
     */
    public function __construct()
    {
        $this->initializeContainer();
        $this->configurationConsulter = $this->getConfigurationConsulter();
    }

    public function run()
    {
        // Timeout after 30 seconds
        set_time_limit(30);

        $settings = $this->getConfiguration();

        echo '<p>Connecting to LDAP server</p>';
        flush();
        $ldapConnect = ldap_connect($settings['host'], $settings['port']);

        if ($ldapConnect)
        {
            ldap_set_option($ldapConnect, LDAP_OPT_NETWORK_TIMEOUT, 10);
            ldap_set_option($ldapConnect, LDAP_OPT_TIMELIMIT, 10);
            echo '<p>Connected to LDAP server</p>';
            flush();

            $username = $this->getRequest()->getFromUrl('username');
            $password = $this->getRequest()->getFromUrl('password');

            ldap_set_option($ldapConnect, LDAP_OPT_PROTOCOL_VERSION, 3);

            echo '<p>Protocol option set</p>';
            flush();

            $filter = '(uid=' . $username . ')';

            $success = ldap_bind($ldapConnect, $settings['rdn'], $settings['password']);

            echo '<p>Bound to LDAP with credentials</p>';
            var_dump($success);
            flush();

            $search_result = ldap_search($ldapConnect, $settings['search_dn'], $filter);

            echo '<p>Search for username based on UID filter</p>';
            flush();

            $info = ldap_get_entries($ldapConnect, $search_result);

            echo '<p>Finding entries with the search result</p>';
            flush();

            var_dump($info);

            $dn = ($info[0]["dn"]);

            echo '<p>First Entry</p>';
            flush();

            var_dump($dn);

            ldap_close($ldapConnect);

            $ldapConnect = ldap_connect($settings['host'], $settings['port']);
            ldap_set_option($ldapConnect, LDAP_OPT_PROTOCOL_VERSION, 3);
            ldap_set_option($ldapConnect, LDAP_OPT_NETWORK_TIMEOUT, 10);
            ldap_set_option($ldapConnect, LDAP_OPT_TIMELIMIT, 10);

            $result = @ldap_bind($ldapConnect, $dn, $password);
            echo '<p>Result of logging in</p>';
            flush();

            var_dump($result);

            ldap_close($ldapConnect);

        }
    }

    /**
     *
     * @return string[]
     */
    protected function getConfiguration()
    {
        if (!isset($this->ldapSettings))
        {
            $ldap = array();
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
}