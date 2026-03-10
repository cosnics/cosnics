<?php
namespace Chamilo\Libraries\Service\Bootstrap;

use Chamilo\Core\Home\Manager;
use Chamilo\Core\User\Architecture\EventDispatcher\Event\AfterUserEnterPageEvent;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Domain\ChamiloRequest;
use Chamilo\Libraries\Architecture\Interface\ApplicationInterface;
use Chamilo\Libraries\Architecture\Interface\NoVisitTraceComponentInterface;
use Chamilo\Libraries\Protocol\Authentication\Architecture\Interface\NoAuthenticationSupportInterface;
use Chamilo\Libraries\Protocol\Authentication\Service\AuthenticationValidator;
use Chamilo\Libraries\Protocol\Error\Architecture\Exception\PlatformNotAvailableException;
use Chamilo\Libraries\Protocol\Error\Architecture\Interface\ExceptionLoggerInterface;
use Chamilo\Libraries\Protocol\Error\Architecture\Interface\UserExceptionInterface;
use Chamilo\Libraries\Protocol\Error\Service\UserExceptionResponseRenderer;
use Chamilo\Libraries\Service\Routing\UrlGenerator;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * @package Chamilo\Libraries\Service\Bootstrap
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 */
class Kernel
{
    public const string PARAM_CODE = 'code';
    public const string PARAM_SESSION_STATE = 'session_state';
    public const string PARAM_STATE = 'state';

    protected AuthenticationValidator $authenticationValidator;

    protected EventDispatcherInterface $eventDispatcher;

    protected bool $maintenanceMode;

    protected SessionInterface $session;

    protected ?string $timezone;

    protected UserExceptionResponseRenderer $userExceptionResponseRenderer;

    private ApplicationFactory $applicationFactory;

    private ExceptionLoggerInterface $exceptionLogger;

    private ChamiloRequest $request;

    private UrlGenerator $urlGenerator;

    private ?User $user;

    public function __construct(
        ChamiloRequest $request, SessionInterface $session, ApplicationFactory $applicationFactory,
        ExceptionLoggerInterface $exceptionLogger, AuthenticationValidator $authenticationValidator,
        UrlGenerator $urlGenerator, EventDispatcherInterface $eventDispatcher,
        UserExceptionResponseRenderer $userExceptionResponseRenderer, string $timezone, User $user = null,
        bool $maintenanceMode = false
    )
    {
        $this->request = $request;
        $this->applicationFactory = $applicationFactory;
        $this->session = $session;
        $this->exceptionLogger = $exceptionLogger;
        $this->urlGenerator = $urlGenerator;
        $this->user = $user;
        $this->authenticationValidator = $authenticationValidator;
        $this->eventDispatcher = $eventDispatcher;
        $this->maintenanceMode = $maintenanceMode;
        $this->timezone = $timezone;
        $this->userExceptionResponseRenderer = $userExceptionResponseRenderer;
    }

    /**
     * @throws \Chamilo\Libraries\Protocol\Error\Architecture\Exception\UserException
     * @throws \Chamilo\Libraries\Protocol\Authentication\Architecture\Exception\NotAuthenticatedException
     */
    protected function checkAuthentication(): static
    {
        $application = $this->getApplicationFactory()->getApplicationComponent($this->getContext(), $this->getAction());

        if (!$application instanceof NoAuthenticationSupportInterface) {
            $this->getAuthenticationValidator()->validate();
        }

        return $this;
    }

    /**
     * @throws \Chamilo\Libraries\Protocol\Error\Architecture\Exception\PlatformNotAvailableException
     */
    protected function checkPlatformAvailability(): static
    {
        if ($this->isMaintenanceMode()) {
            $asAdmin = $this->getSession()->get('_as_admin');

            if ($this->getUser() instanceof User && !$this->getUser()->isPlatformAdministrator() && !$asAdmin) {
                throw new PlatformNotAvailableException();
            }
        }

        return $this;
    }

    protected function configureTimezone(): static
    {
        date_default_timezone_set($this->getTimezone());

        return $this;
    }

    protected function getAction(): ?string
    {
        $request = $this->getRequest();

        $getAction = $request->query->get(ApplicationInterface::PARAM_ACTION);

        if ($getAction) {
            return $getAction;
        }

        $postAction = $request->request->get(ApplicationInterface::PARAM_ACTION);

        if ($postAction) {
            return $postAction;
        }

        return null;
    }

    public function getApplicationFactory(): ApplicationFactory
    {
        return $this->applicationFactory;
    }

    public function getAuthenticationValidator(): AuthenticationValidator
    {
        return $this->authenticationValidator;
    }

    public function getContext(): ?string
    {
        return $this->getRequest()->getFromQueryOrRequest(ApplicationInterface::PARAM_CONTEXT, Manager::CONTEXT);
    }

    public function getEventDispatcher(): EventDispatcherInterface
    {
        return $this->eventDispatcher;
    }

    public function getExceptionLogger(): ExceptionLoggerInterface
    {
        return $this->exceptionLogger;
    }

    public function getRequest(): ChamiloRequest
    {
        return $this->request;
    }

    public function getSession(): SessionInterface
    {
        return $this->session;
    }

    public function getTimezone(): ?string
    {
        return $this->timezone;
    }

    public function getUrlGenerator(): UrlGenerator
    {
        return $this->urlGenerator;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function getUserExceptionResponseRenderer(): UserExceptionResponseRenderer
    {
        return $this->userExceptionResponseRenderer;
    }

    /**
     * Redirects response of Microsoft OAuth 2.0 Authorization workflow to the component which have called
     * MicrosoftClientService::login(...).
     *
     * @see MicrosoftClientService::login(...)
     */
    public function handleOAuth2(): static
    {
        $code = $this->getRequest()->query->get(self::PARAM_CODE);
        $state = $this->getRequest()->query->get(self::PARAM_STATE);
        $sessionState = $this->getRequest()->query->get(self::PARAM_SESSION_STATE); // Not provided in OAUTH2 v2.0

        if (!$code || !$state) {
            return $this;
        }
        $decodedState = base64_decode($state);

        if (!$decodedState) {
            return $this;
        }

        $stateParameters = json_decode($decodedState, true);

        if (!is_array($stateParameters) || !array_key_exists('landingPageParameters', $stateParameters)) {
            return $this;
        }

        $landingPageParameters = $stateParameters['landingPageParameters'];
        $landingPageParameters[self::PARAM_CODE] = $code;

        unset($stateParameters['landingPageParameters']);

        $landingPageParameters[self::PARAM_STATE] = base64_encode(json_encode($stateParameters));

        if ($sessionState) {
            $landingPageParameters[self::PARAM_SESSION_STATE] = $sessionState;
        }

        $response = new RedirectResponse($this->getUrlGenerator()->fromParameters($landingPageParameters));
        $response->send();
        exit;
    }

    public function isMaintenanceMode(): bool
    {
        return $this->maintenanceMode;
    }

    /**
     * @throws \Exception
     */
    public function launch(): void
    {
        try {
            $this->configureTimezone()->handleOAuth2()->checkAuthentication()->checkPlatformAvailability();

            $application =
                $this->getApplicationFactory()->getApplicationComponent($this->getContext(), $this->getAction());
            $this->traceVisit($application);

            $response = $application->run($this->getUser());
        }
        catch (UserExceptionInterface $exception) {
            $this->getExceptionLogger()->logException($exception, ExceptionLoggerInterface::EXCEPTION_LEVEL_WARNING);

            $response = new Response($this->getUserExceptionResponseRenderer()->render($exception));
        }

        $this->sendResponse($response);
    }

    protected function sendResponse(Response $response): void
    {
        $response->send();
    }

    protected function traceVisit(ApplicationInterface $application): static
    {
        if (!$application instanceof NoVisitTraceComponentInterface && $this->getUser() instanceof User) {
            $this->getEventDispatcher()->dispatch(
                new AfterUserEnterPageEvent($this->getUser(), $this->getRequest()->getRequestUri())
            );
        }

        return $this;
    }
}
