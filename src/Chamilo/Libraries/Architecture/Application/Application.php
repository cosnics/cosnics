<?php
namespace Chamilo\Libraries\Architecture\Application;

use Chamilo\Configuration\Storage\DataClass\Registration;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Architecture\Exceptions\UserException;
use Chamilo\Libraries\File\FileLogger;
use Chamilo\Libraries\File\Filesystem;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Format\Display;
use Chamilo\Libraries\Format\NotificationMessage\NotificationMessage;
use Chamilo\Libraries\Format\NotificationMessage\NotificationMessageManager;
use Chamilo\Libraries\Format\NotificationMessage\NotificationMessageRenderer;
use Chamilo\Libraries\Format\Structure\BreadcrumbGenerator;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Format\Structure\Page;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Session\Session;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;
use Chamilo\Configuration\Configuration;

/**
 *
 * @package libraries\architecture
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
abstract class Application
{
    use \Chamilo\Libraries\Architecture\Traits\ClassContext;
    use \Chamilo\Libraries\Architecture\Traits\DependencyInjectionContainerTrait;

    /**
     *
     * @var \Chamilo\Libraries\Architecture\Application\ApplicationConfigurationInterface
     */
    protected $applicationConfiguration;

    // Parameters
    const PARAM_ACTION = 'go';
    const PARAM_MESSAGES = 'messages';
    const PARAM_MESSAGE = 'message';
    const PARAM_MESSAGE_TYPE = 'message_type';
    const PARAM_ERROR_MESSAGE = 'error_message';
    const PARAM_WARNING_MESSAGE = 'warning_message';
    // TODO: the value of this constant should eventually be changed to 'context', but an undefined number of hardcoded
    // references still exists and needs to be fixed first to avoid breaking functionality of an undefined number of
    // places
    const PARAM_CONTEXT = 'application';

    // Result types
    const RESULT_TYPE_CREATED = 'Created';
    const RESULT_TYPE_UPDATED = 'Updated';
    const RESULT_TYPE_DELETED = 'Deleted';
    const RESULT_TYPE_MOVED = 'Moved';

    /**
     *
     * @var string[]
     */
    public static $application_path_cache = array();

    /**
     *
     * @param \Chamilo\Libraries\Architecture\Application\ApplicationConfigurationInterface $applicationConfiguration
     */
    public function __construct(ApplicationConfigurationInterface $applicationConfiguration)
    {
        $this->applicationConfiguration = $applicationConfiguration;

        $this->initializeContainer();
    }

    /**
     *
     * @return \Chamilo\Libraries\Architecture\Application\ApplicationConfigurationInterface
     */
    public function getApplicationConfiguration()
    {
        return $this->applicationConfiguration;
    }

    /**
     * Helper function to call the authorization checker with the current logged in user.
     * Throws the
     * NotAllowedException when not valid.
     *
     * @param string $context
     * @param string $action
     *
     * @throws NotAllowedException
     */
    public function checkAuthorization($context, $action = null)
    {
        return $this->getAuthorizationChecker()->checkAuthorization($this->getUser(), $context, $action);
    }

    /**
     * Helper function to call the authorization checker with the current logged in user, returns true or false
     * and can be used in if statements
     *
     * @param string $context
     * @param string $action
     *
     * @return bool
     */
    public function isAuthorized($context, $action = null)
    {
        return $this->getAuthorizationChecker()->isAuthorized($this->getUser(), $context, $action);
    }

    /**
     * Get the parent application
     *
     * @return \libraries\architecture\application\Application
     * @deprecated User get_application() now
     */
    public function get_parent()
    {
        return $this->get_application();
    }

    /**
     * Get the parent application
     *
     * @return \libraries\architecture\application\Application
     */
    public function get_application()
    {
        return $this->getApplicationConfiguration()->getApplication();
    }

    /**
     * Gets the URL of the current page in the application.
     * Optionally takes an associative array of name/value pairs
     * representing additional query string parameters; these will either be added to the parameters already present, or
     * override them if a value with the same name exists.
     *
     * @param multitype:string $parameters
     * @param multitype:string $filter
     * @param boolean $encode_entities Whether or not to encode HTML entities. Defaults to false.
     * @return string
     */
    public function get_url($parameters = array(), $filter = array(), $encode_entities = false)
    {
        $parameters = (count($parameters) ? array_merge($this->get_parameters(), $parameters) : $this->get_parameters());

        $redirect = new Redirect($parameters, $filter, $encode_entities);
        return $redirect->getUrl();
    }

    /**
     * Redirect the end user to another location.
     * The current url will be used as the basis.
     *
     * @param multitype:string $parameters
     * @param multitype:string $filter
     * @param boolean $encode_entities Whether or not to encode HTML entities. Defaults to false.
     * @param string $redirect_type
     */
    public function simple_redirect($parameters = array(), $filter = array(), $encodeEntities = false, $anchor = null)
    {
        $parameters = (count($parameters) ? array_merge($this->get_parameters(), $parameters) : $this->get_parameters());

        $redirect = new Redirect($parameters, $filter, $encodeEntities, $anchor);
        $redirect->toUrl();
        exit();
    }

    /**
     * Redirect the end user to another location.
     * The current url will be used as the basis. This method allows passing
     * on messages directly instead of using the parameters array
     *
     * @param string $message
     * @param boolean $error_message
     * @param multitype:string $parameters
     * @param multitype:string $filter
     * @param boolean $encode_entities Whether or not to encode HTML entities. Defaults to false.
     * @param string $redirect_type
     */
    public function redirect($message = '', $error_message = false, $parameters = array(), $filter = array(), $encode_entities = false,
        $anchor = null)
    {
        if ($message != null)
        {

            $message_type = (! $error_message) ? \Chamilo\Libraries\Format\NotificationMessage\NotificationMessage::TYPE_INFO : \Chamilo\Libraries\Format\NotificationMessage\NotificationMessage::TYPE_DANGER;

            $notificationMessageManager = new NotificationMessageManager();

            $notificationMessageManager->addMessage(
                new \Chamilo\Libraries\Format\NotificationMessage\NotificationMessage($message, $message_type));
        }

        $this->simple_redirect($parameters, $filter, $encode_entities, $anchor);
    }

    /**
     * Returns the current URL parameters.
     *
     * @return string[]
     */
    public function get_parameters()
    {
        return Parameters::getInstance()->get_parameters($this);
    }

    /**
     * Returns the value of the given URL parameter.
     *
     * @param string $name
     * @return string
     */
    public function get_parameter($name)
    {
        return Parameters::getInstance()->get_parameter($this, $name);
    }

    /**
     * Sets the value of a URL parameter.
     *
     * @param string $name
     * @param string $value
     */
    public function set_parameter($name, $value)
    {
        Parameters::getInstance()->set_parameter($this, $name, $value);
    }

    /**
     * Displays the header
     *
     * @param string $pageTitle
     *
     * @return string
     */
    public function render_header($pageTitle = '')
    {
        if (! $pageTitle)
        {
            $pageTitle = $this->renderPageTitle();
        }

        if ($this->get_application())
        {
            return $this->get_application()->render_header($pageTitle);
        }

        $breadcrumbtrail = BreadcrumbTrail::getInstance();

        $page = Page::getInstance();
        $page->setApplication($this);

        $html = array();

        $html[] = $page->getHeader()->toHtml();

        if ($page->isFullPage())
        {
            $html[] = '<div class="row">';

            // If there is an application-wide menu, show it
            if ($this->has_menu())
            {
                $html[] = '<div class="col-xs-12 col-md-4 col-lg-3">';
                $html[] = $this->get_menu();
                $html[] = '</div>';
                $html[] = '<div class="col-xs-12 col-md-8 col-lg-9">';
            }
            else
            {
                $html[] = '<div class="col-xs-12">';
            }

            $html[] = $pageTitle;
            $html[] = '<div class="clearfix"></div>';
        }

        $maintenanceMode = Configuration::getInstance()->get_setting(array('Chamilo\Core\Admin', 'maintenance_mode'));

        if ($maintenanceMode)
        {
            $html[] = Display::error_message(Translation::get('MaintenanceModeMessage'));
        }

        // Display messages
        $messages = Session::retrieve(self::PARAM_MESSAGES);

        Session::unregister(self::PARAM_MESSAGES);
        if (is_array($messages))
        {
            $html[] = $this->display_messages($messages[self::PARAM_MESSAGE], $messages[self::PARAM_MESSAGE_TYPE]);
        }

        $notificationMessageManager = new NotificationMessageManager();
        $html[] = $notificationMessageManager->renderMessages();

        // DEPRECATED
        // Display messages
        $message = Request::get(self::PARAM_MESSAGE);
        $type = Request::get(self::PARAM_MESSAGE_TYPE);

        if ($message)
        {
            $html[] = $this->display_message($message);
        }

        $message = Request::get(self::PARAM_ERROR_MESSAGE);
        if ($message)
        {
            $html[] = $this->display_error_message($message);
        }

        $message = Request::get(self::PARAM_WARNING_MESSAGE);
        if ($message)
        {
            $html[] = $this->display_warning_message($message);
        }

        return implode(PHP_EOL, $html);
    }

    /**
     * Renders the page title
     *
     * @return string
     */
    protected function renderPageTitle()
    {
        $breadcrumbTrail = BreadcrumbTrail::getInstance();

        if ($breadcrumbTrail->size() > 0)
        {
            $pageTitle = BreadcrumbTrail::getInstance()->get_last()->get_name();

            return '<h3 id="page-title" title="' . htmlentities(strip_tags($pageTitle)) . '">' . $pageTitle . '</h3>';
        }

        return '';
    }

    /**
     * Displays the footer
     */
    public function render_footer()
    {
        if ($this->get_application())
        {
            return $this->get_application()->render_footer();
        }

        $page = Page::getInstance();

        $html = array();

        if ($page->isFullPage())
        {
            $html[] = '<div class="clearfix"></div>';
            $html[] = '</div>';

            $html[] = '<div class="clearfix"></div>';
            $html[] = '</div>';
        }

        $html[] = $page->getFooter()->toHtml();

        return implode(PHP_EOL, $html);
    }

    /**
     * Displays a normal message.
     *
     * @param string $message
     *
     * @return string
     */
    public function display_message($message)
    {
        $notificationMessageRenderer = new NotificationMessageRenderer();
        $notificationMessage = NotificationMessage::normal($message);

        return $notificationMessageRenderer->render($notificationMessage);
    }

    /**
     *
     * @param array $messages
     * @param array $types
     *
     * @return string
     */
    public function display_messages($messages, $types)
    {
        $notificationMessageRenderer = new NotificationMessageRenderer();
        $notificationMessages = array();

        foreach ($types as $key => $type)
        {
            $notificationMessages[] = new NotificationMessage($messages[$key], $type);
        }

        return $notificationMessageRenderer->render($notificationMessages);
    }

    /**
     * Displays an error message.
     *
     * @param string $message
     *
     * @return string
     */
    public function display_error_message($message)
    {
        $notificationMessageRenderer = new NotificationMessageRenderer();
        $notificationMessage = NotificationMessage::error($message);

        return $notificationMessageRenderer->render($notificationMessage);
    }

    /**
     * Displays a warning message.
     *
     * @param string $message
     *
     * @return string
     */
    public function display_warning_message($message)
    {
        $notificationMessageRenderer = new NotificationMessageRenderer();
        $notificationMessage = NotificationMessage::warning($message);

        return $notificationMessageRenderer->render($notificationMessage);
    }

    /**
     * Displays an error page.
     *
     * @param string $message
     *
     * @return string
     */
    public function display_error_page($message)
    {
        if ($this->get_application() instanceof Application)
        {
            return $this->get_application()->display_error_page($message);
        }

        $html = array();

        $html[] = $this->render_header();
        $html[] = $this->display_error_message($message);
        $html[] = $this->render_footer();

        return implode(PHP_EOL, $html);
    }

    /**
     * Displays a warning page.
     *
     * @param string $message
     */
    public function display_warning_page($message)
    {
        if ($this->get_application() instanceof Application)
        {
            return $this->get_application()->display_warning_page($message);
        }

        $html = array();

        $html[] = $this->render_header();
        $html[] = $this->display_warning_message($message);
        $html[] = $this->render_footer();

        return implode(PHP_EOL, $html);
    }

    /**
     *
     * @param boolean $showLoginForm
     */
    public function not_allowed($showLoginForm = true)
    {
        throw new NotAllowedException($showLoginForm);
    }

    /**
     * Gets the user id of this personal calendars owner
     *
     * @return int
     * @deprecated Use getUser()->getId() now
     */
    public function get_user_id()
    {
        if ($this->getApplicationConfiguration()->getUser())
        {
            return $this->get_user()->get_id();
        }

        return 0;
    }

    /**
     * Gets the user.
     *
     * @return \Chamilo\Core\User\Storage\DataClass\User
     * @deprecated Use getUser() now
     */
    public function get_user()
    {
        return $this->getUser();
    }

    /**
     *
     * @return \Chamilo\Core\User\Storage\DataClass\User
     */
    public function getUser()
    {
        return $this->getApplicationConfiguration()->getUser();
    }

    /**
     *
     * @return \Symfony\Component\HttpFoundation\Request
     */
    public function getRequest()
    {
        return $this->getApplicationConfiguration()->getRequest();
    }

    /**
     *
     * @return string
     */
    public function get_action()
    {
        return $this->get_parameter(static::PARAM_ACTION);
    }

    /**
     * Sets the current action.
     *
     * @param string $action
     */
    public function set_action($action)
    {
        return $this->set_parameter(static::PARAM_ACTION, $action);
    }

    public function get_application_name()
    {
        return ClassnameUtilities::getInstance()->getPackageNameFromNamespace(static::context());
    }

    /**
     * Does the entire application have a leftside menu? False per default.
     * Can be overwritten by the specific
     * application
     *
     * @return boolean
     */
    public function has_menu()
    {
        return false;
    }

    /**
     * Returns the html for the application-menu.
     * Empty per default Can be overwritten by the specific application
     *
     * @return string
     */
    public function get_menu()
    {
        return '';
    }

    /**
     * Determines if a given name is the name of an application
     *
     * @param string $string
     * @return boolean
     */
    public static function is_application_name($name)
    {
        return (preg_match('/^[a-z][a-z_]+$/', $name) > 0);
    }

    /**
     *
     * @param int $failures
     * @param int $count
     * @param string $fail_message_single
     * @param string $fail_message_multiple
     * @param string $succes_message_single
     * @param string $succes_message_multiple
     * @return string
     */
    public function get_result($failures, $count, $fail_message_single, $fail_message_multiple, $succes_message_single,
        $succes_message_multiple)
    {
        if ($failures)
        {
            if ($count == 1)
            {
                $message = $fail_message_single;
            }
            else
            {
                $message = $fail_message_multiple;
            }
        }
        else
        {
            if ($count == 1)
            {
                $message = $succes_message_single;
            }
            else
            {
                $message = $succes_message_multiple;
            }
        }

        return Translation::get($message);
    }

    /**
     * Generates a general results message like ObjectCreated, ObjectUpdated, ObjectDeleted
     *
     * @param int $failures
     * @param int $count
     * @param string $single_object
     * @param string $multiple_object
     * @param string $type
     * @return string
     */
    public function get_general_result($failures, $count, $single_object, $multiple_object,
        $type = Application :: RESULT_TYPE_CREATED)
    {
        if ($count == 1)
        {
            $param = array('OBJECT' => $single_object);

            if ($failures)
            {
                $message = 'ObjectNot' . $type;
            }
            else
            {
                $message = 'Object' . $type;
            }
        }
        else
        {
            $param = array('OBJECTS' => $multiple_object);

            if ($failures)
            {
                $message = 'ObjectsNot' . $type;
            }
            else
            {
                $message = 'Objects' . $type;
            }
        }

        return Translation::get($message, $param);
    }

    /**
     * Returns the breadcrumb generator
     *
     * @return BreadcrumbGeneratorInterface
     */
    public function get_breadcrumb_generator()
    {
        return new BreadcrumbGenerator($this, BreadcrumbTrail::getInstance());
    }

    /**
     *
     * @param \libraries\format\BreadcrumbTrail $breadcrumbtrail
     */
    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
    }

    /**
     *
     * @return multitype:string
     */
    public function get_additional_parameters()
    {
        return array();
    }

    /**
     *
     * @param string $context
     * @return boolean
     */
    public static function is_active($context = null)
    {
        if (self::exists($context))
        {
            if (\Chamilo\Configuration\Configuration::getInstance()->isRegisteredAndActive($context))
            {
                return true;
            }
            else
            {
                return false;
            }
        }
        else
        {
            return false;
        }
    }

    /**
     *
     * @param multitype:string $parameters
     * @param multitype:string $filter
     * @param boolean $encode_entities
     * @return string
     */
    public function get_link($parameters = array (), $filter = array(), $encode_entities = false)
    {
        $redirect = new Redirect($parameters, $filter, $encode_entities);
        return $redirect->getUrl();
    }

    /**
     *
     * @param string $application
     * @return boolean
     */
    public static function exists($application)
    {
        if (! isset(self::$application_path_cache[$application]))
        {
            $application_path = Path::getInstance()->namespaceToFullPath($application);
            self::$application_path_cache[$application] = is_dir($application_path);
        }
        return self::$application_path_cache[$application];
    }

    /**
     *
     * @param string $application_name
     * @return string
     * @deprecated Fallback in case we still have an application name somewhere which can't be changed to
     */
    public static function get_application_namespace($application_name)
    {
        $path = Path::getInstance()->namespaceToFullPath('Chamilo\Core') . $application_name . '/';

        if (is_dir($path))
        {
            return 'Chamilo\Core\\' . $application_name;
        }
        else
        {
            $path = Path::getInstance()->namespaceToFullPath('Chamilo\Application') . $application_name . '/';

            if (is_dir($path))
            {
                return 'Chamilo\Application\\' . $application_name;
            }
            else
            {
                return $application_name;
            }
        }
    }

    /**
     * Get a list of core and web applications from the filesystem as an array
     *
     * @return multitype:string
     */
    public static function get_packages_from_filesystem($type = null)
    {
        $applications = array();

        if (! $type || $type == Registration::TYPE_CORE)
        {
            $directories = Filesystem::get_directory_content(
                Path::getInstance()->namespaceToFullPath('Chamilo\Core'),
                Filesystem::LIST_DIRECTORIES,
                false);

            foreach ($directories as $directory)
            {
                $namespace = self::get_application_namespace(basename($directory));

                if (\Chamilo\Configuration\Package\Storage\DataClass\Package::exists($namespace))
                {
                    $applications[] = $namespace;
                }
            }
        }

        if (! $type || $type == Registration::TYPE_APPLICATION)
        {
            $directories = Filesystem::get_directory_content(
                Path::getInstance()->namespaceToFullPath('Chamilo\Application'),
                Filesystem::LIST_DIRECTORIES,
                false);

            foreach ($directories as $directory)
            {
                $namespace = self::get_application_namespace(basename($directory));

                if (\Chamilo\Configuration\Package\Storage\DataClass\Package::exists($namespace))
                {
                    $applications[] = $namespace;
                }
            }
        }

        return $applications;
    }

    /**
     *
     * @return multitype:string
     */
    public static function get_active_packages($type = Registration :: TYPE_APPLICATION)
    {
        $applications = \Chamilo\Configuration\Configuration::registrations_by_type($type);

        $active_applications = array();

        foreach ($applications as $application)
        {
            if (! $application[Registration::PROPERTY_STATUS])
            {
                continue;
            }

            $active_applications[] = $application[Registration::PROPERTY_CONTEXT];
        }
        return $active_applications;
    }

    public static function context_fallback($context, array $fallbackContexts)
    {
        // Check if the context exists
        $context_path = Path::getInstance()->namespaceToFullPath($context);

        if (! is_dir($context_path))
        {
            $original_context = $context;

            // Adding a fallback for old-style contexts which might still exis in certain applications, links, etc.

            $convertedContextParts = explode('\\', $context);

            foreach ($convertedContextParts as $key => $convertedContextPart)
            {
                $convertedContextParts[$key] = (string) StringUtilities::getInstance()->createString(
                    $convertedContextPart)->upperCamelize();
            }

            $convertedContext = implode('\\', $convertedContextParts);

            foreach ($fallbackContexts as $fallbackContext)
            {
                $possibleContext = $fallbackContext . $convertedContext;
                if (is_dir(Path::getInstance()->namespaceToFullPath($possibleContext)))
                {
                    $context = $possibleContext;
                    break;
                }
            }

            $context_path = Path::getInstance()->namespaceToFullPath($context);
            if (! is_dir($context_path))
            {
                throw new UserException(Translation::get('NoContextFound', array('CONTEXT' => $context)));
            }
            else
            {
                $query = $_GET;
                $query[self::PARAM_CONTEXT] = $context;

                // $notificationMessageManager = new NotificationMessageManager();
                // $notificationMessageManager->addMessage(
                // new \Chamilo\Libraries\Format\NotificationMessage\NotificationMessage(
                // Translation :: get(
                // 'OldApplicationParameter', array('OLD' => $original_context, 'NEW' => $context)
                // ),
                // \Chamilo\Libraries\Format\NotificationMessage\NotificationMessage::TYPE_WARNING,
                // 'old_application_parameter'
                // ),
                // 1
                // );

                $redirect = new Redirect();
                $currentUrl = $redirect->getCurrentUrl();

                $logger = new FileLogger(Path::getInstance()->getLogPath() . '/application_parameters.log', true);
                $logger->log_message($currentUrl);

                $redirect = new Redirect($query);
                $redirect->toUrl();
            }
        }

        return $context;
    }

    /**
     *
     * @return int
     */
    public function get_level()
    {
        $level = 0;
        $application = $this;

        while ($application->get_application() instanceof Application)
        {
            $level ++;
            $application = $application->get_application();
        }

        return $level;
    }

    /**
     *
     * @return string
     */
    public static function package()
    {
        $className = self::class_name(false);

        if ($className == 'Manager')
        {
            return static::context();
        }
        else
        {
            return ClassnameUtilities::getInstance()->getNamespaceParent(static::context());
        }
    }

    abstract function run();
}
