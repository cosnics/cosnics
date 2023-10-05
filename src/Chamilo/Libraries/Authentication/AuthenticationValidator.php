<?php
namespace Chamilo\Libraries\Authentication;

use Chamilo\Configuration\Service\Consulter\ConfigurationConsulter;
use Chamilo\Core\User\EventDispatcher\Event\AfterUserLoginEvent;
use Chamilo\Core\User\EventDispatcher\Event\BeforeUserLogoutEvent;
use Chamilo\Core\User\Manager;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Application\Routing\UrlGenerator;
use Chamilo\Libraries\Platform\ChamiloRequest;
use Chamilo\Libraries\Utilities\StringUtilities;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
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
    protected array $authentications;

    protected ConfigurationConsulter $configurationConsulter;

    protected EventDispatcherInterface $eventDispatcher;

    protected ChamiloRequest $request;

    protected SessionInterface $session;

    protected Translator $translator;

    protected UrlGenerator $urlGenerator;

    public function __construct(
        ChamiloRequest $request, ConfigurationConsulter $configurationConsulter, Translator $translator,
        SessionInterface $session, UrlGenerator $urlGenerator, EventDispatcherInterface $eventDispatcher
    )
    {
        $this->request = $request;
        $this->configurationConsulter = $configurationConsulter;
        $this->translator = $translator;
        $this->session = $session;
        $this->urlGenerator = $urlGenerator;
        $this->eventDispatcher = $eventDispatcher;

        $this->authentications = [];
    }

    public function addAuthentication(AuthenticationInterface $authentication): void
    {
        $this->authentications[$authentication->getPriority()] = $authentication;
        ksort($this->authentications);
    }

    public function getAuthenticationByType(string $authenticationType): ?AuthenticationInterface
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

    public function getEventDispatcher(): EventDispatcherInterface
    {
        return $this->eventDispatcher;
    }

    public function isAuthenticated(): bool
    {
        $user_id = $this->session->get(Manager::SESSION_USER_ID);

        return !empty($user_id);
    }

    public function logout(User $user): void
    {
        $this->getEventDispatcher()->dispatch(new BeforeUserLogoutEvent($user, $this->request->getClientIp()));

        $this->session->invalidate();

        foreach ($this->authentications as $authentication)
        {
            if ($authentication->getAuthenticationType() == $user->getAuthenticationSource())
            {
                $authentication->logout($user);
            }
        }
    }

    protected function redirectAfterLogin(): void
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

    protected function setAuthenticatedUser(User $user): void
    {
        $this->session->set(Manager::SESSION_USER_ID, $user->getId());
    }

    /**
     * @throws \Chamilo\Libraries\Authentication\AuthenticationException
     */
    public function validate(): bool
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
        $this->getEventDispatcher()->dispatch(new AfterUserLoginEvent($user, $this->request->getClientIp()));
        $this->redirectAfterLogin();

        return true;
    }

    /**
     * @throws \Chamilo\Libraries\Authentication\AuthenticationException
     */
    protected function validateUser(User $user): void
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