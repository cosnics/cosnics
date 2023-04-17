<?php

namespace Chamilo\Libraries\Authentication\Ldap;

use Chamilo\Configuration\Service\Consulter\ConfigurationConsulter;
use Chamilo\Core\User\Service\UserService;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Application\Routing\UrlGenerator;
use Chamilo\Libraries\Authentication\Authentication;
use Chamilo\Libraries\Authentication\AuthenticationException;
use Chamilo\Libraries\Authentication\AuthenticationInterface;
use Chamilo\Libraries\Platform\ChamiloRequest;
use Exception;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Translation\Translator;

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
    protected UrlGenerator $urlGenerator;

    /**
     * @var string[]
     */
    private array $ldapSettings;

    public function __construct(
        ConfigurationConsulter $configurationConsulter, Translator $translator, ChamiloRequest $request,
        UserService $userService, UrlGenerator $urlGenerator
    )
    {
        parent::__construct($configurationConsulter, $translator, $request, $userService);
        $this->urlGenerator = $urlGenerator;
    }

    public function getAuthenticationType(): string
    {
        return __NAMESPACE__;
    }

    /**
     * @return string[]
     */
    protected function getConfiguration(): array
    {
        if (!isset($this->ldapSettings))
        {
            $ldap = [];
            $ldap['host'] = $this->configurationConsulter->getSetting(['Chamilo\Core\Admin', 'ldap_host']);
            $ldap['port'] = $this->configurationConsulter->getSetting(['Chamilo\Core\Admin', 'ldap_port']);
            $ldap['rdn'] = $this->configurationConsulter->getSetting(['Chamilo\Core\Admin', 'ldap_remote_dn']);
            $ldap['password'] = $this->configurationConsulter->getSetting(['Chamilo\Core\Admin', 'ldap_password']);
            $ldap['search_dn'] = $this->configurationConsulter->getSetting(
                ['Chamilo\Core\Admin', 'ldap_search_dn']
            );

            $this->ldapSettings = $ldap;
        }

        return $this->ldapSettings;
    }

    public function getPriority(): int
    {
        return 100;
    }

    protected function isConfigured(): bool
    {
        $settings = $this->getConfiguration();

        foreach ($settings as $value)
        {
            if (empty($value))
            {
                return false;
            }
        }

        return true;
    }

    /**
     * @throws \Chamilo\Libraries\Authentication\AuthenticationException
     * @throws \Exception
     */
    public function login(): ?User
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

            ldap_bind($ldapConnect, $settings['rdn'], $settings['password']);
            $search_result = ldap_search($ldapConnect, $settings['search_dn'], $filter);
            $info = ldap_get_entries($ldapConnect, $search_result);

            $dn = ($info[0]['dn']);

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

        if (!(ldap_bind($ldapConnect, $dn, $password)))
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

    public function logout(User $user)
    {
        $redirect = new RedirectResponse(
            $this->urlGenerator->fromParameters([], [Application::PARAM_ACTION, Application::PARAM_CONTEXT])
        );

        $redirect->send();
        exit;
    }
}
