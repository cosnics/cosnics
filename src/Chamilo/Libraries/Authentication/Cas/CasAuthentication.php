<?php

namespace Chamilo\Libraries\Authentication\Cas;

use Chamilo\Configuration\Service\ConfigurationConsulter;
use Chamilo\Core\Tracking\Storage\DataClass\Event;
use Chamilo\Core\User\Service\UserService;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Authentication\AuthenticationException;
use Chamilo\Libraries\Authentication\AuthenticationInterface;
use Chamilo\Libraries\Translation\Translation;
use phpCAS;
use Symfony\Component\Translation\Translator;

/**
 *
 * @package Chamilo\Libraries\Authentication\Cas
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class CasAuthentication implements AuthenticationInterface
{
    /**
     * @var string[]
     */
    protected $settings;

    /**
     * @var boolean
     */
    protected $hasBeenInitialized = false;

    /**
     * @var \Chamilo\Configuration\Service\ConfigurationConsulter
     */
    protected $configurationConsulter;

    /**
     * @var \Chamilo\Core\User\Service\UserService
     */
    protected $userService;

    /**
     * @var \Symfony\Component\Translation\Translator
     */
    protected $translator;

    /**
     * CasAuthentication constructor.
     *
     * @param \Chamilo\Configuration\Service\ConfigurationConsulter $configurationConsulter
     * @param \Chamilo\Core\User\Service\UserService $userService
     * @param \Symfony\Component\Translation\Translator $translator
     */
    public function __construct(
        ConfigurationConsulter $configurationConsulter, UserService $userService, Translator $translator
    )
    {
        $this->configurationConsulter = $configurationConsulter;
        $this->userService = $userService;
        $this->translator = $translator;
    }

    /**
     *
     * @see \Chamilo\Libraries\Authentication\ExternalAuthentication::login()
     */
    public function login()
    {
        if (!$this->hasBeenInitialized)
        {
            $this->initializeClient();
        }

        $userAttributes = phpCAS::getAttributes();

        $casUserLogin = $this->configurationConsulter->getSetting(array('Chamilo\Core\Admin', 'cas_user_login'));
        $casValidationString = $this->configurationConsulter->getSetting(
            array('Chamilo\Core\Admin', 'cas_validation_string')
        );

        if ($casUserLogin == 'email')
        {
            if ((is_numeric($userAttributes['person_number']) && $userAttributes['person_number'] > 0) ||
                strpos($userAttributes['person_number'], $casValidationString) !== false)

            {
                $user = $this->userService->getUserBySecurityToken(
                    $userAttributes['person_number']
                );

                if (!$user instanceof User)
                {
                    $user = $this->registerUser();
                }

                return $user;
            }
            elseif (is_numeric($userAttributes['person_number']) && $userAttributes['person_number'] == - 1)
            {
                $user = $this->userService->findUserByUsername($userAttributes['email']);

                if (!$user instanceof User)
                {
                    $user = $this->registerUser();
                }

                return $user;
            }
            else
            {
                throw new AuthenticationException(
                    $this->translator->trans(
                        'CasAuthenticationError',
                        array(
                            'PLATFORM' => $this->configurationConsulter->getSetting(
                                array('Chamilo\Core\Admin', 'site_name')
                            )
                        ),
                        'Chamilo\Libraries'
                    )
                );
            }
        }
        else
        {
            if (strpos($userAttributes['email'], $casValidationString) !== false)
            {
                $user = $this->userService->findUserByUsername($userAttributes['login']);

                if (!$user instanceof User)
                {
                    $user = $this->registerUser();
                }

                return $user;
            }
            else
            {
                throw new AuthenticationException(
                    $this->translator->trans(
                        'CasAuthenticationError',
                        array(
                            'PLATFORM' => $this->configurationConsulter->getSetting(
                                array('Chamilo\Core\Admin', 'site_name')
                            )
                        ),
                        'Chamilo\Libraries'
                    )
                );
            }
        }
    }

    /**
     *
     * @throws \Chamilo\Libraries\Authentication\AuthenticationException
     * @return \Chamilo\Core\User\Storage\DataClass\User
     * @throws \Exception
     */
    protected function registerUser()
    {
        if (!$this->hasBeenInitialized)
        {
            $this->initializeClient();
        }

        $userAttributes = phpCAS::getAttributes();

        $user = new User();

        $casUserLogin = $this->configurationConsulter->getSetting(array('Chamilo\Core\Admin', 'cas_user_login'));

        if ($casUserLogin === 'login')
        {
            $user->set_username($userAttributes['login']);
        }
        else
        {
            $user->set_username($userAttributes['email']);
        }

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

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @throws \Chamilo\Libraries\Authentication\AuthenticationException
     * @throws \Exception
     */
    public function logout(User $user)
    {
        if (!$this->isConfigured())
        {
            throw new AuthenticationException(Translation::get('CheckCASConfiguration'));
        }
        else
        {
            Event::trigger(
                'Logout', \Chamilo\Core\User\Manager::context(), array('server' => $_SERVER, 'user' => $user)
            );

            if (!$this->hasBeenInitialized)
            {
                $this->initializeClient();
            }

            // Do the logout
            phpCAS::logout();
        }
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
     * @return boolean
     */
    protected function isConfigured()
    {
        $settings = $this->getConfiguration();

        foreach ($settings as $setting => $value)
        {
            if ((empty($value) || !isset($value)) && !in_array(
                    $setting,
                    array('uri', 'certificate', 'log', 'enable_log')
                ))
            {
                return false;
            }
        }

        return true;
    }

    /**
     * @throws \Exception
     */
    protected function initializeClient()
    {
        if (!$this->isConfigured())
        {
            throw new \Exception(Translation::get('CheckCASConfiguration'));
        }
        else
        {
            try
            {
                $settings = $this->getConfiguration();

                // initialize phpCAS
                if ($settings['enable_log'])
                {
                    phpCAS::setDebug($settings['log']);
                }

                $uri = ($settings['uri'] ? $settings['uri'] : '');

                $casVersion = $this->configurationConsulter->getSetting(array('Chamilo\Core\Admin', 'cas_version'));

                if ($casVersion == 'SAML_VERSION_1_1')
                {
                    phpCAS::client(
                        SAML_VERSION_1_1,
                        $settings['host'],
                        (int) $settings['port'],
                        (string) $settings['uri'],
                        false
                    );
                }
                else
                {
                    phpCAS::client(
                        CAS_VERSION_2_0,
                        $settings['host'],
                        (int) $settings['port'],
                        (string) $settings['uri'],
                        false
                    );
                }

                $this->hasBeenInitialized = true;

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

                // force CAS authentication
                phpCAS::forceAuthentication();
            }
            catch (\Exception $exception)
            {
                $this->initializeClient();
            }
        }
    }
}
