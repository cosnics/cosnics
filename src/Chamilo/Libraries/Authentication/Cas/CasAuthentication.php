<?php
namespace Chamilo\Libraries\Authentication\Cas;

use Chamilo\Configuration\Configuration;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Authentication\AuthenticationException;
use Chamilo\Libraries\Authentication\ExternalAuthentication;
use Chamilo\Libraries\Platform\Configuration\PlatformSetting;
use Chamilo\Libraries\Platform\Translation;
use phpCAS;

/**
 *
 * @package Chamilo\Libraries\Authentication\Cas
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class CasAuthentication extends ExternalAuthentication
{

    /**
     *
     * @var string[]
     */
    private $settings;

    /**
     *
     * @var boolean
     */
    private $hasBeenInitialized = false;

    /**
     *
     * @see \Chamilo\Libraries\Authentication\ExternalAuthentication::login()
     */
    public function login()
    {
        if (! $this->hasBeenInitialized)
        {
            $this->initializeClient();
        }

        $userAttributes = phpCAS::getAttributes();

        if (PlatformSetting::get('cas_user_login') == 'email')
        {
            if ((is_numeric($userAttributes['person_number']) && $userAttributes['person_number'] > 0) ||
                 strpos($userAttributes['person_number'], PlatformSetting::get('cas_validation_string')) !== false)

            {
                $user = \Chamilo\Core\User\Storage\DataManager::retrieve_user_by_official_code(
                    $userAttributes['person_number']);

                if (! $user instanceof User)
                {
                    $user = $this->registerUser();
                }

                return $user;
            }
            elseif (is_numeric($userAttributes['person_number']) && $userAttributes['person_number'] == - 1)
            {
                $user = \Chamilo\Core\User\Storage\DataManager::retrieve_user_by_username($userAttributes['email']);

                if (! $user instanceof User)
                {
                    $user = $this->registerUser();
                }

                return $user;
            }
            else
            {
                throw new AuthenticationException(
                    Translation::get(
                        'CasAuthenticationError',
                        array(
                            'PLATFORM' => Configuration::getInstance()->get_setting(
                                'Chamilo\Core\Admin',
                                'platform_name'))));
            }
        }
        else
        {
            if (strpos($userAttributes['email'], PlatformSetting::get('cas_validation_string')) !== false)
            {
                $user = \Chamilo\Core\User\Storage\DataManager::retrieve_user_by_username($userAttributes['login']);

                if (! $user instanceof User)
                {
                    $user = $this->registerUser();
                }

                return $user;
            }
            else
            {
                throw new AuthenticationException(
                    Translation::get(
                        'CasAuthenticationError',
                        array(
                            'PLATFORM' => Configuration::getInstance()->get_setting(
                                'Chamilo\Core\Admin',
                                'platform_name'))));
            }
        }
    }

    /**
     *
     * @throws AuthenticationException
     * @return \Chamilo\Core\User\Storage\DataClass\User
     */
    public function registerUser()
    {
        if (! $this->hasBeenInitialized)
        {
            $this->initializeClient();
        }

        $userAttributes = phpCAS::getAttributes();

        $user = new User();
        if (PlatformSetting::get('cas_user_login') === 'login')
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

        if (! $user->create())
        {
            throw new AuthenticationException('CasUserRegistrationFailed');
        }
        else
        {
            return $user;
        }
    }

    /**
     *
     * @see \Chamilo\Libraries\Authentication\Authentication::logout()
     */
    public function logout($user)
    {
        if (! $this->isConfigured())
        {
            throw new AuthenticationException(Translation::get('CheckCASConfiguration'));
        }
        else
        {
            $this->trackLogout($user);

            if (! $this->hasBeenInitialized)
            {
                $this->initializeClient();
            }

            // Do the logout
            phpCAS::logout();
        }
    }

    /**
     *
     * @return string[]
     */
    public function getConfiguration()
    {
        if (! isset($this->settings))
        {
            $this->settings = array();
            $this->settings['host'] = PlatformSetting::get('cas_host');
            $this->settings['port'] = PlatformSetting::get('cas_port');
            $this->settings['uri'] = PlatformSetting::get('cas_uri');
            $this->settings['certificate'] = PlatformSetting::get('cas_certificate');
            $this->settings['log'] = PlatformSetting::get('cas_log');
            $this->settings['enable_log'] = PlatformSetting::get('cas_enable_log');
        }

        return $this->settings;
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
            if ((empty($value) || ! isset($value)) && ! in_array(
                $setting,
                array('uri', 'certificate', 'log', 'enable_log')))
            {
                return false;
            }
        }

        return true;
    }

    /**
     *
     * @throws \Exception
     * @throws AuthenticationException
     */
    public function initializeClient()
    {
        if (! $this->isConfigured())
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

                if (PlatformSetting::get('cas_version') == 'SAML_VERSION_1_1')
                {
                    phpCAS::client(
                        SAML_VERSION_1_1,
                        $settings['host'],
                        (int) $settings['port'],
                        (string) $settings['uri'],
                        false);
                }
                else
                {
                    phpCAS::client(
                        CAS_VERSION_2_0,
                        $settings['host'],
                        (int) $settings['port'],
                        (string) $settings['uri'],
                        false);
                }

                $this->hasBeenInitialized = true;

                // SSL validation for the CAS server
                if (PlatformSetting::get('cas_check_certificate') == '1')
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
