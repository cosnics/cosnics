<?php
namespace Chamilo\Libraries\Architecture;

use Chamilo\Configuration\Configuration;
use Chamilo\Configuration\Service\ConfigurationConsulter;
use Chamilo\Configuration\Service\FileConfigurationLoader;
use Chamilo\Configuration\Storage\DataClass\Registration;
use Chamilo\Core\Tracking\Storage\DataClass\Event;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Application\ApplicationConfiguration;
use Chamilo\Libraries\Architecture\Application\ApplicationFactory;
use Chamilo\Libraries\Architecture\ErrorHandler\ErrorHandler;
use Chamilo\Libraries\Architecture\ErrorHandler\ExceptionLogger\ExceptionLoggerFactory;
use Chamilo\Libraries\Architecture\ErrorHandler\ExceptionLogger\ExceptionLoggerInterface;
use Chamilo\Libraries\Architecture\Exceptions\NotAuthenticatedException;
use Chamilo\Libraries\Architecture\Exceptions\UserException;
use Chamilo\Libraries\Authentication\Authentication;
use Chamilo\Libraries\Authentication\AuthenticationValidator;
use Chamilo\Libraries\File\PathBuilder;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Format\Response\ExceptionResponse;
use Chamilo\Libraries\Format\Response\NotAuthenticatedResponse;
use Chamilo\Libraries\Format\Response\Response;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Configuration\LocalSetting;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Session\Session;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Cache\DataClassCountCache;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 *
 * @package Chamilo\Libraries\Architecture
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 * @deprecated Use the new \Chamilo\Libraries\Architecture\Factory\KernelFactory now
 */
class Kernel
{
    const PARAM_CODE = 'code';
    const PARAM_STATE = 'state';
    const PARAM_SESSION_STATE = 'session_state';

    /**
     *
     * @var \Chamilo\Libraries\Architecture\Kernel
     */
    protected static $instance = null;

    /**
     *
     * @var \Chamilo\Core\User\Storage\DataClass\User
     */
    private $user;

    /**
     * The namespace of the application we want to launch
     *
     * @var string
     */
    private $context;

    /**
     *
     * @var \Chamilo\Libraries\Architecture\Application\Application
     */
    private $application;

    /**
     *
     * @var \Chamilo\Configuration\Configuration
     */
    private $configuration;

    /**
     *
     * @var \Symfony\Component\HttpFoundation\Request
     */
    private $request;

    /**
     *
     * @var \Chamilo\Libraries\Architecture\Application\ApplicationFactory
     */
    private $applicationFactory;

    /**
     *
     * @var ExceptionLoggerInterface
     */
    protected $exceptionLogger;

    /**
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \Chamilo\Configuration\Configuration $configuration
     */
    public function __construct(\Symfony\Component\HttpFoundation\Request $request, Configuration $configuration)
    {
        $this->request = $request;
        $this->configuration = $configuration;
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
    protected function setUser($user)
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
    protected function setContext($context)
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
    protected function setApplication($application)
    {
        $this->application = $application;
    }

    /**
     *
     * @return \Chamilo\Configuration\Configuration
     */
    public function getConfiguration()
    {
        return $this->configuration;
    }

    /**
     *
     * @param \Chamilo\Configuration\Configuration $configuration
     */
    protected function setConfiguration($configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     *
     * @return \Symfony\Component\HttpFoundation\Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     */
    public function setRequest($request)
    {
        $this->request = $request;
    }

    /**
     *
     * @return \Chamilo\Libraries\Architecture\Kernel
     */
    protected function checkUpgrade()
    {
        $package_info = \Chamilo\Configuration\Package\Storage\DataClass\Package::get('Chamilo\Configuration');
        $registration = \Chamilo\Configuration\Configuration::registration('Chamilo\Configuration');

        if ($package_info->get_version() != $registration[Registration::PROPERTY_VERSION])
        {
            $theme = \Chamilo\Libraries\Platform\Session\Request::get('theme');
            $server_type = \Chamilo\Libraries\Platform\Session\Request::get('server_type');
            $time = \Chamilo\Libraries\Platform\Session\Request::get('time');

            if (! $theme && ! $server_type && ! $time)
            {
                Request::set_get(Application::PARAM_CONTEXT, \Chamilo\Core\Lynx\Manager::context());
                Request::set_get(Application::PARAM_ACTION, \Chamilo\Core\Lynx\Manager::ACTION_UPGRADE);
            }

            $settings = array(
                'platform_language',
                'platform_timezone',
                'institution',
                'site_name',
                'server_type',
                'theme',
                'hide_dcda_markup',
                'session_timeout');

            foreach ($settings as $setting)
            {
                $old_setting = \Chamilo\Libraries\Platform\Configuration\PlatformSetting::get(
                    $setting,
                    'Chamilo\Core\Admin');
                \Chamilo\Libraries\Platform\Configuration\PlatformSetting::set($setting, $old_setting);
            }

            $language_interface = $platform_language = \Chamilo\Libraries\Platform\Configuration\PlatformSetting::get(
                'platform_language');
            $conditions = array();
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(
                    \Chamilo\Configuration\Storage\DataClass\Language::class_name(),
                    \Chamilo\Configuration\Storage\DataClass\Language::PROPERTY_ISOCODE),
                new StaticConditionVariable($platform_language));
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(
                    \Chamilo\Configuration\Storage\DataClass\Language::class_name(),
                    \Chamilo\Configuration\Storage\DataClass\Language::PROPERTY_AVAILABLE),
                new StaticConditionVariable(1));
            $parameters = new DataClassCountParameters(new AndCondition($conditions));
            DataClassCountCache::set_cache(
                \Chamilo\Configuration\Storage\DataClass\Language::class_name(),
                $parameters->hash(),
                1);
        }

        return $this;
    }

    /**
     *
     * @return \Chamilo\Libraries\Architecture\Kernel
     */
    protected function checkAuthentication()
    {
        $applicationClassName = $this->getApplicationFactory()->getClassName();
        $applicationRequiresAuthentication = ! is_subclass_of(
            $applicationClassName,
            'Chamilo\Libraries\Architecture\Interfaces\NoAuthenticationSupport');

        $authenticationValidator = new AuthenticationValidator($this->getRequest(), $this->getConfiguration());

        if ($applicationRequiresAuthentication)
        {
            if (! $authenticationValidator->validate() && ! Authentication::anonymous_user_exists())
            {
                throw new NotAuthenticatedException(true);
            }
        }

        return $this;
    }

    /**
     *
     * @return \Chamilo\Libraries\Architecture\Kernel
     */
    protected function loadUser()
    {
        $user_id = Session::get_user_id();
        if ($user_id)
        {
            $this->user = \Chamilo\Core\User\Storage\DataManager::retrieve_by_id(User::class_name(), $user_id);
        }

        if (! $this->getUser() instanceof User)
        {
            $this->user = Authentication::as_anonymous_user();
        }

        if ($this->getUser() instanceof User)
        {
            $themeSelectionAllowed = $this->getConfiguration()->get_setting(
                array('Chamilo\Core\User', 'allow_user_theme_selection'));

            if ($themeSelectionAllowed)
            {
                Theme::getInstance()->setTheme(LocalSetting::getInstance()->get('theme'));
            }

            $languageSelectionAllowed = $this->getConfiguration()->get_setting(
                array('Chamilo\Core\User', 'allow_user_change_platform_language'));

            if ($languageSelectionAllowed)
            {
                Translation::getInstance()->setLanguageIsocode(LocalSetting::getInstance()->get('platform_language'));
            }
        }

        return $this;
    }

    /**
     *
     * @return \Chamilo\Libraries\Architecture\Kernel
     */
    protected function setup()
    {
        if (! \Chamilo\Configuration\Configuration::get('Chamilo\Configuration', 'debug', 'show_errors'))
        {
            $this->registerErrorHandlers();
        }

        $timezone = \Chamilo\Configuration\Configuration::get('Chamilo\Core\Admin', 'platform_timezone');
        date_default_timezone_set($timezone);

        return $this->configureContext();
    }

    /**
     * Registers the error handler by using the error handler manager
     */
    protected function registerErrorHandlers()
    {
        $errorHandler = new ErrorHandler($this->getExceptionLogger(), Translation::getInstance());
        $errorHandler->registerErrorHandlers();
    }

    /**
     * Returns the exception logger
     *
     * @return ExceptionLoggerInterface
     */
    protected function getExceptionLogger()
    {
        if (! isset($this->exceptionLogger))
        {
            $stringUtilities = new StringUtilities();
            $classnameUtilities = new ClassnameUtilities($stringUtilities);

            $configurationConsulter = new ConfigurationConsulter(
                new FileConfigurationLoader(new PathBuilder($classnameUtilities)));

            $exceptionLoggerFactory = new ExceptionLoggerFactory($configurationConsulter);
            $this->exceptionLogger = $exceptionLoggerFactory->createExceptionLogger();
        }

        return $this->exceptionLogger;
    }

    /**
     *
     * @return \Chamilo\Libraries\Architecture\Kernel
     */
    protected function configureContext()
    {
        $this->context = $this->determineContext();
        $this->context = Application::context_fallback($this->context, $this->getFallbackContexts());

        return $this;
    }

    /**
     * Returns a list of the available fallback contexts
     *
     * @return array
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
     * @return string
     */
    protected function determineContext()
    {
        $getContext = $this->getRequest()->query->get(Application::PARAM_CONTEXT);

        if (! $getContext)
        {
            $postContext = $this->getRequest()->request->get(Application::PARAM_CONTEXT);

            if (! $postContext)
            {
                $this->getRequest()->query->set(Application::PARAM_CONTEXT, 'Chamilo\Core\Home');

                return 'Chamilo\Core\Home';
            }
            else
            {
                return $postContext;
            }
        }
        else
        {
            return $getContext;
        }
    }

    /**
     *
     * @return \Chamilo\Libraries\Architecture\Kernel
     */
    protected function traceVisit()
    {
        $applicationClassName = $this->getApplicationFactory()->getClassName();
        $applicationRequiresTracing = ! is_subclass_of(
            $applicationClassName,
            'Chamilo\Libraries\Architecture\Interfaces\NoVisitTraceComponentInterface');

        if ($applicationRequiresTracing)
        {
            if ($this->getUser() instanceof User)
            {
                Event::trigger(
                    'Online',
                    \Chamilo\Core\Admin\Manager::context(),
                    array('user' => $this->getUser()->get_id()));

                $requestUri = $this->getRequest()->server->get('REQUEST_URI');

                if ($this->getRequest()->query->get(Application::PARAM_CONTEXT) != 'Chamilo\Core\User\Ajax' &&
                     $this->getRequest()->query->get(Application::PARAM_ACTION) != 'LeaveComponent')
                {
                    $return = Event::trigger(
                        'Enter',
                        \Chamilo\Core\User\Manager::context(),
                        array(
                            \Chamilo\Core\User\Integration\Chamilo\Core\Tracking\Storage\DataClass\Visit::PROPERTY_LOCATION => $_SERVER['REQUEST_URI'],
                            \Chamilo\Core\User\Integration\Chamilo\Core\Tracking\Storage\DataClass\Visit::PROPERTY_USER_ID => $this->getUser()->get_id()));
                }
            }
        }

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
     *
     * @return \Chamilo\Libraries\Architecture\Application\ApplicationFactory
     */
    protected function getApplicationFactory()
    {
        return new ApplicationFactory($this->getContext(), $this->getApplicationConfiguration());
    }

    /**
     *
     * @return \Chamilo\Libraries\Architecture\Kernel
     */
    protected function buildApplication()
    {
        $this->application = $this->getApplicationFactory()->getComponent();

        return $this;
    }

    /**
     * Executes the application's component
     */
    protected function runApplication()
    {
        $response = $this->getApplication()->run();

        if (! $response instanceof Response)
        {
            $response = new Response($response);
        }

        $response->send();
    }

    /**
     * Redirects response of Microsoft OAuth 2.0 Authorization workflow to the component which have called
     * MicrosoftClientService::login(...).
     *
     * @return \Chamilo\Libraries\Architecture\Kernel
     *
     * @see MicrosoftClientService::login(...)
     */
    public function handleOAuth2()
    {
        $code = $this->getRequest()->query->get(self::PARAM_CODE);
        $state = $this->getRequest()->query->get(self::PARAM_STATE);
        $session_state = $this->getRequest()->query->get(self::PARAM_SESSION_STATE); // Not provided in OAUTH2 v2.0

        if ($code && $state)
        {
            $stateParameters = (array) unserialize(base64_decode($state));
            $stateParameters[self::PARAM_CODE] = $code;
            if ($session_state)
            {
                $stateParameters[self::PARAM_SESSION_STATE] = $session_state;
            }

            $redirect = new Redirect($stateParameters);
            $redirect->toUrl();
        }

        return $this;
    }

    /**
     * Launch the kernel, executing some common checks, building the application component and executing it
     */
    public function launch()
    {
        try
        {
            if (! $this->getConfiguration()->is_available())
            {
                $this->configureContext();
                $this->buildApplication()->runApplication();
            }
            else
            {
                $this->checkUpgrade()->setup()->handleOAuth2()->checkAuthentication()->loadUser()->buildApplication()->traceVisit()->runApplication();
            }
        }
        catch (NotAuthenticatedException $exception)
        {
            $response = $this->getNotAuthenticatedResponse();
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
     * @return NotAuthenticatedResponse
     */
    protected function getNotAuthenticatedResponse()
    {
        return new NotAuthenticatedResponse();
    }
}
