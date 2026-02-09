<?php
namespace Chamilo\Libraries\Protocol\Authentication\Service;

use Chamilo\Core\User\Architecture\EventDispatcher\Event\AfterUserLoginEvent;
use Chamilo\Core\User\Architecture\EventDispatcher\Event\BeforeUserLogoutEvent;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Domain\Application;
use Chamilo\Libraries\Architecture\Domain\ChamiloRequest;
use Chamilo\Libraries\Protocol\Authentication\Architecture\Exception\AuthenticationException;
use Chamilo\Libraries\Protocol\Authentication\Architecture\Exception\NotAuthenticatedException;
use Chamilo\Libraries\Protocol\Authentication\Architecture\Interface\AuthenticationInterface;
use Chamilo\Libraries\Service\Routing\UrlGenerator;
use Chamilo\Libraries\Service\Utilities\StringUtilities;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Libraries\Protocol\Authentication\Service
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
     * @var \Chamilo\Libraries\Protocol\Authentication\Architecture\Interface\AuthenticationInterface[]
     */
    protected array $authentications;

    protected array $enabledSources;

    protected EventDispatcherInterface $eventDispatcher;

    protected ChamiloRequest $request;

    protected SessionInterface $session;

    protected Translator $translator;

    protected UrlGenerator $urlGenerator;

    public function __construct(
        ChamiloRequest $request, Translator $translator, SessionInterface $session, UrlGenerator $urlGenerator,
        EventDispatcherInterface $eventDispatcher, array $enabledSources = []
    )
    {
        $this->request = $request;
        $this->translator = $translator;
        $this->session = $session;
        $this->urlGenerator = $urlGenerator;
        $this->eventDispatcher = $eventDispatcher;
        $this->enabledSources = $enabledSources;

        $this->authentications = [];
    }

    public function addAuthentication(AuthenticationInterface $authentication): void
    {
        $this->authentications[$authentication->getPriority()] = $authentication;
        ksort($this->authentications);
    }

    public function getAuthenticationByType(string $authenticationType): ?AuthenticationInterface
    {
        foreach ($this->authentications as $authentication) {
            if ($authenticationType == get_class($authentication)) {
                return $authentication;
            }
        }

        return null;
    }

    public function getAuthentications(): array
    {
        return $this->authentications;
    }

    public function getEnabledSources(): array
    {
        return $this->enabledSources;
    }

    public function getEventDispatcher(): EventDispatcherInterface
    {
        return $this->eventDispatcher;
    }

    public function getRequest(): ChamiloRequest
    {
        return $this->request;
    }

    public function getSession(): SessionInterface
    {
        return $this->session;
    }

    public function getTranslator(): Translator
    {
        return $this->translator;
    }

    public function getUrlGenerator(): UrlGenerator
    {
        return $this->urlGenerator;
    }

    public function isAuthenticated(): bool
    {
        $userIdentifier = $this->session->get(AuthenticationValidator::SESSION_USER_ID);

        return !empty($userIdentifier);
    }

    public function isSourceEnabled(string $authenticationSource): bool
    {
        return in_array($authenticationSource, $this->getEnabledSources());
    }

    public function logout(User $user): void
    {
        $this->getEventDispatcher()->dispatch(new BeforeUserLogoutEvent($user, $this->request->getClientIp()));

        $this->session->invalidate();

        foreach ($this->authentications as $authentication) {
            if (get_class($authentication) == $user->getAuthenticationSource()) {
                $authentication->logout($user);
            }
        }
    }

    protected function redirectAfterLogin(): void
    {
        $context = $this->request->query->get(Application::PARAM_CONTEXT);

        if ($this->request->query->count() > 0 && $context != 'Chamilo\Core\Home') {
            $parameters = $this->request->query->all();
        }
        else {
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
     * @throws \Chamilo\Libraries\Protocol\Authentication\Architecture\Exception\NotAuthenticatedException
     */
    public function validate(bool $checkIfAuthenticationSourceIsEnabled = true): void
    {
        if ($this->isAuthenticated()) {
            return;
        }

        foreach ($this->authentications as $authentication) {
            try {
                $this->validateForAuthentication($authentication, $checkIfAuthenticationSourceIsEnabled);
            }
            catch (NotAuthenticatedException) {
            }
        }

        throw new NotAuthenticatedException(true);
    }

    /**
     * @throws \Chamilo\Libraries\Protocol\Authentication\Architecture\Exception\NotAuthenticatedException
     */
    public function validateForAuthentication(
        Authentication $authentication, bool $checkIfAuthenticationSourceIsEnabled = true,
        bool $redirectAfterLogin = true
    ): void
    {
        try {
            $user = $authentication->login($checkIfAuthenticationSourceIsEnabled);

            if (!$user instanceof User) {
                return;
            }

            $this->validateUser($user);
            $this->setAuthenticatedUser($user);
            $this->getEventDispatcher()->dispatch(new AfterUserLoginEvent($user, $this->request->getClientIp()));

            if ($redirectAfterLogin) {
                $this->redirectAfterLogin();
            }

            return;
        }
        catch (AuthenticationException) {
            throw new NotAuthenticatedException(true);
        }
    }

    /**
     * @throws \Chamilo\Libraries\Protocol\Authentication\Architecture\Exception\AuthenticationException
     */
    protected function validateUser(User $user): void
    {
        if (!$user->getActive() && !$user->isPlatformAdministrator()) {
            throw new AuthenticationException(
                $this->translator->trans('AccountNotActive', [], StringUtilities::LIBRARIES)
            );
        }
    }
}