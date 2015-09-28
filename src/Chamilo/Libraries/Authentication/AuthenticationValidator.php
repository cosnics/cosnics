<?php
namespace Chamilo\Libraries\Authentication;

use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Session\Session;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Core\Tracking\Storage\DataClass\Event;
use Chamilo\Libraries\Architecture\Interfaces\UserRegistrationSupport;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\File\Redirect;

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
     * @var \Symfony\Component\HttpFoundation\Request
     */
    private $request;

    /**
     *
     * @var \Chamilo\Configuration\Configuration
     */
    private $configuration;

    /**
     *
     * @param \Chamilo\Configuration\Configuration $configuration
     */
    public function __construct(\Symfony\Component\HttpFoundation\Request $request,
        \Chamilo\Configuration\Configuration $configuration)
    {
        $this->request = $request;
        $this->configuration = $configuration;
    }

    /**
     *
     * @return \Symfony\Component\HttpFoundation\Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     */
    public function setRequest(\Symfony\Component\HttpFoundation\Request $request)
    {
        $this->request = $request;
    }

    /**
     *
     * @return \Chamilo\Configuration\Configuration
     */
    public function getConfiguration()
    {
        return $this->configuration;
    }

    /**
     *
     * @param \Chamilo\Configuration\Configuration $configuration
     */
    public function setConfiguration(\Chamilo\Configuration\Configuration $configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     *
     * @return boolean
     */
    public function validate()
    {
        if (! $this->isAuthenticated())
        {

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
            // TODO: Re-invent this in a durable way ...
            // $preventDoubleLogin = (boolean) $this->getConfiguration()->get_setting(
            // \Chamilo\Core\User\Manager :: context(),
            // 'prevent_double_login');

            // if ($preventDoubleLogin)
            // {
            // \Chamilo\Core\User\Storage\DataClass\UserLoginSession :: check_single_login(false);
            // }

            return true;
        }
    }

    /**
     *
     * @return boolean
     */
    public function isAuthenticated()
    {
        $user_id = Session :: get_user_id();
        return ! empty($user_id);
    }

    /**
     *
     * @return boolean
     */
    public function performExternalAuthentication()
    {
        $externalAuthenticationEnabled = $this->getConfiguration()->get_setting(
            array('Chamilo\Core\Admin', 'enableExternalAuthentication'));
        $bypassExternalAuthentication = (boolean) Request :: get('noExtAuth', false);

        if (! $externalAuthenticationEnabled || $bypassExternalAuthentication)
        {
            return false;
        }

        $externalAuthenticationTypes = Authentication :: getExternalAuthenticationTypes();

        foreach ($externalAuthenticationTypes as $externalAuthenticationType)
        {
            $sourceEnabled = $this->getConfiguration()->get_setting(
                array('Chamilo\Core\Admin', 'enable' . $externalAuthenticationType . 'Authentication'));

            if ($sourceEnabled)
            {
                $authentication = ExternalAuthentication :: factory($externalAuthenticationType);

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
        $queryAuthenticationTypes = Authentication :: getQueryAuthenticationTypes();
        $disabledSources = 0;

        foreach ($queryAuthenticationTypes as $queryAuthenticationType)
        {
            $sourceEnabled = $this->getConfiguration()->get_setting(
                array('Chamilo\Core\Admin', 'enable' . $queryAuthenticationType . 'Authentication'));

            if ($sourceEnabled)
            {
                $authentication = QueryAuthentication :: factory($queryAuthenticationType, $this->getRequest());

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
     * @param string $userName
     * @param string $password
     */
    public function performCredentialsAuthentication($userName, $password)
    {

        if (\Chamilo\Core\User\Storage\DataManager :: userExists($userName))
        {
            $user = \Chamilo\Core\User\Storage\DataManager :: retrieveUserByUsername($userName);
            $this->isValidUser($user);

            $authenticationSource = $user->getAuthenticationSource();

            $sourceEnabled = $this->getConfiguration()->get_setting(
                array('Chamilo\Core\Admin', 'enable' . $authenticationSource . 'Authentication'));

            if (! $sourceEnabled)
            {
                throw new AuthenticationException(Translation :: get('AccountNotActive'));
            }
            else
            {
                $authentication = CredentialsAuthentication :: factory($authenticationSource, $userName);
                $authentication->login($password);
            }
        }
        else
        {
            $errorMessages = array();
            $disabledSources = 0;

            $credentialsAuthenticationTypes = Authentication :: getCredentialsAuthenticationTypes();

            foreach ($credentialsAuthenticationTypes as $credentialsAuthenticationType)
            {
                $sourceEnabled = $this->getConfiguration()->get_setting(
                    array('Chamilo\Core\Admin', 'enable' . $credentialsAuthenticationType . 'Authentication'));

                if ($sourceEnabled)
                {
                    $authentication = CredentialsAuthentication :: factory($credentialsAuthenticationType, $userName);

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
                            $errorMessages[] = $exception->getMessage();
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
                    $errorMessages[] = Translation :: get('UsernameOrPasswordIncorrect');
                }

                throw new AuthenticationException(implode('<br />', $errorMessages));
            }
        }

        $this->setAuthenticatedUser($user);
        $this->trackLogin($user);
        $this->redirectAfterLogin();

        return $user;
    }

    /**
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     */
    private function setAuthenticatedUser(User $user)
    {
        \Chamilo\Libraries\Platform\Session\Session :: register('_uid', $user->get_id());
    }

    /**
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     */
    private function trackLogin(User $user)
    {
        Event :: trigger('Login', \Chamilo\Core\User\Manager :: context(), array('server' => $_SERVER, 'user' => $user));
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

        if (($userExpirationDate != '0' && $userExpirationDate < time()) ||
             ($userActivationDate != '0' && $userActivationDate > time()) || ! $user->get_active())
        {
            throw new AuthenticationException(Translation :: get('AccountNotActive'));
        }

        return true;
    }

    private function redirectAfterLogin()
    {
        $parameters = array(
            Application :: PARAM_CONTEXT => $this->getConfiguration()->get_setting(
                'Chamilo\Core\Admin',
                'page_after_login'));

        $redirect = new Redirect($parameters);
        $redirect->toUrl();
        exit();
    }

    /**
     * \Chamilo\Core\User\Storage\DataClass\User $user
     */
    public function logout(User $user)
    {
        $authentication = Authentication :: factory($user->getAuthenticationSource());
        $authentication->logout($user);
    }
}