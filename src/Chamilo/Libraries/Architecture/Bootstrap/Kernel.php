<?php

namespace Chamilo\Libraries\Architecture\Bootstrap;

use Chamilo\Configuration\Service\ConfigurationConsulter;
use Chamilo\Core\Tracking\Storage\DataClass\Event;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Application\ApplicationConfiguration;
use Chamilo\Libraries\Architecture\ErrorHandler\ExceptionLogger\ExceptionLoggerInterface;
use Chamilo\Libraries\Architecture\Exceptions\NotAuthenticatedException;
use Chamilo\Libraries\Architecture\Exceptions\PlatformNotAvailableException;
use Chamilo\Libraries\Architecture\Exceptions\UserException;
use Chamilo\Libraries\Architecture\Factory\ApplicationFactory;
use Chamilo\Libraries\Authentication\AuthenticationValidator;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Format\Response\ExceptionResponse;
use Chamilo\Libraries\Format\Response\NotAuthenticatedResponse;
use Chamilo\Libraries\Format\Structure\Page;
use Chamilo\Libraries\Platform\Session\SessionUtilities;
use Symfony\Component\HttpFoundation\Response;

/**
 *
 * @package Chamilo\Libraries\Architecture\Bootstrap
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 */
class Kernel
{
    const PARAM_CODE = 'code';
    const PARAM_STATE = 'state';
    const PARAM_SESSION_STATE = 'session_state';

    /**
     *
     * @var \Chamilo\Libraries\Platform\ChamiloRequest
     */
    private $request;

    /**
     *
     * @var \Chamilo\Libraries\Architecture\ErrorHandler\ExceptionLogger\ExceptionLoggerInterface
     */
    private $exceptionLogger;

    /**
     *
     * @var \Chamilo\Libraries\Architecture\Factory\ApplicationFactory
     */
    private $applicationFactory;

    /**
     *
     * @var \Chamilo\Configuration\Service\ConfigurationConsulter
     */
    private $configurationConsulter;

    /**
     *
     * @var string
     */
    private $context;

    /**
     *
     * @var integer
     */
    private $version;

    /**
     *
     * @var \Chamilo\Libraries\Architecture\Application\Application
     */
    private $application;

    /**
     *
     * @var \Chamilo\Libraries\Platform\Session\SessionUtilities
     */
    protected $sessionUtilities;

    /**
     *
     * @var \Chamilo\Core\User\Storage\DataClass\User
     */
    private $user;

    /**
     * @var \Chamilo\Libraries\Authentication\AuthenticationValidator
     */
    protected $authenticationValidator;

    /**
     *
     * @param \Chamilo\Libraries\Platform\ChamiloRequest $request
     * @param \Chamilo\Configuration\Service\ConfigurationConsulter $configurationConsulter
     * @param \Chamilo\Libraries\Architecture\Factory\ApplicationFactory $applicationFactory
     * @param \Chamilo\Libraries\Platform\Session\SessionUtilities $sessionUtilities
     * @param \Chamilo\Libraries\Architecture\ErrorHandler\ExceptionLogger\ExceptionLoggerInterface $exceptionLogger
     * @param \Chamilo\Libraries\Authentication\AuthenticationValidator $authenticationValidator
     * @param integer $version
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     */
    public function __construct(
        \Chamilo\Libraries\Platform\ChamiloRequest $request,
        ConfigurationConsulter $configurationConsulter, ApplicationFactory $applicationFactory,
        SessionUtilities $sessionUtilities, ExceptionLoggerInterface $exceptionLogger,
        AuthenticationValidator $authenticationValidator, $version, User $user = null
    )
    {
        $this->request = $request;
        $this->configurationConsulter = $configurationConsulter;
        $this->applicationFactory = $applicationFactory;
        $this->sessionUtilities = $sessionUtilities;
        $this->exceptionLogger = $exceptionLogger;
        $this->version = $version;
        $this->user = $user;
        $this->authenticationValidator = $authenticationValidator;
    }

    /**
     *
     * @return \Chamilo\Libraries\Platform\ChamiloRequest
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     *
     * @param \Chamilo\Libraries\Platform\ChamiloRequest $request
     */
    public function setRequest($request)
    {
        $this->request = $request;
    }

    /**
     *
     * @return \Chamilo\Libraries\Architecture\Factory\ApplicationFactory
     */
    public function getApplicationFactory()
    {
        return $this->applicationFactory;
    }

    /**
     *
     * @param \Chamilo\Libraries\Architecture\Factory\ApplicationFactory $applicationFactory
     */
    public function setApplicationFactory(ApplicationFactory $applicationFactory)
    {
        $this->applicationFactory = $applicationFactory;
    }

    /**
     *
     * @return \Chamilo\Configuration\Service\ConfigurationConsulter
     */
    public function getConfigurationConsulter()
    {
        return $this->configurationConsulter;
    }

    /**
     *
     * @param \Chamilo\Configuration\Service\ConfigurationConsulter $configurationConsulter
     */
    public function setConfigurationConsulter(ConfigurationConsulter $configurationConsulter)
    {
        $this->configurationConsulter = $configurationConsulter;
    }

    /**
     *
     * @return \Chamilo\Libraries\Architecture\ErrorHandler\ExceptionLogger\ExceptionLoggerInterface
     */
    public function getExceptionLogger()
    {
        return $this->exceptionLogger;
    }

    /**
     *
     * @param \Chamilo\Libraries\Architecture\ErrorHandler\ExceptionLogger\ExceptionLoggerInterface $exceptionLogger
     */
    public function setExceptionLogger(ExceptionLoggerInterface $exceptionLogger)
    {
        $this->exceptionLogger = $exceptionLogger;
    }

    /**
     *
     * @return \Chamilo\Core\User\Storage\DataClass\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     */
    public function setUser(User $user)
    {
        $this->user = $user;
    }

    /**
     *
     * @return string
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     *
     * @param string $context
     */
    public function setContext($context)
    {
        $this->context = $context;
    }

    /**
     *
     * @return \Chamilo\Libraries\Architecture\Application\Application
     */
    public function getApplication()
    {
        return $this->application;
    }

    /**
     *
     * @param \Chamilo\Libraries\Architecture\Application\Application $application
     */
    public function setApplication(Application $application)
    {
        $this->application = $application;
    }

    /**
     *
     * @return integer
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     *
     * @param integer $version
     */
    public function setVersion($version)
    {
        $this->version = $version;
    }

    public function launch()
    {
        try
        {
            $this->configureTimeZone()->configureContext()->handleOAuth2()->checkAuthentication()
                ->checkPlatformAvailability()->buildApplication()->traceVisit()->runApplication();
        }
        catch (NotAuthenticatedException $exception)
        {
            $response = $this->getNotAuthenticatedResponse();
            $response->send();
        }
        catch (PlatformNotAvailableException $exception)
        {
            $page = Page::getInstance();
            $page->setApplication($this->getApplication());

            $html = array();
            $html[] = $page->getHeader()->toHtml();
            $html[] = '<br />';
            $html[] = '<div class="alert alert-danger text-center">';
            $html[] = $this->configurationConsulter->getSetting(
                ['Chamilo\Core\Admin', 'maintenance_warning_message']
            );
            $html[] = '</div>';
            $html[] = $page->getFooter()->toHtml();

            $response = new Response(implode("\n", $html));
            $response->send();
        }
        catch (UserException $exception)
        {
            $this->getExceptionLogger()->logException($exception, ExceptionLoggerInterface::EXCEPTION_LEVEL_WARNING);

            $response = new ExceptionResponse($exception, $this->getApplication());
            $response->send();
        }
    }

    /**
     * Returns a response that renders the not authenticated message
     *
     * @return \Chamilo\Libraries\Format\Response\NotAuthenticatedResponse
     */
    protected function getNotAuthenticatedResponse()
    {
        return new NotAuthenticatedResponse();
    }

    /**
     *
     * @return \Chamilo\Libraries\Architecture\Bootstrap\Kernel
     */
    protected function configureContext()
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

        $this->setContext(Application::context_fallback($context, $this->getFallbackContexts()));

        return $this;
    }

    /**
     * Returns a list of the available fallback contexts
     *
     * @return string[]
     */
    protected function getFallbackContexts()
    {
        $fallbackContexts = array();
        $fallbackContexts[] = 'Chamilo\Application\\';
        $fallbackContexts[] = 'Chamilo\Core\\';
        $fallbackContexts[] = 'Chamilo\\';

        return $fallbackContexts;
    }

    /**
     *
     * @return \Chamilo\Libraries\Architecture\Bootstrap\Kernel
     */
    protected function buildApplication()
    {
        $context = $this->getContext();

        if (!isset($context))
        {
            throw new \Exception('Must call configureContext before buildApplication');
        }

        $this->setApplication(
            $this->getApplicationFactory()->getApplication($this->getContext(), $this->getApplicationConfiguration())
        );

        return $this;
    }

    /**
     *
     * @return \Chamilo\Libraries\Architecture\Application\ApplicationConfiguration
     */
    protected function getApplicationConfiguration()
    {
        return new ApplicationConfiguration($this->getRequest(), $this->getUser());
    }

    /**
     * Executes the application's component
     *
     * @throws \Exception
     */
    protected function runApplication()
    {
        $application = $this->getApplication();

        if (!isset($application))
        {
            throw new \Exception('Must call buildApplication before runApplication');
        }

        $response = $application->run();

        if (!$response instanceof Response)
        {
            $response = new \Chamilo\Libraries\Format\Response\Response($this->getVersion(), $response);
        }

        $this->sendResponse($response);
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Response $response
     */
    protected function sendResponse(Response $response)
    {
        $response->send();
    }

    /**
     * Checks if the platform is available for the given user
     *
     * @return \Chamilo\Libraries\Architecture\Bootstrap\Kernel
     * @throws \Chamilo\Libraries\Architecture\Exceptions\PlatformNotAvailableException
     */
    protected function checkPlatformAvailability()
    {
        if ($this->configurationConsulter->getSetting(['Chamilo\Core\Admin', 'maintenance_block_access']))
        {
            $asAdmin = $this->sessionUtilities->get('_as_admin');
            if ($this->getUser() instanceof User && !$this->getUser()->is_platform_admin() && !$asAdmin)
            {
                throw new PlatformNotAvailableException();
            }
        }

        return $this;
    }

    /**
     *
     * @return \Chamilo\Libraries\Architecture\Bootstrap\Kernel
     */
    protected function configureTimezone()
    {
        date_default_timezone_set(
            $this->getConfigurationConsulter()->getSetting(array('Chamilo\Libraries\Calendar', 'platform_timezone'))
        );

        return $this;
    }

    /**
     * Redirects response of Microsoft OAuth 2.0 Authorization workflow to the component which have called
     * MicrosoftClientService::login(...).
     *
     * @return \Chamilo\Libraries\Architecture\Bootstrap\Kernel
     *
     * @see MicrosoftClientService::login(...)
     */
    public function handleOAuth2()
    {
        $code = $this->getRequest()->query->get(self::PARAM_CODE);
        $state = $this->getRequest()->query->get(self::PARAM_STATE);
        $session_state = $this->getRequest()->query->get(self::PARAM_SESSION_STATE); // Not provided in OAUTH2 v2.0

        if (!$code || !$state)
        {
            return $this;
        }
        $decodedState = base64_decode($state);

        if (!$decodedState)
        {
            return $this;
        }

        $stateParameters = json_decode($decodedState, true);

        if (!is_array($stateParameters) || !array_key_exists('landingPageParameters', $stateParameters))
        {
            return $this;
        }

        $landingPageParameters = $stateParameters['landingPageParameters'];
        $landingPageParameters[self::PARAM_CODE] = $code;

        unset($stateParameters['landingPageParameters']);

        $landingPageParameters[self::PARAM_STATE] = base64_encode(json_encode($stateParameters));

        if ($session_state)
        {
            $landingPageParameters[self::PARAM_SESSION_STATE] = $session_state;
        }

        $redirect = new Redirect($landingPageParameters);
        $redirect->toUrl();

        return $this;
    }

    /**
     *
     * @return \Chamilo\Libraries\Architecture\Bootstrap\Kernel
     * @throws \Chamilo\Libraries\Architecture\Exceptions\ClassNotExistException
     *
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NotAuthenticatedException
     * @throws \Chamilo\Libraries\Authentication\AuthenticationException
     * @throws \Exception
     */
    protected function checkAuthentication()
    {
        $applicationClassName = $this->getApplicationFactory()->getClassName(
            $this->getContext(),
            $this->getApplicationConfiguration()
        );
        $applicationRequiresAuthentication = !is_subclass_of(
            $applicationClassName,
            'Chamilo\Libraries\Architecture\Interfaces\NoAuthenticationSupport'
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
     *
     * @return \Chamilo\Libraries\Architecture\Bootstrap\Kernel
     * @throws \Chamilo\Libraries\Architecture\Exceptions\ClassNotExistException
     * @throws \Exception
     */
    protected function traceVisit()
    {
        $applicationClassName = $this->getApplicationFactory()->getClassName(
            $this->getContext(),
            $this->getApplicationConfiguration()
        );
        $applicationRequiresTracing = !is_subclass_of(
            $applicationClassName,
            'Chamilo\Libraries\Architecture\Interfaces\NoVisitTraceComponentInterface'
        );

        if ($applicationRequiresTracing)
        {
            if ($this->getUser() instanceof User)
            {
                Event::trigger(
                    'Online',
                    \Chamilo\Core\Admin\Manager::context(),
                    array('user' => $this->getUser()->get_id())
                );

                $requestUri = $this->getRequest()->server->get('REQUEST_URI');

                if ($this->getRequest()->query->get(Application::PARAM_CONTEXT) != 'Chamilo\Core\User\Ajax' &&
                    $this->getRequest()->query->get(Application::PARAM_ACTION) != 'LeaveComponent')
                {
                    $return = Event::trigger(
                        'Enter',
                        \Chamilo\Core\User\Manager::context(),
                        array(
                            \Chamilo\Core\User\Integration\Chamilo\Core\Tracking\Storage\DataClass\Visit::PROPERTY_LOCATION => $_SERVER['REQUEST_URI'],
                            \Chamilo\Core\User\Integration\Chamilo\Core\Tracking\Storage\DataClass\Visit::PROPERTY_USER_ID => $this->getUser(
                            )->get_id()
                        )
                    );
                }
            }
        }

        return $this;
    }
}
