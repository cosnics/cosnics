<?php
namespace Chamilo\Libraries\Service\Bootstrap;

use Chamilo\Core\Home\Manager;
use Chamilo\Core\User\Architecture\EventDispatcher\Event\AfterUserEnterPageEvent;
use Chamilo\Core\User\Storage\Entity\User;
use Chamilo\Libraries\Architecture\Domain\ChamiloRequest;
use Chamilo\Libraries\Architecture\Interface\ApplicationInterface;
use Chamilo\Libraries\Architecture\Interface\NoVisitTraceComponentInterface;
use Chamilo\Libraries\Protocol\Authentication\Architecture\Interface\NoAuthenticationSupportInterface;
use Chamilo\Libraries\Protocol\Authentication\Service\AuthenticationValidator;
use Chamilo\Libraries\Protocol\ErrorHandling\Architecture\Interface\ExceptionLoggerInterface;
use Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\PlatformNotAvailableException;
use Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Interface\UserExceptionInterface;
use Chamilo\Libraries\Protocol\ExceptionHandling\Service\UserExceptionResponseRenderer;
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

    public function __construct(
        protected ChamiloRequest $request, protected SessionInterface $session,
        protected ApplicationFactory $applicationFactory, protected ExceptionLoggerInterface $exceptionLogger,
        protected AuthenticationValidator $authenticationValidator, protected UrlGenerator $urlGenerator,
        protected EventDispatcherInterface $eventDispatcher,
        protected UserExceptionResponseRenderer $userExceptionResponseRenderer, protected ?string $timezone = null,
        protected ?User $currentUser = null, protected bool $maintenanceMode = false
    )
    {
    }

    /**
     * @throws \Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\UserException
     * @throws \Chamilo\Libraries\Protocol\Authentication\Architecture\Exception\NotAuthenticatedException
     */
    protected function checkAuthentication(): static
    {
        $application = $this->applicationFactory->getApplicationComponent($this->getContext(), $this->getAction());

        if (!$application instanceof NoAuthenticationSupportInterface) {
            $this->authenticationValidator->validate();
        }

        return $this;
    }

    /**
     * @throws \Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\PlatformNotAvailableException
     */
    protected function checkPlatformAvailability(): static
    {
        if ($this->isMaintenanceMode()) {
            $asAdmin = $this->session->get('_as_admin');

            if ($this->currentUser instanceof User && !$this->currentUser->isPlatformAdministrator() && !$asAdmin) {
                throw new PlatformNotAvailableException();
            }
        }

        return $this;
    }

    protected function configureTimezone(): static
    {
        if ($this->timezone) {
            date_default_timezone_set($this->timezone);
        }

        return $this;
    }

    protected function getAction(): ?string
    {
        $getAction = $this->request->query->get(ApplicationInterface::PARAM_ACTION);

        if ($getAction) {
            return $getAction;
        }

        $postAction = $this->request->request->get(ApplicationInterface::PARAM_ACTION);

        if ($postAction) {
            return $postAction;
        }

        return null;
    }

    protected function getContext(): ?string
    {
        return $this->request->getFromQueryOrRequest(ApplicationInterface::PARAM_CONTEXT, Manager::CONTEXT);
    }

    /**
     * Redirects response of Microsoft OAuth 2.0 Authorization workflow to the component which have called
     * MicrosoftClientService::login(...).
     *
     * @see MicrosoftClientService::login(...)
     */
    protected function handleOAuth2(): static
    {
        $code = $this->request->query->get(self::PARAM_CODE);
        $state = $this->request->query->get(self::PARAM_STATE);
        $sessionState = $this->request->query->get(self::PARAM_SESSION_STATE); // Not provided in OAUTH2 v2.0

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

        $response = new RedirectResponse($this->urlGenerator->fromParameters($landingPageParameters));
        $response->send();
        exit;
    }

    protected function isMaintenanceMode(): bool
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

            $application = $this->applicationFactory->getApplicationComponent($this->getContext(), $this->getAction());
            $this->traceVisit($application);

            $response = $application->run($this->currentUser);
        }
        catch (UserExceptionInterface $exception) {
            $this->exceptionLogger->logException($exception, ExceptionLoggerInterface::EXCEPTION_LEVEL_WARNING);

            $response = new Response($this->userExceptionResponseRenderer->render($exception));
        }

        $this->sendResponse($response);
    }

    protected function sendResponse(Response $response): void
    {
        $response->send();
    }

    protected function traceVisit(ApplicationInterface $application): static
    {
        if (!$application instanceof NoVisitTraceComponentInterface && $this->currentUser instanceof User) {
            $this->eventDispatcher->dispatch(
                new AfterUserEnterPageEvent($this->currentUser, $this->request->getRequestUri())
            );
        }

        return $this;
    }
}
