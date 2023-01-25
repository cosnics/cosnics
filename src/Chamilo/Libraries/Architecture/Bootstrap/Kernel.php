<?php

namespace Chamilo\Libraries\Architecture\Bootstrap;

use Chamilo\Configuration\Service\ConfigurationConsulter;
use Chamilo\Core\Admin\Service\WhoIsOnlineService;
use Chamilo\Core\Tracking\Storage\DataClass\Event;
use Chamilo\Core\User\Integration\Chamilo\Core\Tracking\Storage\DataClass\Visit;
use Chamilo\Core\User\Manager;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Application\ApplicationConfiguration;
use Chamilo\Libraries\Architecture\Application\Routing\UrlGenerator;
use Chamilo\Libraries\Architecture\ErrorHandler\ExceptionLogger\ExceptionLoggerInterface;
use Chamilo\Libraries\Architecture\Exceptions\NotAuthenticatedException;
use Chamilo\Libraries\Architecture\Exceptions\PlatformNotAvailableException;
use Chamilo\Libraries\Architecture\Exceptions\UserException;
use Chamilo\Libraries\Architecture\Factory\ApplicationFactory;
use Chamilo\Libraries\Authentication\AuthenticationValidator;
use Chamilo\Libraries\Format\Response\ExceptionResponse;
use Chamilo\Libraries\Format\Response\NotAuthenticatedResponse;
use Chamilo\Libraries\Format\Response\PlatformNotAvailableResponse;
use Chamilo\Libraries\Format\Structure\PageConfiguration;
use Chamilo\Libraries\Platform\ChamiloRequest;
use Chamilo\Libraries\Platform\Session\SessionUtilities;
use Exception;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * @package Chamilo\Libraries\Architecture\Bootstrap
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 */
class Kernel
{
    public const PARAM_CODE = 'code';
    public const PARAM_SESSION_STATE = 'session_state';
    public const PARAM_STATE = 'state';

    protected AuthenticationValidator $authenticationValidator;

    protected SessionUtilities $sessionUtilities;

    protected WhoIsOnlineService $whoIsOnlineService;

    private ?Application $application = null;

    private ApplicationFactory $applicationFactory;

    private ConfigurationConsulter $configurationConsulter;

    private ?string $context = null;

    private ExceptionLoggerInterface $exceptionLogger;

    private PageConfiguration $pageConfiguration;

    private ChamiloRequest $request;

    private UrlGenerator $urlGenerator;

    private ?User $user;

    public function __construct(
        ChamiloRequest $request, ConfigurationConsulter $configurationConsulter, ApplicationFactory $applicationFactory,
        SessionUtilities $sessionUtilities, ExceptionLoggerInterface $exceptionLogger,
        WhoIsOnlineService $whoIsOnlineService, AuthenticationValidator $authenticationValidator,
        UrlGenerator $urlGenerator, PageConfiguration $pageConfiguration, User $user = null
    )
    {
        $this->request = $request;
        $this->configurationConsulter = $configurationConsulter;
        $this->applicationFactory = $applicationFactory;
        $this->sessionUtilities = $sessionUtilities;
        $this->exceptionLogger = $exceptionLogger;
        $this->urlGenerator = $urlGenerator;
        $this->pageConfiguration = $pageConfiguration;
        $this->user = $user;
        $this->authenticationValidator = $authenticationValidator;
        $this->whoIsOnlineService = $whoIsOnlineService;
    }

    /**
     * @throws \Chamilo\Libraries\Architecture\Exceptions\ClassNotExistException
     * @throws \Exception
     */
    protected function buildApplication(): Kernel
    {
        $context = $this->getContext();

        if (!isset($context))
        {
            throw new Exception('Must call configureContext before buildApplication');
        }

        $this->setApplication(
            $this->getApplicationFactory()->getApplication($this->getContext(), $this->getApplicationConfiguration())
        );

        return $this;
    }

    /**
     * @throws \Chamilo\Libraries\Architecture\Exceptions\ClassNotExistException
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NotAuthenticatedException
     * @throws \Chamilo\Libraries\Authentication\AuthenticationException
     * @throws \Exception
     */
    protected function checkAuthentication(): Kernel
    {
        $applicationClassName = $this->getApplicationFactory()->getClassName(
            $this->getContext(), $this->getApplicationConfiguration()
        );
        $applicationRequiresAuthentication = !is_subclass_of(
            $applicationClassName, 'Chamilo\Libraries\Architecture\Interfaces\NoAuthenticationSupport'
        );

        if ($applicationRequiresAuthentication)
        {
            if (!$this->authenticationValidator->validate())
            {
                throw new NotAuthenticatedException(true);
            }
        }

        return $this;
    }

    /**
     * @throws \Chamilo\Libraries\Architecture\Exceptions\PlatformNotAvailableException
     */
    protected function checkPlatformAvailability(): Kernel
    {
        if ($this->configurationConsulter->getSetting(['Chamilo\Core\Admin', 'maintenance_block_access']))
        {
            $asAdmin = $this->sessionUtilities->get('_as_admin');
            if ($this->getUser() instanceof User && !$this->getUser()->is_platform_admin() && !$asAdmin)
            {
                throw new PlatformNotAvailableException('Platform temporarily unavailable due to maintenance.');
            }
        }

        return $this;
    }

    protected function configureContext(): Kernel
    {
        $getContext = $this->getRequest()->query->get(Application::PARAM_CONTEXT);

        if (!$getContext)
        {
            $postContext = $this->getRequest()->request->get(Application::PARAM_CONTEXT);

            if (!$postContext)
            {
                $this->getRequest()->query->set(Application::PARAM_CONTEXT, 'Chamilo\Core\Home');

                $context = 'Chamilo\Core\Home';
            }
            else
            {
                $context = $postContext;
            }
        }
        else
        {
            $context = $getContext;
        }

        $this->setContext($context);

        return $this;
    }

    protected function configureTimezone(): Kernel
    {
        date_default_timezone_set(
            $this->getConfigurationConsulter()->getSetting(['Chamilo\Libraries\Calendar', 'platform_timezone'])
        );

        return $this;
    }

    public function getApplication(): ?Application
    {
        return $this->application;
    }

    protected function getApplicationConfiguration(): ApplicationConfiguration
    {
        return new ApplicationConfiguration($this->getRequest(), $this->getUser());
    }

    public function getApplicationFactory(): ApplicationFactory
    {
        return $this->applicationFactory;
    }

    public function getConfigurationConsulter(): ConfigurationConsulter
    {
        return $this->configurationConsulter;
    }

    public function getContext(): ?string
    {
        return $this->context;
    }

    public function getExceptionLogger(): ExceptionLoggerInterface
    {
        return $this->exceptionLogger;
    }

    protected function getNotAuthenticatedResponse(): NotAuthenticatedResponse
    {
        return new NotAuthenticatedResponse();
    }

    public function getPageConfiguration(): PageConfiguration
    {
        return $this->pageConfiguration;
    }

    /**
     * @throws \ReflectionException
     */
    protected function getPlatformNotAvailableResponse(): PlatformNotAvailableResponse
    {
        return new PlatformNotAvailableResponse(
            $this->configurationConsulter->getSetting(
                ['Chamilo\Core\Admin', 'maintenance_warning_message']
            ), $this->getApplication()
        );
    }

    public function getRequest(): ChamiloRequest
    {
        return $this->request;
    }

    public function getUrlGenerator(): UrlGenerator
    {
        return $this->urlGenerator;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function getWhoIsOnlineService(): WhoIsOnlineService
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

        if (!$code || !$state)
        {
            return null;
        }
        $decodedState = base64_decode($state);

        if (!$decodedState)
        {
            return null;
        }

        $stateParameters = json_decode($decodedState, true);

        if (!is_array($stateParameters) || !array_key_exists('landingPageParameters', $stateParameters))
        {
            return null;
        }

        $landingPageParameters = $stateParameters['landingPageParameters'];
        $landingPageParameters[self::PARAM_CODE] = $code;

        unset($stateParameters['landingPageParameters']);

        $landingPageParameters[self::PARAM_STATE] = base64_encode(json_encode($stateParameters));

        if ($session_state)
        {
            $landingPageParameters[self::PARAM_SESSION_STATE] = $session_state;
        }

        return new RedirectResponse($this->getUrlGenerator()->fromParameters($landingPageParameters));
    }

    /**
     * @throws \Exception
     */
    public function launch()
    {
        try
        {
            $this->configureTimezone()->configureContext();

            $response = $this->handleOAuth2();

            if (!$response instanceof RedirectResponse)
            {
                $response = $this->checkAuthentication()->checkPlatformAvailability()->buildApplication()->traceVisit()
                    ->runApplication();
            }
        }
        catch (NotAuthenticatedException $exception)
        {
            $response = $this->getNotAuthenticatedResponse();
        }
        catch (PlatformNotAvailableException $exception)
        {
            $response = $this->getPlatformNotAvailableResponse();
        }
        catch (UserException $exception)
        {
            $this->getExceptionLogger()->logException($exception, ExceptionLoggerInterface::EXCEPTION_LEVEL_WARNING);

            $response = new ExceptionResponse($exception, $this->getApplication());
        }

        $this->sendResponse($response);
    }

    /**
     * @throws \Exception
     */
    protected function runApplication()
    {
        $application = $this->getApplication();

        if (!isset($application))
        {
            throw new Exception('Must call buildApplication before runApplication');
        }

        $response = $application->run();

        if (!$response instanceof Response)
        {
            $response = new Response($response);
        }

        return $response;
    }

    protected function sendResponse(Response $response)
    {
        $response->send();
    }

    public function setApplication(Application $application)
    {
        $this->application = $application;
    }

    public function setApplicationFactory(ApplicationFactory $applicationFactory)
    {
        $this->applicationFactory = $applicationFactory;
    }

    public function setConfigurationConsulter(ConfigurationConsulter $configurationConsulter)
    {
        $this->configurationConsulter = $configurationConsulter;
    }

    public function setContext(string $context)
    {
        $this->context = $context;
    }

    public function setExceptionLogger(ExceptionLoggerInterface $exceptionLogger)
    {
        $this->exceptionLogger = $exceptionLogger;
    }

    public function setRequest(ChamiloRequest $request)
    {
        $this->request = $request;
    }

    public function setUrlGenerator(UrlGenerator $urlGenerator): Kernel
    {
        $this->urlGenerator = $urlGenerator;

        return $this;
    }

    public function setUser(User $user)
    {
        $this->user = $user;
    }

    public function setWhoIsOnlineService(WhoIsOnlineService $whoIsOnlineService): Kernel
    {
        $this->whoIsOnlineService = $whoIsOnlineService;

        return $this;
    }

    /**
     * @throws \Chamilo\Libraries\Architecture\Exceptions\ClassNotExistException
     * @throws \Exception
     */
    protected function traceVisit(): Kernel
    {
        $applicationClassName = $this->getApplicationFactory()->getClassName(
            $this->getContext(), $this->getApplicationConfiguration()
        );
        $applicationRequiresTracing = !is_subclass_of(
            $applicationClassName, 'Chamilo\Libraries\Architecture\Interfaces\NoVisitTraceComponentInterface'
        );

        if ($applicationRequiresTracing)
        {
            if ($this->getUser() instanceof User)
            {
                $this->getWhoIsOnlineService()->updateWhoIsOnlineForUserIdentifierWithCurrentTime(
                    $this->getUser()->getId()
                );

                if ($this->getRequest()->query->get(Application::PARAM_CONTEXT) != 'Chamilo\Core\User\Ajax' &&
                    $this->getRequest()->query->get(Application::PARAM_ACTION) != 'LeaveComponent')
                {
                    Event::trigger(
                        'Enter', Manager::context(), [
                            Visit::PROPERTY_LOCATION => $_SERVER['REQUEST_URI'],
                            Visit::PROPERTY_USER_ID => $this->getUser()->getId()
                        ]
                    );
                }
            }
        }

        return $this;
    }
}
