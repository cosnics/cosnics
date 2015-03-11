<?php
namespace Chamilo\Libraries\Architecture\Application;

use Chamilo\Configuration\Storage\DataClass\Registration;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\File\FileLogger;
use Chamilo\Libraries\File\Filesystem;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Format\Display;
use Chamilo\Libraries\Format\NotificationMessage;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbGenerator;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Platform\Configuration\PlatformSetting;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Session\Session;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Utilities\StringUtilities;
use Chamilo\Libraries\Format\Structure\Page;

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

    /**
     *
     * @var \Symfony\Component\HttpFoundation\Request
     */
    private $request;

    /**
     *
     * @var \core\user\User
     */
    private $user;

    /**
     *
     * @var \libraries\architecture\application\Application
     */
    private $application;

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
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \core\user\storage\data_class\User $user $user
     * @param \libraries\architecture\application\Application $application
     */
    public function __construct(\Symfony\Component\HttpFoundation\Request $request, $user = null, $application = null)
    {
        $this->request = $request;
        $this->user = $user;
        $this->application = $application;

        Page :: getInstance()->setSection($this->package());
    }

    public function getRequest()
    {
        return $this->request;
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
        return $this->application;
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
        return Redirect :: get_url($parameters, $filter, $encode_entities);
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
    public function simple_redirect($parameters = array(), $filter = array(), $encode_entities = false,
        $redirect_type = Redirect :: TYPE_URL)
    {
        switch ($redirect_type)
        {
            case Redirect :: TYPE_URL :
                $parameters = (count($parameters) ? array_merge($this->get_parameters(), $parameters) : $this->get_parameters());
                Redirect :: url($parameters, $filter, $encode_entities);
                break;
            case Redirect :: TYPE_LINK :
                Redirect :: link($parameters, $filter, $encode_entities);
                break;
        }
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
        $redirect_type = Redirect :: TYPE_URL)
    {
        $message_type = (! $error_message) ? NotificationMessage :: TYPE_NORMAL : NotificationMessage :: TYPE_ERROR;

        $messages = Session :: retrieve(self :: PARAM_MESSAGES);
        $messages[self :: PARAM_MESSAGE_TYPE][] = $message_type;
        $messages[self :: PARAM_MESSAGE][] = $message;

        Session :: register(self :: PARAM_MESSAGES, $messages);

        $this->simple_redirect($parameters, $filter, $encode_entities, $redirect_type);
    }

    /**
     * Returns the current URL parameters.
     *
     * @return string[]
     */
    public function get_parameters()
    {
        return Parameters :: get_instance()->get_parameters($this);
    }

    /**
     * Returns the value of the given URL parameter.
     *
     * @param string $name
     * @return string
     */
    public function get_parameter($name)
    {
        return Parameters :: get_instance()->get_parameter($this, $name);
    }

    /**
     * Sets the value of a URL parameter.
     *
     * @param string $name
     * @param string $value
     */
    public function set_parameter($name, $value)
    {
        Parameters :: get_instance()->set_parameter($this, $name, $value);
    }

    /**
     * Displays the header
     */
    public function render_header()
    {
        if ($this->get_application())
        {
            return $this->get_application()->render_header();
        }

        $breadcrumbtrail = BreadcrumbTrail :: get_instance();
        if ($breadcrumbtrail->size() == 1)
        {
            $breadcrumbtrail->add(
                new Breadcrumb($this->get_url(), Translation :: get('TypeName', null, static :: context())));
        }

        $page = Page :: getInstance();
        $page->setApplication($this);

        $html = array();

        $html[] = $page->getHeader()->toHtml();

        // If there is an application-wide menu, show it
        if ($this->has_menu())
        {
            $html[] = '<div style="float: left; width: 17%;">';
            $html[] = $this->get_menu();
            $html[] = '</div>';
            $html[] = '<div style="float: right; width: 82%;">';
        }

        if ($breadcrumbtrail->size() > 0)
        {
            $title = $breadcrumbtrail->get_last()->get_name();

            $html[] = '<h3 title="' . strip_tags($title) . '">' . $title . '</h3>';
            $html[] = '<div class="clear">&nbsp;</div>';
        }

        if (PlatformSetting :: get('maintenance_mode'))
        {
            $html[] = Display :: error_message(Translation :: get('MaintenanceModeMessage'));
        }

        // Display messages
        $messages = Session :: retrieve(self :: PARAM_MESSAGES);
        Session :: unregister(self :: PARAM_MESSAGES);
        if (is_array($messages))
        {
            $html[] = $this->display_messages($messages[self :: PARAM_MESSAGE], $messages[self :: PARAM_MESSAGE_TYPE]);
        }

        // DEPRECATED
        // Display messages
        $message = Request :: get(self :: PARAM_MESSAGE);
        $type = Request :: get(self :: PARAM_MESSAGE_TYPE);

        if ($message)
        {
            $html[] = $this->display_message($message);
        }

        $message = Request :: get(self :: PARAM_ERROR_MESSAGE);
        if ($message)
        {
            $html[] = $this->display_error_message($message);
        }

        $message = Request :: get(self :: PARAM_WARNING_MESSAGE);
        if ($message)
        {
            $html[] = $this->display_warning_message($message);
        }

        return implode(PHP_EOL, $html);
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

        $html = array();

        // In case there is an application-wide menu, properly end it
        if ($this->has_menu())
        {
            $html[] = '<div class="clear">&nbsp;</div>';
            $html[] = '</div>';
        }

        $html[] = '<div class="clear">&nbsp;</div>';
        $html[] = Page :: getInstance()->getFooter()->toHtml();

        return implode(PHP_EOL, $html);
    }

    /**
     * Displays a normal message.
     *
     * @param string $message
     */
    public function display_message($message)
    {
        $html = array();

        $html[] = '<div class="notifications">';
        $html[] = NotificationMessage :: normal($message)->to_html();
        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }

    /**
     *
     * @param multitype:string $messages
     * @param multitype:int $types
     */
    public function display_messages($messages, $types)
    {
        $html = array();

        $html[] = '<div class="notifications">';
        foreach ($types as $key => $type)
        {
            $html[] = NotificationMessage :: create($messages[$key], $type)->to_html();
        }
        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }

    /**
     * Displays an error message.
     *
     * @param string $message
     */
    public function display_error_message($message)
    {
        $html = array();

        $html[] = '<div class="notifications">';
        $html[] = NotificationMessage :: error($message)->to_html();
        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }

    /**
     * Displays a warning message.
     *
     * @param string $message
     */
    public function display_warning_message($message)
    {
        $html = array();

        $html[] = '<div class="notifications">';
        $html[] = NotificationMessage :: warning($message)->to_html();
        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }

    /**
     * Displays an error page.
     *
     * @param string $message
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
     */
    public function get_user_id()
    {
        if ($this->user)
        {
            return $this->user->get_id();
        }

        return 0;
    }

    /**
     * Gets the user.
     *
     * @return \core\user\User
     */
    public function get_user()
    {
        return $this->user;
    }

    /**
     *
     * @return string
     */
    public function get_action()
    {
        return $this->get_parameter(static :: PARAM_ACTION);
    }

    /**
     * Sets the current action.
     *
     * @param string $action
     */
    public function set_action($action)
    {
        return $this->set_parameter(static :: PARAM_ACTION, $action);
    }

    public function get_application_name()
    {
        return ClassnameUtilities :: getInstance()->getPackageNameFromNamespace(static :: context());
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

        return Translation :: get($message);
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

        return Translation :: get($message, $param);
    }

    /**
     * Returns the breadcrumb generator
     *
     * @return BreadcrumbGeneratorInterface
     */
    public function get_breadcrumb_generator()
    {
        return new BreadcrumbGenerator($this, BreadcrumbTrail :: get_instance());
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
        if (self :: exists($context))
        {
            $registration = \Chamilo\Configuration\Storage\DataManager :: get_registration($context);

            if ($registration && $registration->is_active())
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
        return Redirect :: get_link($parameters, $filter, $encode_entities);
    }

    /**
     *
     * @param string $application
     * @return boolean
     */
    public static function exists($application)
    {
        if (! isset(self :: $application_path_cache[$application]))
        {
            $application_path = Path :: getInstance()->namespaceToFullPath($application);
            self :: $application_path_cache[$application] = is_dir($application_path);
        }
        return self :: $application_path_cache[$application];
    }

    /**
     *
     * @param string $application_name
     * @return string
     * @deprecated Fallback in case we still have an application name somewhere which can't be changed to
     */
    public static function get_application_namespace($application_name)
    {
        $path = Path :: getInstance()->namespaceToFullPath('Chamilo\Core') . $application_name . '/';

        if (is_dir($path))
        {
            return 'Chamilo\Core\\' . $application_name;
        }
        else
        {
            $path = Path :: getInstance()->namespaceToFullPath('Chamilo\Application') . $application_name . '/';

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

        if (! $type || $type == Registration :: TYPE_CORE)
        {
            $directories = Filesystem :: get_directory_content(
                Path :: getInstance()->namespaceToFullPath('Chamilo\Core'),
                Filesystem :: LIST_DIRECTORIES,
                false);

            foreach ($directories as $directory)
            {
                $namespace = self :: get_application_namespace(basename($directory));

                if (\Chamilo\Configuration\Package\Storage\DataClass\Package :: exists($namespace))
                {
                    $applications[] = $namespace;
                }
            }
        }

        if (! $type || $type == Registration :: TYPE_APPLICATION)
        {
            $directories = Filesystem :: get_directory_content(
                Path :: getInstance()->namespaceToFullPath('Chamilo\Application'),
                Filesystem :: LIST_DIRECTORIES,
                false);

            foreach ($directories as $directory)
            {
                $namespace = self :: get_application_namespace(basename($directory));

                if (\Chamilo\Configuration\Package\Storage\DataClass\Package :: exists($namespace))
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
        $applications = \Chamilo\Configuration\Storage\DataManager :: get_registrations_by_type($type);

        $active_applications = array();

        foreach ($applications as $application)
        {
            if (! $application->is_active())
            {
                continue;
            }

            $active_applications[] = $application->get_name();
        }
        return $active_applications;
    }

    public static function context_fallback($context)
    {
        // Check if the context exists
        $context_path = Path :: getInstance()->namespaceToFullPath($context);

        if (! is_dir($context_path))
        {
            $original_context = $context;

            // Adding a fallback for old-style contexts which might still exis in certain applications, links, etc.
            $convertedContext = (string) StringUtilities :: getInstance()->createString($context)->upperCamelize();

            $possible_contexts = array();
            $possible_contexts[] = 'Chamilo\Application\\' . $convertedContext;
            $possible_contexts[] = 'Chamilo\Core\\' . $convertedContext;
            $possible_contexts[] = 'Chamilo\\' . $convertedContext;

            foreach ($possible_contexts as $possible_context)
            {
                if (is_dir(Path :: getInstance()->namespaceToFullPath($possible_context)))
                {
                    $context = $possible_context;
                }
            }

            $context_path = Path :: getInstance()->namespaceToFullPath($context);
            if (! is_dir($context_path))
            {
                throw new \Exception(Translation :: get('NoContextFound', array('CONTEXT' => $context)));
            }
            else
            {
                $query = $_GET;
                $query[self :: PARAM_CONTEXT] = $context;

                $messages = Session :: retrieve(self :: PARAM_MESSAGES);
                $messages[self :: PARAM_MESSAGE_TYPE][] = NotificationMessage :: TYPE_WARNING;
                $messages[self :: PARAM_MESSAGE][] = Translation :: get(
                    'OldApplicationParameter',
                    array('OLD' => $original_context, 'NEW' => $context));

                Session :: register(self :: PARAM_MESSAGES, $messages);

                $logger = new FileLogger(Path :: getInstance()->getLogPath() . '/application_parameters.log', true);
                $logger->log_message(Redirect :: current_url());

                Redirect :: link($query);
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
        $className = self :: class_name(false);

        if ($className == 'Manager')
        {
            return static :: context();
        }
        else
        {
            return ClassnameUtilities :: getInstance()->getNamespaceParent(static :: context());
        }
    }

    abstract function run();
}
