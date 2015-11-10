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

    private $settings;

    private $hasBeenInitialized = false;

    public function login()
    {
        if (! $this->hasBeenInitialized)
        {
            $this->initializeClient();
        }

        $userAttributes = phpCAS :: getAttributes();

        if (is_numeric($userAttributes['person_number']) || strpos($userAttributes['person_number'], 'EXT') !== false)
        {
            $user = \Chamilo\Core\User\Storage\DataManager :: retrieve_user_by_official_code(
                $userAttributes['person_number']);

            if (! $user instanceof User)
            {
                $user = $this->registerUser();
            }

            return $user;
        }
        else
        {
            throw new AuthenticationException(
                Translation :: get(
                    'CasAuthenticationError',
                    array(
                        'PLATFORM' => Configuration :: get_instance()->get_setting(
                            'Chamilo\Core\Admin',
                            'platform_name'))));
        }
    }

    public function registerUser()
    {
        if (! $this->hasBeenInitialized)
        {
            $this->initializeClient();
        }

        $userAttributes = phpCAS :: getAttributes();

        $user = new User();
        $user->set_username($userAttributes['email']);
        $user->set_password('PLACEHOLDER');
        $user->set_status(User :: STATUS_STUDENT);
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

    public function logout($user)
    {
        if (! $this->isConfigured())
        {
            throw new AuthenticationException(Translation :: get('CheckCASConfiguration'));
        }
        else
        {
            $this->trackLogout($user);

            if (! $this->hasBeenInitialized)
            {
                $this->initializeClient();
            }

            // Do the logout
            phpCAS :: logout();
        }
    }

    public function getConfiguration()
    {
        if (! isset($this->settings))
        {
            $this->settings = array();
            $this->settings['host'] = PlatformSetting :: get('cas_host');
            $this->settings['port'] = PlatformSetting :: get('cas_port');
            $this->settings['uri'] = PlatformSetting :: get('cas_uri');
            $this->settings['certificate'] = PlatformSetting :: get('cas_certificate');
            $this->settings['log'] = PlatformSetting :: get('cas_log');
            $this->settings['enable_log'] = PlatformSetting :: get('cas_enable_log');
        }

        return $this->settings;
    }

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

    public function initializeClient()
    {
        if (! $this->isConfigured())
        {
            throw new \Exception(Translation :: get('CheckCASConfiguration'));
        }
        else
        {
            usleep(500);

            $settings = $this->getConfiguration();

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

            $this->hasBeenInitialized = true;

            // SSL validation for the CAS server
            phpCAS :: setExtraCurlOption(CURLOPT_SSLVERSION, 3);
            phpCAS :: setCasServerCACert($settings['certificate']);

            // force CAS authentication
            phpCAS :: forceAuthentication();
        }
    }
}
