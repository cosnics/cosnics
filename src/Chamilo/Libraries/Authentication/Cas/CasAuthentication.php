<?php
namespace Chamilo\Libraries\Authentication\Cas;

use Chamilo\Configuration\Service\ConfigurationConsulter;
use Chamilo\Core\User\Service\UserService;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Authentication\Authentication;
use Chamilo\Libraries\Authentication\AuthenticationException;
use Chamilo\Libraries\Authentication\AuthenticationInterface;
use Chamilo\Libraries\Platform\ChamiloRequest;
use Chamilo\Libraries\Platform\Session\SessionUtilities;
use Exception;
use phpCAS;
use Symfony\Component\Translation\Translator;

/**
 *
 * @package Chamilo\Libraries\Authentication\Cas
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class CasAuthentication extends Authentication implements AuthenticationInterface
{
    /**
     * @var string[]
     */
    protected $settings;

    /**
     * @var \Chamilo\Libraries\Platform\Session\SessionUtilities
     */
    protected $sessionUtilities;

    /**
     * @param \Chamilo\Configuration\Service\ConfigurationConsulter $configurationConsulter
     * @param \Symfony\Component\Translation\Translator $translator
     * @param \Chamilo\Libraries\Platform\ChamiloRequest $request
     * @param \Chamilo\Core\User\Service\UserService $userService
     * @param \Chamilo\Libraries\Platform\Session\SessionUtilities $sessionUtilities
     *
     * @throws \Exception
     */
    public function __construct(
        ConfigurationConsulter $configurationConsulter, Translator $translator, ChamiloRequest $request,
        UserService $userService, SessionUtilities $sessionUtilities
    )
    {
        parent::__construct($configurationConsulter, $translator, $request, $userService);
        $this->sessionUtilities = $sessionUtilities;

        $this->initializeClient();
    }

    /**
     * Returns the short name of the authentication to check in the settings
     *
     * @return string
     */
    public function getAuthenticationType()
    {
        return 'Cas';
    }

    /**
     * @return string[]
     */
    protected function getConfiguration()
    {
        if (!isset($this->settings))
        {
            $this->settings = array();
            $this->settings['host'] =
                $this->configurationConsulter->getSetting(array('Chamilo\Core\Admin', 'cas_host'));
            $this->settings['port'] =
                $this->configurationConsulter->getSetting(array('Chamilo\Core\Admin', 'cas_port'));
            $this->settings['uri'] = $this->configurationConsulter->getSetting(array('Chamilo\Core\Admin', 'cas_uri'));
            $this->settings['certificate'] = $this->configurationConsulter->getSetting(
                array('Chamilo\Core\Admin', 'cas_certificate')
            );
            $this->settings['log'] = $this->configurationConsulter->getSetting(array('Chamilo\Core\Admin', 'cas_log'));
            $this->settings['enable_log'] = $this->configurationConsulter->getSetting(
                array('Chamilo\Core\Admin', 'cas_enable_log')
            );
        }

        return $this->settings;
    }

    /**
     * Returns the priority of the authentication, lower priorities come first
     *
     * @return int
     */
    public function getPriority()
    {
        return 500;
    }

    /**
     * @return \Chamilo\Libraries\Platform\Session\SessionUtilities
     */
    public function getSessionUtilities(): SessionUtilities
    {
        return $this->sessionUtilities;
    }

    /**
     * @param \Chamilo\Libraries\Platform\Session\SessionUtilities $sessionUtilities
     */
    public function setSessionUtilities(SessionUtilities $sessionUtilities): void
    {
        $this->sessionUtilities = $sessionUtilities;
    }

    /**
     * @throws \Exception
     */
    protected function initializeClient()
    {
        if (!$this->isConfigured())
        {
            throw new Exception($this->getTranslator()->trans('CheckCASConfiguration'));
        }
        else
        {
            $settings = $this->getConfiguration();

            // initialize phpCAS
            if ($settings['enable_log'])
            {
                phpCAS::setDebug($settings['log']);
            }

            $casVersion = $this->configurationConsulter->getSetting(array('Chamilo\Core\Admin', 'cas_version'));

            if ($casVersion == 'SAML_VERSION_1_1')
            {
                phpCAS::client(
                    SAML_VERSION_1_1, $settings['host'], (int) $settings['port'], (string) $settings['uri'], false
                );
            }
            else
            {
                phpCAS::client(
                    CAS_VERSION_2_0, $settings['host'], (int) $settings['port'], (string) $settings['uri'], false
                );
            }

            $casCheckCertificate = $this->configurationConsulter->getSetting(
                array('Chamilo\Core\Admin', 'cas_check_certificate')
            );

            // SSL validation for the CAS server
            if ($casCheckCertificate == '1')
            {
                phpCAS::setCasServerCACert($settings['certificate']);
            }
            else
            {
                phpCAS::setNoCasServerValidation();
            }
        }
    }

    /**
     * @return boolean
     */
    protected function isConfigured()
    {
        $settings = $this->getConfiguration();

        foreach ($settings as $setting => $value)
        {
            if ((empty($value) || !isset($value)) && !in_array(
                    $setting, array('uri', 'certificate', 'log', 'enable_log')
                ))
            {
                return false;
            }
        }

        return true;
    }

    /**
     * @return \Chamilo\Core\User\Storage\DataClass\User|null
     * @throws \Chamilo\Libraries\Authentication\AuthenticationException
     */
    public function login()
    {
        if (!$this->isAuthSourceActive())
        {
            return null;
        }

        $externalAuthenticationEnabled = $this->configurationConsulter->getSetting(
            array('Chamilo\Core\Admin', 'enableExternalAuthentication')
        );

        $bypassExternalAuthentication = (boolean) $this->request->getFromUrl('noExtAuth', false);

        if (!$externalAuthenticationEnabled || $bypassExternalAuthentication)
        {
            return null;
        }

        phpCAS::forceAuthentication();

        $userAttributes = phpCAS::getAttributes();
        $userName = phpCAS::getUser();

        if ($userName)
        {
            $user = $this->userService->findUserByUsername($userName);

            if (!$user instanceof User)
            {
                $user = $this->registerUser();
            }

            if ($userAttributes && isset($userAttributes['surrogatePrincipal']))
            {
                $surrogateUserName = array_pop($userAttributes['surrogatePrincipal']);
                $surrogateUser = $this->userService->findUserByUsername($surrogateUserName);
                $this->getSessionUtilities()->register('_as_admin', $surrogateUser->getId());
            }

            return $user;
        }
        else
        {
            throw new AuthenticationException(
                $this->translator->trans(
                    'CasAuthenticationError', array(
                    'PLATFORM' => $this->configurationConsulter->getSetting(
                        array('Chamilo\Core\Admin', 'site_name')
                    )
                ), 'Chamilo\Libraries'
                )
            );
        }
    }

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     */
    public function logout(User $user)
    {
        phpCAS::logout();
    }

    /**
     *
     * @return \Chamilo\Core\User\Storage\DataClass\User
     * @throws \Chamilo\Libraries\Authentication\AuthenticationException
     * @throws \Exception
     */
    protected function registerUser()
    {
        $userName = phpCAS::getUser();
        $userAttributes = phpCAS::getAttributes();

        $user = new User();

        $user->set_username($userName);
        $user->set_password('PLACEHOLDER');
        $user->set_status(User::STATUS_STUDENT);
        $user->set_auth_source('Cas');
        $user->set_platformadmin(0);
        $user->set_email($userAttributes['email']);
        $user->set_lastname($userAttributes['last_name']);
        $user->set_firstname($userAttributes['first_name']);
        $user->set_official_code($userAttributes['person_number']);

        if (!$user->create())
        {
            throw new AuthenticationException('CasUserRegistrationFailed');
        }
        else
        {
            return $user;
        }
    }
}
