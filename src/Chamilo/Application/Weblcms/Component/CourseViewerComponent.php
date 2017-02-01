<?php
namespace Chamilo\Application\Weblcms\Component;

use Chamilo\Application\Weblcms\Course\OpenCourse\Service\OpenCourseService;
use Chamilo\Application\Weblcms\Course\Storage\DataClass\Course;
use Chamilo\Application\Weblcms\Course\Storage\DataManager as CourseDataManager;
use Chamilo\Application\Weblcms\CourseSettingsConnector;
use Chamilo\Application\Weblcms\CourseSettingsController;
use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\CourseVisit;
use Chamilo\Application\Weblcms\Manager;
use Chamilo\Application\Weblcms\Storage\DataClass\CourseSetting;
use Chamilo\Application\Weblcms\Storage\DataManager;
use Chamilo\Core\Tracking\Storage\DataClass\Event;
use Chamilo\Libraries\Architecture\Exceptions\NoObjectSelectedException;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException;
use Chamilo\Libraries\Architecture\Exceptions\UserException;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Format\Utilities\ResourceManager;
use Chamilo\Libraries\Platform\Configuration\LocalSetting;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Session\Session;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 * $Id: course_viewer.class.php 218 2009-11-13 14:21:26Z kariboe $
 *
 * @package application.lib.weblcms.weblcms_manager.component
 */

/**
 * Weblcms component which provides the course page
 */
class CourseViewerComponent extends Manager implements DelegateComponent
{

    /**
     * The selected course object
     *
     * @var \application\weblcms\course\Course
     */
    private $course;

    /**
     * The selected tool registration
     *
     * @var \application\weblcms\CourseTool
     */
    private $course_tool_registration;

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        /**
         * Make sure the tool parameter is backwards compatible by upper camelizing the tool
         */
        $tool = Request::get(self::PARAM_TOOL);
        $tool = StringUtilities::getInstance()->createString($tool)->upperCamelize()->__toString();

        $this->set_parameter(self::PARAM_COURSE, $this->get_course()->get_id());
        $this->set_parameter(self::PARAM_TOOL, $tool);

        $breadcrumbtrail = BreadcrumbTrail::getInstance();

        $breadcrumb_title = CourseSettingsConnector::get_breadcrumb_title_for_course($this->get_course());

        $breadcrumbtrail->add(
            new Breadcrumb(
                $this->get_url(array(self::PARAM_CATEGORY => null, self::PARAM_TOOL => null)),
                $breadcrumb_title
            )
        );

        if (!$this->access_allowed())
        {
            throw new NotAllowedException();
        }

        if (is_null($this->get_course()))
        {
            throw new \Exception(Translation::get('SelectedCourseNotValid'));
        }

        $this->load_course_theme();
        $this->load_course_language();

        if (!$tool)
        {
            $tool = 'Home';
        }

        $category = Request::get(self::PARAM_CATEGORY, 0);

        $this->course_tool_registration = DataManager::retrieve_course_tool_by_name($tool);

        if (!$this->course_tool_registration)
        {
            throw new UserException(Translation::get('SelectedCourseToolNotValid'));
        }

        if ($tool != 'CourseGroup')
        {
            $this->set_parameter('course_group', null);
        }

        Event::trigger(
            'VisitCourse',
            Manager::context(),
            array(
                CourseVisit::PROPERTY_USER_ID => $this->get_user_id(),
                CourseVisit::PROPERTY_COURSE_ID => $this->get_course_id(),
                CourseVisit::PROPERTY_TOOL_ID => $this->course_tool_registration->get_id(),
                CourseVisit::PROPERTY_CATEGORY_ID => $category,
                CourseVisit::PROPERTY_PUBLICATION_ID => Request::get(
                    \Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID
                )
            )
        );

        $result = \Chamilo\Application\Weblcms\Tool\Manager::factory_and_launch(
            $this->course_tool_registration->getContext(),
            $this
        );

        DataManager::log_course_module_access($this->get_course_id(), $this->get_user_id(), $tool, $category);

        return $result;
    }

    /**
     * Returns the course
     *
     * @return Course
     *
     * @throws \Exception
     */
    public function get_course()
    {
        if (is_null($this->course))
        {
            $course_id = Request::get(self::PARAM_COURSE);

            if (!$course_id)
            {
                throw new NoObjectSelectedException(
                    Translation::getInstance()->getTranslation('Course', null, Manager::context())
                );
            }

            $this->course = CourseDataManager:: retrieve_by_id(Course:: class_name(), $course_id);

            if (empty($this->course))
            {
                throw new ObjectNotExistException(
                    Translation::getInstance()->getTranslation('Course', null, Manager::context()),
                    $course_id
                );
            }
        }

        return $this->course;
    }

    /**
     * Fake user id as necessary for "view as" functionality.
     * If no "view as" id is set, use normal user id.
     */
    public function get_user_id()
    {
        $va_id = Session::get(\Chamilo\Application\Weblcms\Tool\Manager::PARAM_VIEW_AS_ID);
        $course_id = Session::get(\Chamilo\Application\Weblcms\Tool\Manager::PARAM_VIEW_AS_COURSE_ID);

        if (isset($va_id) && isset($course_id))
        {
            if ($course_id == $this->get_course_id())
            {
                return $va_id;
            }
        }

        return parent::get_user_id();
    }

    /**
     * Fake user as necessary for "view as" functionality.
     */
    public function get_user()
    {
        $va_id = Session::get(\Chamilo\Application\Weblcms\Tool\Manager::PARAM_VIEW_AS_ID);
        $course_id = Session::get(\Chamilo\Application\Weblcms\Tool\Manager::PARAM_VIEW_AS_COURSE_ID);

        if (isset($va_id) && isset($course_id))
        {
            if ($course_id == $this->get_course_id())
            {
                return $this->get_user_info($va_id);
            }
        }

        return parent::get_user();
    }

    /**
     * Add a header when viewing course as another user.
     *
     * @param null $pageTitle
     *
     * @return string
     */
    public function render_header($pageTitle = null)
    {
        $html = array();

        $html[] = parent::render_header($pageTitle);
        $va_id = Session::get(\Chamilo\Application\Weblcms\Tool\Manager::PARAM_VIEW_AS_ID);
        $course_id = Session::get(\Chamilo\Application\Weblcms\Tool\Manager::PARAM_VIEW_AS_COURSE_ID);

        if (isset($va_id) && isset($course_id))
        {
            if ($course_id == $this->get_course_id())
            {
                $user = $this->get_user_info($va_id);

                $html[] = '<div class="alert alert-warning">' . Translation::get('ViewingAsUser') . ' ' .
                    $user->get_firstname() . ' ' . $user->get_lastname() . ' <a href="' .
                    $this->get_url(
                        array(
                            self::PARAM_TOOL => 'User',  // replace,
                            // seriously
                            self::PARAM_TOOL_ACTION => \Chamilo\Application\Weblcms\Tool\Implementation\User\Manager::ACTION_VIEW_AS
                        )
                    ) .
                    '">' . Translation::get('Back') . '</a></div>';
            }
        }

        $html[] = ResourceManager::getInstance()->get_resource_html(
            Path::getInstance()->getJavascriptPath('Chamilo\Application\Weblcms', true) . 'CourseVisit.js'
        );

        return implode(PHP_EOL, $html);
    }

    /**
     * Returns the identifier of the course that is being used.
     *
     * @return int
     */
    public function get_course_id()
    {
        if ($this->get_course())
        {
            return $this->get_course()->get_id();
        }
    }

    /**
     * Loads the theme of the course
     */
    public function load_course_theme()
    {
        $course_settings_controller = CourseSettingsController::getInstance();
        $theme_setting = $course_settings_controller->get_course_setting(
            $this->get_course(),
            CourseSettingsConnector::THEME
        );

        if ($theme_setting)
        {
            Theme::getInstance()->setTheme($theme_setting);
        }
    }

    /**
     * Loads the language of the course
     */
    public function load_course_language()
    {
        $course_settings_controller = CourseSettingsController::getInstance();
        $language = $course_settings_controller->get_course_setting(
            $this->get_course(),
            CourseSettingsConnector::LANGUAGE
        );

        // set user selected language or general platform language
        if ($language == 'platform_language')
        {
            $language = LocalSetting::getInstance()->get('platform_language');
        }

        Translation::getInstance()->setLanguageIsocode($language);
    }

    private $is_teacher;

    public function is_teacher()
    {
        if (is_null($this->is_teacher))
        {
            $user = $this->get_user();
            $course = $this->get_course();

            $this->is_teacher = parent::is_teacher($course, $user);
        }

        return $this->is_teacher;
    }

    /**
     * determine if this user has access to this course platform admin & course admin always have access subscribed
     * users have access when status is 'open', 'open to platform' or 'open to world' other 'real' platform users have
     * access when status is 'open to platform' or 'open to world' anonymous users have access when status is 'open to
     * world'
     *
     * @return boolean access_allowed
     */
    public function access_allowed()
    {
        $tool = Request::get(self::PARAM_TOOL);
        $tool_action = Request::get(self::PARAM_TOOL_ACTION);

        if ($this->is_teacher() || ($tool == 'User' &&
                $tool_action == \Chamilo\Application\Weblcms\Tool\Implementation\User\Manager::ACTION_VIEW_AS)
        )
        {
            $allowed = true;
        }
        else
        {
            $course_settings_controller = CourseSettingsController::getInstance();
            $course_access = $course_settings_controller->get_course_setting(
                $this->get_course(),
                CourseSettingsConnector::COURSE_ACCESS
            );

            $viewAsCourseId = Session::get('view_as_course_id');

            if ($course_access == CourseSettingsConnector::COURSE_ACCESS_CLOSED &&
                (!isset($viewAsCourseId) || $viewAsCourseId != $this->get_course_id())
            )
            {
                $allowed = false;
            }
            else
            {
                $is_subscribed = CourseDataManager::is_subscribed($this->get_course(), $this->get_user());

                if ($is_subscribed)
                {
                    $allowed = true;
                }
                else
                {
                    $allowed =
                        $this->getOpenCourseService()->isCourseOpenForUser($this->get_course(), $this->getUser());
                }
            }
        }

        return $allowed;
    }

    /**
     *
     * @return OpenCourseService
     */
    protected function getOpenCourseService()
    {
        return $this->getService('chamilo.application.weblcms.course.open_course.service.open_course_service');
    }

    /**
     * Returns the CourseTool registration of the selected tool
     *
     * @return CourseTool
     */
    public function get_tool_registration()
    {
        return $this->course_tool_registration;
    }

    /**
     * Indicates whether the current tool may be accessed for the current course.
     *
     * @return bool
     */
    public function is_tool_accessible()
    {
        if (!$this->isToolActive())
        {
            return false;
        }

        if ($this->is_teacher())
        {
            return true;
        }

        if ($this->isReturningToNormalUser())
        {
            return true;
        }

        return $this->isToolVisible();
    }

    /**
     * Returns whether or not the tool is active
     *
     * @return bool
     *
     * @throws NoObjectSelectedException
     * @throws ObjectNotExistException
     */
    protected function isToolActive()
    {
        return CourseSettingsController::getInstance()->get_course_setting(
            $this->get_course(),
            CourseSetting::COURSE_SETTING_TOOL_ACTIVE,
            $this->get_tool_registration()->get_id()
        );
    }

    /**
     * Returns whether or not the tool is visible
     *
     * @return bool
     *
     * @throws NoObjectSelectedException
     * @throws ObjectNotExistException
     */
    protected function isToolVisible()
    {
        return CourseSettingsController::getInstance()->get_course_setting(
            $this->get_course(),
            CourseSetting::COURSE_SETTING_TOOL_VISIBLE,
            $this->get_tool_registration()->get_id()
        );
    }

    /**
     * Returns whether or not the current user is viewing as another user and is returning to a normal user
     *
     * @return bool
     */
    protected function isReturningToNormalUser()
    {
        if (Session::get('view_as_course_id') == $this->get_course_id())
        {
            if ($this->getRequest()->get(self::PARAM_TOOL) == 'User' &&
                $this->getRequest()->get(self::PARAM_TOOL_ACTION) ==
                \Chamilo\Application\Weblcms\Tool\Implementation\User\Manager::ACTION_VIEW_AS
            )
            {
                return true;
            }
        }

        return false;
    }
}
