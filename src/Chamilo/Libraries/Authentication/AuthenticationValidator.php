<?php
namespace Chamilo\Libraries\Authentication;

use Chamilo\Core\Admin\Service\Consulter\ConfigurationConsulter;
use Chamilo\Core\User\Architecture\EventDispatcher\Event\AfterUserLoginEvent;
use Chamilo\Core\User\Architecture\EventDispatcher\Event\BeforeUserLogoutEvent;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Application\Routing\UrlGenerator;
use Chamilo\Libraries\Authentication\Exception\AuthenticationException;
use Chamilo\Libraries\Authentication\Interface\AuthenticationInterface;
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
    public const PARAM_AS_ADMIN = '_as_admin';

    public const PARAM_AUTHENTICATION_ERROR = 'authentication_error';

    public const SESSION_USER_ID = '_uid';

    /**
     * @var \Chamilo\Libraries\Authentication\Interface\AuthenticationInterface[]
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
            if ($authenticationType == get_class($authentication))
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
        $user_id = $this->session->get(AuthenticationValidator::SESSION_USER_ID);

        return !empty($user_id);
    }

    public function logout(User $user): void
    {
        $this->getEventDispatcher()->dispatch(new BeforeUserLogoutEvent($user, $this->request->getClientIp()));

        $this->session->invalidate();

        foreach ($this->authentications as $authentication)
        {
            if (get_class($authentication) == $user->getAuthenticationSource())
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
                Application::PARAM_CONTEXT => 'Chamilo\Core\Home'
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
        $this->session->set(AuthenticationValidator::SESSION_USER_ID, $user->getId());
    }

    /**
     * @throws \Chamilo\Libraries\Authentication\Exception\AuthenticationException
     */
    public function validate(): bool
    {
        if ($this->isAuthenticated())
        {
            return true;
        }

        foreach ($this->authentications as $authentication)
        {
            $this->validateForAuthentication($authentication);
        }

        return false;
    }

    /**
     * @throws \Chamilo\Libraries\Authentication\Exception\AuthenticationException
     */
    public function validateForAuthentication(Authentication $authentication, bool $redirectAfterLogin = true): bool
    {
        $user = $authentication->login();

        if (!$user instanceof User)
        {
            return false;
        }

        $this->validateUser($user);
        $this->setAuthenticatedUser($user);
        $this->getEventDispatcher()->dispatch(new AfterUserLoginEvent($user, $this->request->getClientIp()));

        if ($redirectAfterLogin)
        {
            $this->redirectAfterLogin();
        }

        return true;
    }

    /**
     * @throws \Chamilo\Libraries\Authentication\Exception\AuthenticationException
     */
    protected function validateUser(User $user): void
    {
        if (!$user->getActive() && !$user->isPlatformAdministrator())
        {
            throw new AuthenticationException(
                $this->translator->trans('AccountNotActive', [], StringUtilities::LIBRARIES)
            );
        }
    }
}