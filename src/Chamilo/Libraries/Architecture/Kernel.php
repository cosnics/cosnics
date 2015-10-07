<?php
namespace Chamilo\Libraries\Architecture;

use Chamilo\Core\Tracking\Storage\DataClass\Event;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Core\User\Storage\DataClass\UserLoginSession;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Application\ApplicationFactory;
use Chamilo\Libraries\Authentication\Authentication;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Platform\Configuration\PlatformSetting;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Session\Session;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Cache\DataClassCountCache;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Architecture\Interfaces\NoAuthenticationSupport;
use Chamilo\Configuration\Configuration;
use Chamilo\Libraries\Authentication\AuthenticationValidator;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Format\Response\ExceptionResponse;
use Chamilo\Libraries\Format\Response\Response;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Configuration\LocalSetting;
use Chamilo\Libraries\Architecture\Application\ApplicationConfiguration;
use Chamilo\Libraries\Utilities\Utilities;
use Chamilo\Libraries\Architecture\Interfaces\NoVisitTraceComponentInterface;

/**
 *
 * @package Chamilo\Libraries\Architecture
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class Kernel
{
    const PARAM_CODE = 'code';
    const PARAM_SESSION_STATE = 'session_state';
    const PARAM_STATE = 'state';

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
    private function setUser($user)
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
    private function setContext($context)
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
    private function setApplication($application)
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
    private function setConfiguration($configuration)
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
    private function checkUpgrade()
    {
        $package_info = \Chamilo\Configuration\Package\Storage\DataClass\Package :: get('Chamilo\Configuration');
        $registration = \Chamilo\Configuration\Configuration :: registration('Chamilo\Configuration');

        if ($package_info->get_version() != $registration->get_version())
        {
            $theme = \Chamilo\Libraries\Platform\Session\Request :: get('theme');
            $server_type = \Chamilo\Libraries\Platform\Session\Request :: get('server_type');
            $time = \Chamilo\Libraries\Platform\Session\Request :: get('time');

            if (! $theme && ! $server_type && ! $time)
            {
                Request :: set_get(Application :: PARAM_CONTEXT, \Chamilo\Core\Lynx\Manager :: context());
                Request :: set_get(Application :: PARAM_ACTION, \Chamilo\Core\Lynx\Manager :: ACTION_UPGRADE);
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
                $old_setting = \Chamilo\Libraries\Platform\Configuration\PlatformSetting :: get(
                    $setting,
                    'Chamilo\Core\Admin');
                \Chamilo\Libraries\Platform\Configuration\PlatformSetting :: set($setting, $old_setting);
            }

            $language_interface = $platform_language = \Chamilo\Libraries\Platform\Configuration\PlatformSetting :: get(
                'platform_language');
            $conditions = array();
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(
                    \Chamilo\Configuration\Storage\DataClass\Language :: class_name(),
                    \Chamilo\Configuration\Storage\DataClass\Language :: PROPERTY_ISOCODE),
                new StaticConditionVariable($platform_language));
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(
                    \Chamilo\Configuration\Storage\DataClass\Language :: class_name(),
                    \Chamilo\Configuration\Storage\DataClass\Language :: PROPERTY_AVAILABLE),
                new StaticConditionVariable(1));
            $parameters = new DataClassCountParameters(new AndCondition($conditions));
            DataClassCountCache :: set_cache(
                \Chamilo\Configuration\Storage\DataClass\Language :: class_name(),
                $parameters->hash(),
                1);
        }
        return $this;
    }

    /**
     *
     * @return \Chamilo\Libraries\Architecture\Kernel
     */
    private function checkMaintenance()
    {
        if (\Chamilo\Libraries\Platform\Configuration\PlatformSetting :: get('maintenance_mode', 'Chamilo\Core\Admin'))
        {
            throw new \Exception(Translation :: get('MaintenanceMessage'));
        }

        return $this;
    }

    /**
     *
     * @return \Chamilo\Libraries\Architecture\Kernel
     */
    private function checkAuthentication()
    {
        if (! $this->getApplication() instanceof Application)
        {
            throw new \Exception(
                'No application available to check the authentication. Please call Kernel::buildApplication() before calling Kernel::checkAuthentication()');
        }
        else
        {
            $authenticationValidator = new AuthenticationValidator($this->getRequest(), $this->getConfiguration());

            if (! $this->getApplication() instanceof NoAuthenticationSupport && ! $authenticationValidator->validate() &&
                 ! Authentication :: anonymous_user_exists())
            {
                throw new NotAllowedException();
            }

            return $this;
        }
    }

    /**
     *
     * @return \Chamilo\Libraries\Architecture\Kernel
     */
    private function loadUser()
    {
        $user_id = Session :: get_user_id();
        if ($user_id)
        {
            $this->user = \Chamilo\Core\User\Storage\DataManager :: retrieve_by_id(User :: CLASS_NAME, $user_id);
        }

        if (! $this->getUser() instanceof User)
        {
            $this->user = Authentication :: as_anonymous_user();
        }

        if ($this->getUser() instanceof User)
        {
            $themeSelectionAllowed = $this->getConfiguration()->get_setting(
                array('Chamilo\Core\User', 'allow_user_theme_selection'));

            if ($themeSelectionAllowed)
            {
                Theme :: getInstance()->setTheme(LocalSetting :: get('theme'));
            }

            $languageSelectionAllowed = $this->getConfiguration()->get_setting(
                array('Chamilo\Core\User', 'allow_user_change_platform_language'));

            if ($languageSelectionAllowed)
            {
                Translation :: getInstance()->setLanguageIsocode(LocalSetting :: get('platform_language'));
            }
        }

        return $this;
    }

    /**
     *
     * @return \Chamilo\Libraries\Architecture\Kernel
     */
    private function setup()
    {
        if (\Chamilo\Configuration\Configuration :: get('Chamilo\Configuration', 'debug', 'show_errors'))
        {
            set_exception_handler('\Chamilo\Libraries\Utilities\Utilities::handle_exception');
            set_error_handler('\Chamilo\Libraries\Utilities\Utilities::handle_error');
        }

        $timezone = \Chamilo\Configuration\Configuration :: get('Chamilo\Core\Admin', 'platform_timezone');
        date_default_timezone_set($timezone);

        return $this->configureContext();
    }

    /**
     *
     * @return \Chamilo\Libraries\Architecture\Kernel
     */
    private function configureContext()
    {
        $this->context = $this->determineContext();
        $this->context = Application :: context_fallback($this->context);

        return $this;
    }

    /**
     *
     * @return string
     */
    private function determineContext()
    {
        $getContext = $this->getRequest()->query->get(Application :: PARAM_CONTEXT);

        if (! $getContext)
        {
            $postContext = $this->getRequest()->request->get(Application :: PARAM_CONTEXT);

            if (! $postContext)
            {
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
    private function displayTerms()
    {
        $already_viewed = \Chamilo\Libraries\Platform\Session\Session :: get('terms_and_conditions_viewed');
        $is_terms_component = $this->getRequest()->query->get(Application :: PARAM_CONTEXT) ==
             \Chamilo\Core\User\Manager :: context() && $this->getRequest()->query->get(Application :: PARAM_ACTION) ==
             \Chamilo\Core\User\Manager :: ACTION_VIEW_TERMSCONDITIONS;
        $terms_enabled = $this->getConfiguration()->get_setting(
            array(\Chamilo\Core\User\Manager :: context(), 'enable_terms_and_conditions'));

        if ($terms_enabled && ! $is_terms_component && ! $this->getUser()->terms_conditions_uptodate() &&
             ! $already_viewed)
        {
            \Chamilo\Libraries\Platform\Session\Session :: register('terms_and_conditions_viewed', true);

            $redirect = new Redirect(
                array(
                    Application :: PARAM_CONTEXT => \Chamilo\Core\User\Manager :: context(),
                    Application :: PARAM_ACTION => \Chamilo\Core\User\Manager :: ACTION_VIEW_TERMSCONDITIONS));
            $redirect->toUrl();
        }

        return $this;
    }

    /**
     * TODO: Re-invent this in a durable way
     *
     * @deprecated Re-invent this in a durable way ...
     * @return \Chamilo\Libraries\Architecture\Kernel
     */
    private function preventDoubleLogin()
    {
        $prevent_double_logins = PlatformSetting :: get('prevent_double_login', \Chamilo\Core\User\Manager :: context());

        if ($prevent_double_logins)
        {
            $exceptions = PlatformSetting :: get('double_login_exceptions', \Chamilo\Core\User\Manager :: context());
            $exceptions = explode(',', $exceptions);
            $exception_found = array_search($this->getUser()->get_auth_source(), $exceptions);

            // logout when user is logged in more than once
            if (UserLoginSession :: $single_login === false && $exception_found === false &&
                 $this->getUser()->is_platform_admin() === false)
            {
                $udm = \Chamilo\Core\User\Storage\DataManager :: logout();
                throw new NotAllowedException();
            }
        }

        return $this;
    }

    /**
     *
     * @return \Chamilo\Libraries\Architecture\Kernel
     */
    private function traceVisit()
    {
        if (! $this->getApplication() instanceof Application)
        {
            throw new \Exception(
                'No application available to trace. Please call Kernel::buildApplication() before calling Kernel::traceVisit()');
        }
        else
        {
            if (! $this->getApplication() instanceof NoVisitTraceComponentInterface)
            {
                if ($this->getUser() instanceof User)
                {
                    Event :: trigger(
                        'Online',
                        \Chamilo\Core\Admin\Manager :: context(),
                        array('user' => $this->getUser()->get_id()));

                    $requestUri = $this->getRequest()->server->get('REQUEST_URI');

                    if ($this->getRequest()->query->get(Application :: PARAM_CONTEXT) != 'Chamilo\Core\User\Ajax' &&
                         $this->getRequest()->query->get(Application :: PARAM_ACTION) != 'LeaveComponent')
                    {
                        $return = Event :: trigger(
                            'Enter',
                            \Chamilo\Core\User\Manager :: context(),
                            array(
                                \Chamilo\Core\User\Integration\Chamilo\Core\Tracking\Storage\DataClass\Visit :: PROPERTY_LOCATION => $_SERVER['REQUEST_URI'],
                                \Chamilo\Core\User\Integration\Chamilo\Core\Tracking\Storage\DataClass\Visit :: PROPERTY_USER_ID => $this->getUser()->get_id()));
                    }
                }
            }
            return $this;
        }
    }

    /**
     *
     * @return \Chamilo\Libraries\Architecture\Application\ApplicationConfiguration
     */
    private function getApplicationConfiguration()
    {
        return new ApplicationConfiguration($this->getRequest(), $this->getUser());
    }

    /**
     *
     * @return \Chamilo\Libraries\Architecture\Kernel
     */
    private function buildApplication()
    {
        $applicationFactory = new ApplicationFactory($this->getContext(), $this->getApplicationConfiguration());
        $this->application = $applicationFactory->getComponent();

        return $this;
    }

    /**
     * Executes the application's component
     */
    private function runApplication()
    {
        $response = new Response($this->getApplication()->run());
        $response->send();
    }

    /**
     *
     * @return \Chamilo\Libraries\Architecture\Kernel
     */
    public function handleOAuth2()
    {
        $state = $this->getRequest()->query->get(self :: PARAM_STATE);
        $code = $this->getRequest()->query->get(self :: PARAM_CODE);
        $sessionState = $this->getRequest()->query->get(self :: PARAM_SESSION_STATE);

        if ($state && $code && $sessionState)
        {
            $stateParameters = (array) unserialize(base64_decode($state));
            $stateParameters[self :: PARAM_CODE] = $code;
            $stateParameters[self :: PARAM_SESSION_STATE] = $sessionState;

            $redirect = new Redirect($stateParameters);
            $redirect->toUrl();
        }

        return $this;
    }

    private function logException(\Exception $exception)
    {
        if (! $exception instanceof NotAllowedException)
        {
            Utilities :: write_error(
                $exception->getCode(),
                $exception->getMessage(),
                $exception->getFile(),
                $exception->getLine());

            if (extension_loaded('newrelic'))
            {
                newrelic_notice_error('chamilo_exception', $exception);
            }
        }
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
                $this->checkUpgrade()->checkMaintenance()->setup()->loadUser()->displayTerms()->handleOAuth2()->buildApplication()->traceVisit()->checkAuthentication()->runApplication();
            }
        }
        catch (\Exception $exception)
        {
            $this->logException($exception);

            $response = new ExceptionResponse($exception, $this->getApplication());
            $response->send();
        }
    }
}
