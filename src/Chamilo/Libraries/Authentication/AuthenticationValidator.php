<?php

namespace Chamilo\Libraries\Authentication;

use Chamilo\Configuration\Service\ConfigurationConsulter;
use Chamilo\Core\Tracking\Storage\DataClass\Event;
use Chamilo\Core\User\Manager;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Platform\ChamiloRequest;
use Chamilo\Libraries\Platform\Session\SessionUtilities;
use Chamilo\Libraries\Utilities\StringUtilities;
use Symfony\Component\Translation\Translator;

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
     * @var ChamiloRequest
     */
    protected $request;

    /**
     * @var ConfigurationConsulter
     */
    protected $configurationConsulter;

    /**
     * @var \Symfony\Component\Translation\Translator
     */
    protected $translator;

    /**
     * @var \Chamilo\Libraries\Platform\Session\SessionUtilities
     */
    protected $sessionUtilities;

    /**
     * @var \Chamilo\Libraries\Authentication\AuthenticationInterface[]
     */
    protected $authentications;

    /**
     * AuthenticationValidator constructor.
     *
     * @param ChamiloRequest $request
     * @param ConfigurationConsulter $configurationConsulter
     * @param \Symfony\Component\Translation\Translator $translator
     * @param \Chamilo\Libraries\Platform\Session\SessionUtilities $sessionUtilities
     */
    public function __construct(
        ChamiloRequest $request, ConfigurationConsulter $configurationConsulter, Translator $translator,
        SessionUtilities $sessionUtilities
    )
    {
        $this->request = $request;
        $this->configurationConsulter = $configurationConsulter;
        $this->translator = $translator;
        $this->sessionUtilities = $sessionUtilities;

        $this->authentications = [];
    }

    /**
     * @param \Chamilo\Libraries\Authentication\AuthenticationInterface $authentication
     */
    public function addAuthentication(AuthenticationInterface $authentication)
    {
        $this->authentications[$authentication->getPriority()] = $authentication;
        ksort($this->authentications);
    }

    /**
     * @param string $authenticationType
     *
     * @return \Chamilo\Libraries\Authentication\AuthenticationInterface|null
     */
    public function getAuthenticationByType($authenticationType)
    {
        foreach ($this->authentications as $authentication)
        {
            if ($authenticationType == $authentication->getAuthenticationType())
            {
                return $authentication;
            }
        }

        return null;
    }

    /**
     * @return boolean
     */
    public function isAuthenticated()
    {
        $user_id = $this->sessionUtilities->getUserId();

        return !empty($user_id);
    }

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @throws \ReflectionException
     */
    public function logout(User $user)
    {
        Event::trigger('Logout', Manager::context(), array('server' => $_SERVER, 'user' => $user));
        $this->sessionUtilities->destroy();

        foreach ($this->authentications as $authentication)
        {
            if ($authentication->getAuthenticationType() == $user->getAuthenticationSource())
            {
                $authentication->logout($user);
            }
        }
    }

    protected function redirectAfterLogin()
    {
        $context = $this->request->query->get(Application::PARAM_CONTEXT);
        if ($this->request->query->count() > 0 && $context != 'Chamilo\Core\Home')
        {
            $parameters = $this->request->query->all();
        }
        else
        {
            $parameters = array(
                Application::PARAM_CONTEXT => $this->configurationConsulter->getSetting(
                    array('Chamilo\Core\Admin', 'page_after_login')
                )
            );
        }

        $redirect = new Redirect($parameters);
        $redirect->toUrl();
        exit();
    }

    /**
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     */
    protected function setAuthenticatedUser(User $user)
    {
        $this->sessionUtilities->register('_uid', $user->getId());
    }

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @throws \ReflectionException
     */
    protected function trackLogin(User $user)
    {
        Event::trigger('Login', Manager::context(), array('server' => $_SERVER, 'user' => $user));
    }

    /**
     * @return boolean
     *
     * @throws \Chamilo\Libraries\Authentication\AuthenticationException
     * @throws \ReflectionException
     */
    public function validate()
    {
        if ($this->isAuthenticated())
        {
            return true;
        }

        $user = null;

        foreach ($this->authentications as $authentication)
        {
            $user = $authentication->login();

            if ($user instanceof User)
            {
                break;
            }
        }

        if (!$user instanceof User)
        {
            return false;
        }

        $this->validateUser($user);
        $this->setAuthenticatedUser($user);
        $this->trackLogin($user);
        $this->redirectAfterLogin();

        return true;
    }

    /**
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @throws \Chamilo\Libraries\Authentication\AuthenticationException
     */
    protected function validateUser(User $user)
    {
        $userExpirationDate = $user->get_expiration_date();
        $userActivationDate = $user->get_activation_date();

        $accountHasExpired = ($userExpirationDate != '0' && $userExpirationDate < time());
        $accountNotActivated = ($userActivationDate != '0' && $userActivationDate > time());

        if (($accountHasExpired || $accountNotActivated || !$user->get_active()) && !$user->is_platform_admin())
        {
            throw new AuthenticationException(
                $this->translator->trans('AccountNotActive', [], StringUtilities::LIBRARIES)
            );
        }
    }
}