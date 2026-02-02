<?php
namespace Chamilo\Libraries\Service\Bootstrap;

use Chamilo\Core\Admin\Service\Consulter\ConfigurationConsulter;
use Chamilo\Core\Admin\Service\OnlineService;
use Chamilo\Core\Home\Manager as HomeManager;
use Chamilo\Core\User\Architecture\EventDispatcher\Event\AfterUserEnterPageEvent;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Domain\Application;
use Chamilo\Libraries\Architecture\Exception\PlatformNotAvailableException;
use Chamilo\Libraries\Architecture\Exception\UserException;
use Chamilo\Libraries\Architecture\Interface\NoVisitTraceComponentInterface;
use Chamilo\Libraries\Architecture\Response\PlatformNotAvailableResponse;
use Chamilo\Libraries\Platform\ChamiloRequest;
use Chamilo\Libraries\Protocol\Authentication\Architecture\Exception\NotAuthenticatedException;
use Chamilo\Libraries\Protocol\Authentication\Architecture\Response\NotAuthenticatedResponse;
use Chamilo\Libraries\Protocol\Authentication\Service\AuthenticationValidator;
use Chamilo\Libraries\Protocol\Error\Architecture\Interface\ExceptionLoggerInterface;
use Chamilo\Libraries\Protocol\Error\Architecture\Response\ExceptionResponse;
use Chamilo\Libraries\Service\Routing\UrlGenerator;
use Exception;
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

    protected SessionInterface $session;

    protected OnlineService $whoIsOnlineService;

    private ?Application $application = null;

    private ApplicationFactory $applicationFactory;

    private ConfigurationConsulter $configurationConsulter;

    private ?string $context = null;

    private ExceptionLoggerInterface $exceptionLogger;

    private ChamiloRequest $request;

    private UrlGenerator $urlGenerator;

    private ?User $user;

    public function __construct(
        ChamiloRequest $request, ConfigurationConsulter $configurationConsulter, ApplicationFactory $applicationFactory,
        SessionInterface $session, ExceptionLoggerInterface $exceptionLogger, OnlineService $whoIsOnlineService,
        AuthenticationValidator $authenticationValidator, UrlGenerator $urlGenerator,
        EventDispatcherInterface $eventDispatcher, User $user = null
    )
    {
        $this->request = $request;
        $this->configurationConsulter = $configurationConsulter;
        $this->applicationFactory = $applicationFactory;
        $this->session = $session;
        $this->exceptionLogger = $exceptionLogger;
        $this->urlGenerator = $urlGenerator;
        $this->user = $user;
        $this->authenticationValidator = $authenticationValidator;
        $this->whoIsOnlineService = $whoIsOnlineService;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @throws \Chamilo\Libraries\Architecture\Exception\ClassNotExistException
     * @throws \Exception
     */
    protected function buildApplication(): Kernel
    {
        $context = $this->getContext();

        if (!isset($context)) {
            throw new Exception('Must call configureContext before buildApplication');
        }

        $this->setApplication(
            $this->getApplicationFactory()->getApplication($this->getContext(),  $this->getUser())
        );

        return $this;
    }

    /**
     * @throws \Chamilo\Libraries\Architecture\Exception\ClassNotExistException
     * @throws \Chamilo\Libraries\Protocol\Authentication\Architecture\Exception\NotAuthenticatedException
     * @throws \Chamilo\Libraries\Protocol\Authentication\Architecture\Exception\AuthenticationException
     * @throws \Exception
     */
    protected function checkAuthentication(): Kernel
    {
        $applicationClassName = $this->getApplicationFactory()->getClassName($this->getContext());
        $applicationRequiresAuthentication = !is_subclass_of(
            $applicationClassName,
            'Chamilo\Libraries\Protocol\Authentication\Architecture\Interface\NoAuthenticationSupportInterface'
        );

        if ($applicationRequiresAuthentication) {
            if (!$this->getAuthenticationValidator()->validate()) {
                throw new NotAuthenticatedException(true);
            }
        }

        return $this;
    }

    /**
     * @throws \Chamilo\Libraries\Architecture\Exception\PlatformNotAvailableException
     */
    protected function checkPlatformAvailability(): Kernel
    {
        if ($this->getConfigurationConsulter()->getSetting(['Chamilo\Core\Admin', 'maintenance_block_access'])) {
            $asAdmin = $this->getSession()->get('_as_admin');

            if ($this->getUser() instanceof User && !$this->getUser()->isPlatformAdministrator() && !$asAdmin) {
                throw new PlatformNotAvailableException('Platform temporarily unavailable due to maintenance.');
            }
        }

        return $this;
    }

    protected function configureContext(): Kernel
    {
        $getContext = $this->getRequest()->query->get(Application::PARAM_CONTEXT);

        if (!$getContext) {
            $postContext = $this->getRequest()->request->get(Application::PARAM_CONTEXT);

            if (!$postContext) {
                $this->getRequest()->query->set(Application::PARAM_CONTEXT, 'Chamilo\Core\Home');

                $context = 'Chamilo\Core\Home';
            }
            else {
                $context = $postContext;
            }
        }
        else {
            $context = $getContext;
        }

        $this->setContext($context);

        return $this;
    }

    protected function configureTimezone(): Kernel
    {
        date_default_timezone_set(
            $this->getConfigurationConsulter()->getSetting(['Chamilo\Core\Admin', 'platform_timezone'])
        );

        return $this;
    }

    public function getApplication(): ?Application
    {
        return $this->application;
    }

    public function setApplication(Application $application): void
    {
        $this->application = $application;
    }

    public function getApplicationFactory(): ApplicationFactory
    {
        return $this->applicationFactory;
    }

    public function getAuthenticationValidator(): AuthenticationValidator
    {
        return $this->authenticationValidator;
    }

    public function getConfigurationConsulter(): ConfigurationConsulter
    {
        return $this->configurationConsulter;
    }

    public function getContext(): ?string
    {
        if (!isset($this->context)) {
            $this->context =
                $this->getRequest()->getFromRequestOrQuery(Application::PARAM_CONTEXT, HomeManager::CONTEXT);
        }

        return $this->context;
    }

    public function setContext(string $context): void
    {
        $this->context = $context;
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
        return new PlatformNotAvailableResponse(
            $this->configurationConsulter->getSetting(
                ['Chamilo\Core\Admin', 'maintenance_warning_message']
            )
        );
    }

    public function getRequest(): ChamiloRequest
    {
        return $this->request;
    }

    public function getSession(): SessionInterface
    {
        return $this->session;
    }

    public function getUrlGenerator(): UrlGenerator
    {
        return $this->urlGenerator;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function getWhoIsOnlineService(): OnlineService
    {
        return $this->whoIsOnlineService;
    }

    /**
     * Redirects response of Microsoft OAuth 2.0 Authorization workflow to the component which have called
     * MicrosoftClientService::login(...).
     *
     * @see MicrosoftClientService::login(...)
     */
    public function handleOAuth2(): ?RedirectResponse
    {
        $code = $this->getRequest()->query->get(self::PARAM_CODE);
        $state = $this->getRequest()->query->get(self::PARAM_STATE);
        $session_state = $this->getRequest()->query->get(self::PARAM_SESSION_STATE); // Not provided in OAUTH2 v2.0

        if (!$code || !$state) {
            return null;
        }
        $decodedState = base64_decode($state);

        if (!$decodedState) {
            return null;
        }

        $stateParameters = json_decode($decodedState, true);

        if (!is_array($stateParameters) || !array_key_exists('landingPageParameters', $stateParameters)) {
            return null;
        }

        $landingPageParameters = $stateParameters['landingPageParameters'];
        $landingPageParameters[self::PARAM_CODE] = $code;

        unset($stateParameters['landingPageParameters']);

        $landingPageParameters[self::PARAM_STATE] = base64_encode(json_encode($stateParameters));

        if ($session_state) {
            $landingPageParameters[self::PARAM_SESSION_STATE] = $session_state;
        }

        $response = new RedirectResponse($this->getUrlGenerator()->fromParameters($landingPageParameters));
        $response->send();
        exit;
    }

    /**
     * @throws \Exception
     */
    public function launch(): void
    {
        try {
            $this->configureTimezone()->configureContext()->handleOAuth2();
            $response = $this->checkAuthentication()->checkPlatformAvailability()->buildApplication()->traceVisit()
                ->runApplication();
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

    /**
     * @throws \Exception
     */
    protected function runApplication(): Response
    {
        $application = $this->getApplication();

        if (!isset($application)) {
            throw new Exception('Must call buildApplication before runApplication');
        }

        return $application->run();
    }

    protected function sendResponse(Response $response): void
    {
        $response->send();
    }

    /**
     * @throws \Chamilo\Libraries\Architecture\Exception\ClassNotExistException
     * @throws \Exception
     */
    protected function traceVisit(): Kernel
    {
        $applicationClassName = $this->getApplicationFactory()->getClassName($this->getContext());
        $applicationRequiresTracing = !is_subclass_of(
            $applicationClassName, NoVisitTraceComponentInterface::class
        );

        if ($applicationRequiresTracing && $this->getUser() instanceof User) {
            $this->getEventDispatcher()->dispatch(
                new AfterUserEnterPageEvent($this->getUser(), $this->getRequest()->getRequestUri())
            );
        }

        return $this;
    }
}
