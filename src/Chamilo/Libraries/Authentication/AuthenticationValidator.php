<?php
namespace Chamilo\Libraries\Authentication;

use Chamilo\Configuration\Service\ConfigurationConsulter;
use Chamilo\Core\Tracking\Storage\DataClass\Event;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Interfaces\UserRegistrationSupport;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Session\Session;
use Chamilo\Libraries\Platform\Translation;

/**
 *
 * @package Chamilo\Libraries\Authentication
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class AuthenticationValidator
{
    const PARAM_AUTHENTICATION_ERROR = 'authentication_error';

    /**
     *
     * @var \Chamilo\Libraries\Platform\ChamiloRequest
     */
    private $request;

    /**
     *
     * @var \Chamilo\Configuration\Service\ConfigurationConsulter
     */
    private $configurationConsulter;

    /**
     *
     * @param \Chamilo\Libraries\Platform\ChamiloRequest $request
     * @param \Chamilo\Configuration\Service\ConfigurationConsulter $configurationConsulter
     */
    public function __construct(\Chamilo\Libraries\Platform\ChamiloRequest $request,
        ConfigurationConsulter $configurationConsulter)
    {
        $this->request = $request;
        $this->configurationConsulter = $configurationConsulter;
    }

    /**
     *
     * @return \Chamilo\Libraries\Platform\ChamiloRequest
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     *
     * @param \Chamilo\Libraries\Platform\ChamiloRequest $request
     */
    public function setRequest(\Chamilo\Libraries\Platform\ChamiloRequest $request)
    {
        $this->request = $request;
    }

    /**
     *
     * @return \Chamilo\Configuration\Service\ConfigurationConsulter
     */
    public function getConfigurationConsulter()
    {
        return $this->configurationConsulter;
    }

    /**
     *
     * @param \Chamilo\Configuration\Configuration $configurationConsulter
     */
    public function setConfigurationConsulter(ConfigurationConsulter $configurationConsulter)
    {
        $this->configurationConsulter = $configurationConsulter;
    }

    /**
     *
     * @return boolean
     */
    public function validate()
    {
        if (! $this->isAuthenticated())
        {
            if ($this->performCredentialsAuthentication())
            {
                return true;
            }
            
            if ($this->performQueryAuthentication())
            {
                
                return true;
            }
            
            if ($this->performExternalAuthentication())
            {
                return true;
            }
            
            return false;
        }
        else
        {
            return true;
        }
    }

    /**
     *
     * @return boolean
     */
    public function isAuthenticated()
    {
        $user_id = Session::get_user_id();
        return ! empty($user_id);
    }

    /**
     *
     * @return boolean
     */
    public function performExternalAuthentication()
    {
        $externalAuthenticationEnabled = $this->getConfigurationConsulter()->getSetting(
            array('Chamilo\Core\Admin', 'enableExternalAuthentication'));
        $bypassExternalAuthentication = (boolean) Request::get('noExtAuth', false);
        
        if (! $externalAuthenticationEnabled || $bypassExternalAuthentication)
        {
            return false;
        }
        
        $externalAuthenticationTypes = Authentication::getExternalAuthenticationTypes();
        
        foreach ($externalAuthenticationTypes as $externalAuthenticationType)
        {
            $sourceEnabled = $this->getConfigurationConsulter()->getSetting(
                array('Chamilo\Core\Admin', 'enable' . $externalAuthenticationType . 'Authentication'));
            
            if ($sourceEnabled)
            {
                $authentication = ExternalAuthentication::factory($externalAuthenticationType);
                
                $user = $authentication->login();
                
                if ($user instanceof User)
                {
                    $this->isValidUser($user);
                    break;
                }
            }
        }
        
        if ($user instanceof User)
        {
            $this->setAuthenticatedUser($user);
            $this->trackLogin($user);
            $this->redirectAfterLogin();
        }
        else
        {
            return false;
        }
    }

    /**
     *
     * @throws AuthenticationException
     * @return \Chamilo\Core\User\Storage\DataClass\User
     */
    public function performQueryAuthentication()
    {
        $queryAuthenticationTypes = Authentication::getQueryAuthenticationTypes();
        $disabledSources = 0;
        
        foreach ($queryAuthenticationTypes as $queryAuthenticationType)
        {
            $sourceEnabled = $this->getConfigurationConsulter()->getSetting(
                array('Chamilo\Core\Admin', 'enable' . $queryAuthenticationType . 'Authentication'));
            
            if ($sourceEnabled)
            {
                $authentication = QueryAuthentication::factory($queryAuthenticationType, $this->getRequest());
                
                $user = $authentication->login();
                
                if ($user instanceof User)
                {
                    $this->isValidUser($user);
                    break;
                }
            }
            else
            {
                $disabledSources ++;
            }
        }
        
        if (isset($user) && $user instanceof User)
        {
            $this->setAuthenticatedUser($user);
            $this->trackLogin($user);
            $this->redirectAfterLogin();
        }
        else
        {
            return false;
        }
    }

    /**
     *
     * @param string $userName
     * @param string $password
     */
    public function performCredentialsAuthentication()
    {
        $userIdentifier = $this->getRequest()->request->get(CredentialsAuthentication::PARAM_LOGIN);
        $password = $this->getRequest()->request->get(CredentialsAuthentication::PARAM_PASSWORD);
        
        if ($userIdentifier && $password)
        {
            if (\Chamilo\Core\User\Storage\DataManager::usernameOrEmailExists($userIdentifier))
            {
                $user = \Chamilo\Core\User\Storage\DataManager::retrieveUserByUsernameOrEmail($userIdentifier);
                
                $this->isValidUser($user);
                
                $authenticationSource = $user->getAuthenticationSource();
                
                $sourceEnabled = $this->getConfigurationConsulter()->getSetting(
                    array('Chamilo\Core\Admin', 'enable' . $authenticationSource . 'Authentication'));
                
                if (! $sourceEnabled)
                {
                    throw new AuthenticationException(Translation::get('AccountNotActive'));
                }
                else
                {
                    $authentication = CredentialsAuthentication::factory($authenticationSource, $user->get_username());
                    $authentication->login($password);
                }
            }
            else
            {
                $errorMessages = array();
                $disabledSources = 0;
                
                $credentialsAuthenticationTypes = Authentication::getCredentialsAuthenticationTypes();
                
                foreach ($credentialsAuthenticationTypes as $credentialsAuthenticationType)
                {
                    $sourceEnabled = $this->getConfigurationConsulter()->getSetting(
                        array('Chamilo\Core\Admin', 'enable' . $credentialsAuthenticationType . 'Authentication'));
                    
                    if ($sourceEnabled)
                    {
                        $authentication = CredentialsAuthentication::factory(
                            $credentialsAuthenticationType, 
                            $userIdentifier);
                        
                        if ($authentication instanceof UserRegistrationSupport)
                        {
                            
                            try
                            {
                                if ($authentication->login($password))
                                {
                                    $user = $authentication->registerNewUser();
                                }
                                break;
                            }
                            catch (AuthenticationException $exception)
                            {
                                $errorMessages[] = $exception->getErrorMessage();
                            }
                        }
                        else
                        {
                            $disabledSources ++;
                        }
                    }
                    else
                    {
                        $disabledSources ++;
                    }
                }
                
                if (! $user instanceof User)
                {
                    if (count($credentialsAuthenticationTypes) == $disabledSources)
                    {
                        $errorMessages[] = Translation::get('UsernameOrPasswordIncorrect');
                    }
                    
                    throw new AuthenticationException(implode('<br />', $errorMessages));
                }
            }
            
            $this->setAuthenticatedUser($user);
            $this->trackLogin($user);
            $this->redirectAfterLogin();
            
            return $user;
        }
    }

    /**
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     */
    private function setAuthenticatedUser(User $user)
    {
        \Chamilo\Libraries\Platform\Session\Session::register('_uid', $user->get_id());
    }

    /**
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     */
    private function trackLogin(User $user)
    {
        Event::trigger('Login', \Chamilo\Core\User\Manager::context(), array('server' => $_SERVER, 'user' => $user));
    }

    /**
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @throws AuthenticationException
     * @return boolean
     */
    private function isValidUser(User $user)
    {
        $userExpirationDate = $user->get_expiration_date();
        $userActivationDate = $user->get_activation_date();
        
        $accountHasExpired = ($userExpirationDate != '0' && $userExpirationDate < time());
        $accountNotActivated = ($userActivationDate != '0' && $userActivationDate > time());
        
        if (($accountHasExpired || $accountNotActivated || ! $user->get_active()) && ! $user->is_platform_admin())
        {
            throw new AuthenticationException(Translation::get('AccountNotActive'));
        }
        
        return true;
    }

    private function redirectAfterLogin()
    {
        $context = $this->getRequest()->query->get(Application::PARAM_CONTEXT);
        if ($this->getRequest()->query->count() > 0 && $context != 'Chamilo\Core\Home')
        {
            $parameters = $this->getRequest()->query->all();
        }
        else
        {
            $parameters = array(
                Application::PARAM_CONTEXT => $this->getConfigurationConsulter()->getSetting(
                    array('Chamilo\Core\Admin', 'page_after_login')));
        }
        
        $redirect = new Redirect($parameters);
        $redirect->toUrl();
        exit();
    }

    /**
     * \Chamilo\Core\User\Storage\DataClass\User $user
     */
    public function logout(User $user)
    {
        $authentication = Authentication::factory($user->getAuthenticationSource());
        $authentication->logout($user);
    }
}