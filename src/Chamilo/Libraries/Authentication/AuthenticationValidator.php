<?php
namespace Chamilo\Libraries\Authentication;

use Chamilo\Configuration\Service\Consulter\ConfigurationConsulter;
use Chamilo\Core\Tracking\Storage\DataClass\Event;
use Chamilo\Core\User\Manager;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Application\Routing\UrlGenerator;
use Chamilo\Libraries\Platform\ChamiloRequest;
use Chamilo\Libraries\Utilities\StringUtilities;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Libraries\Authentication
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class AuthenticationValidator
{
    public const PARAM_AUTHENTICATION_ERROR = 'authentication_error';

    /**
     * @var \Chamilo\Libraries\Authentication\AuthenticationInterface[]
     */
    protected $authentications;

    /**
     * @var ConfigurationConsulter
     */
    protected $configurationConsulter;

    /**
     * @var ChamiloRequest
     */
    protected $request;

    protected SessionInterface $session;

    /**
     * @var \Symfony\Component\Translation\Translator
     */
    protected $translator;

    protected UrlGenerator $urlGenerator;

    public function __construct(
        ChamiloRequest $request, ConfigurationConsulter $configurationConsulter, Translator $translator,
        SessionInterface $session, UrlGenerator $urlGenerator
    )
    {
        $this->request = $request;
        $this->configurationConsulter = $configurationConsulter;
        $this->translator = $translator;
        $this->session = $session;
        $this->urlGenerator = $urlGenerator;

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
     * @return bool
     */
    public function isAuthenticated()
    {
        $user_id = $this->session->get(Manager::SESSION_USER_ID);

        return !empty($user_id);
    }

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @throws \ReflectionException
     */
    public function logout(User $user)
    {
        Event::trigger('Logout', Manager::CONTEXT, ['server' => $_SERVER, 'user' => $user]);
        $this->session->invalidate();

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
            $parameters = [
                Application::PARAM_CONTEXT => $this->configurationConsulter->getSetting(
                    ['Chamilo\Core\Admin', 'page_after_login']
                )
            ];
        }

        $redirect = new RedirectResponse(
            $this->urlGenerator->fromParameters($parameters)
        );

        $redirect->send();
        exit;
    }

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     */
    protected function setAuthenticatedUser(User $user)
    {
        $this->session->set(Manager::SESSION_USER_ID, $user->getId());
    }

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @throws \ReflectionException
     */
    protected function trackLogin(User $user)
    {
        Event::trigger('Login', Manager::CONTEXT, ['server' => $_SERVER, 'user' => $user]);
    }

    /**
     * @return bool
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

        if (($accountHasExpired || $accountNotActivated || !$user->get_active()) && !$user->isPlatformAdmin())
        {
            throw new AuthenticationException(
                $this->translator->trans('AccountNotActive', [], StringUtilities::LIBRARIES)
            );
        }
    }
}