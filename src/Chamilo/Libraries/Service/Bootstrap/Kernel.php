<?php
namespace Chamilo\Libraries\Service\Bootstrap;

use Chamilo\Core\Home\Manager;
use Chamilo\Core\User\Architecture\EventDispatcher\Event\AfterUserEnterPageEvent;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Domain\Application;
use Chamilo\Libraries\Architecture\Domain\ChamiloRequest;
use Chamilo\Libraries\Architecture\Exception\PlatformNotAvailableException;
use Chamilo\Libraries\Architecture\Exception\UserException;
use Chamilo\Libraries\Architecture\Interface\NoVisitTraceComponentInterface;
use Chamilo\Libraries\Architecture\Response\PlatformNotAvailableResponse;
use Chamilo\Libraries\Protocol\Authentication\Architecture\Exception\NotAuthenticatedException;
use Chamilo\Libraries\Protocol\Authentication\Architecture\Interface\NoAuthenticationSupportInterface;
use Chamilo\Libraries\Protocol\Authentication\Architecture\Response\NotAuthenticatedResponse;
use Chamilo\Libraries\Protocol\Authentication\Service\AuthenticationValidator;
use Chamilo\Libraries\Protocol\Error\Architecture\Interface\ExceptionLoggerInterface;
use Chamilo\Libraries\Protocol\Error\Architecture\Response\ExceptionResponse;
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
    public const PARAM_CODE = 'code';
    public const PARAM_SESSION_STATE = 'session_state';
    public const PARAM_STATE = 'state';

    protected AuthenticationValidator $authenticationValidator;

    protected EventDispatcherInterface $eventDispatcher;

    protected bool $maintenanceMode;

    protected SessionInterface $session;

    protected ?string $timezone;

    private ApplicationFactory $applicationFactory;

    private ExceptionLoggerInterface $exceptionLogger;

    private ChamiloRequest $request;

    private UrlGenerator $urlGenerator;

    private ?User $user;

    public function __construct(
        ChamiloRequest $request, SessionInterface $session, ApplicationFactory $applicationFactory,
        ExceptionLoggerInterface $exceptionLogger, AuthenticationValidator $authenticationValidator,
        UrlGenerator $urlGenerator, EventDispatcherInterface $eventDispatcher, string $timezone, User $user = null,
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
    }

    /**
     * @throws \Chamilo\Libraries\Architecture\Exception\ClassNotExistException
     * @throws \Chamilo\Libraries\Protocol\Authentication\Architecture\Exception\NotAuthenticatedException
     * @throws \Chamilo\Libraries\Protocol\Authentication\Architecture\Exception\AuthenticationException
     * @throws \Exception
     */
    protected function checkAuthentication(): static
    {
        $applicationClassName = $this->getApplicationFactory()->getClassName($this->getContext());
        $applicationRequiresAuthentication =
            !is_subclass_of($applicationClassName, NoAuthenticationSupportInterface::class);

        if ($applicationRequiresAuthentication) {
            $this->getAuthenticationValidator()->validate();
        }

        return $this;
    }

    /**
     * @throws \Chamilo\Libraries\Architecture\Exception\PlatformNotAvailableException
     */
    protected function checkPlatformAvailability(): static
    {
        if ($this->isMaintenanceMode()) {
            $asAdmin = $this->getSession()->get('_as_admin');

            if ($this->getUser() instanceof User && !$this->getUser()->isPlatformAdministrator() && !$asAdmin) {
                throw new PlatformNotAvailableException('Platform temporarily unavailable due to maintenance.');
            }
        }

        return $this;
    }

    protected function configureTimezone(): static
    {
        date_default_timezone_set($this->getTimezone());

        return $this;
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
        return $this->getRequest()->getFromQueryOrRequest(Application::PARAM_CONTEXT, Manager::CONTEXT);
    }

    public function getEventDispatcher(): EventDispatcherInterface
    {
        return $this->eventDispatcher;
    }

    public function getExceptionLogger(): ExceptionLoggerInterface
    {
        return $this->exceptionLogger;
    }

    protected function getNotAuthenticatedResponse(): NotAuthenticatedResponse
    {
        return new NotAuthenticatedResponse();
    }

    protected function getPlatformNotAvailableResponse(): PlatformNotAvailableResponse
    {
        return new PlatformNotAvailableResponse();
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

            $application = $this->getApplicationFactory()->getApplication($this->getContext());
            $this->traceVisit($application);

            $response = $application->run($this->getUser());
        }
        catch (NotAuthenticatedException) {
            $response = $this->getNotAuthenticatedResponse();
        }
        catch (PlatformNotAvailableException) {
            $response = $this->getPlatformNotAvailableResponse();
        }
        catch (UserException $exception) {
            $this->getExceptionLogger()->logException($exception, ExceptionLoggerInterface::EXCEPTION_LEVEL_WARNING);

            $response = new ExceptionResponse($exception);
        }

        $this->sendResponse($response);
    }

    protected function sendResponse(Response $response): void
    {
        $response->send();
    }

    protected function traceVisit(Application $application): static
    {
        if (!$application instanceof NoVisitTraceComponentInterface && $this->getUser() instanceof User) {
            $this->getEventDispatcher()->dispatch(
                new AfterUserEnterPageEvent($this->getUser(), $this->getRequest()->getRequestUri())
            );
        }

        return $this;
    }
}
