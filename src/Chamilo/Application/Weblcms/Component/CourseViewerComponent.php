<?php
namespace Chamilo\Application\Weblcms\Component;

use Chamilo\Application\Weblcms\Admin\CourseAdminValidator;
use Chamilo\Application\Weblcms\Course\OpenCourse\Service\OpenCourseService;
use Chamilo\Application\Weblcms\Course\Storage\DataClass\Course;
use Chamilo\Application\Weblcms\Course\Storage\DataManager as CourseDataManager;
use Chamilo\Application\Weblcms\CourseSettingsConnector;
use Chamilo\Application\Weblcms\CourseSettingsController;
use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\CourseVisit;
use Chamilo\Application\Weblcms\Interfaces\IgnoreToolTrackingInterface;
use Chamilo\Application\Weblcms\Manager;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Storage\DataClass\CourseSetting;
use Chamilo\Application\Weblcms\Storage\DataManager;
use Chamilo\Core\Tracking\Storage\DataClass\Event;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Exceptions\NoObjectSelectedException;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException;
use Chamilo\Libraries\Architecture\Exceptions\UserException;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Format\Utilities\ResourceManager;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;
use Exception;

/**
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

    private $is_teacher;

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        /**
         * Make sure the tool parameter is backwards compatible by upper camelizing the tool
         */
        $tool = $this->getRequest()->query->get(self::PARAM_TOOL);
        $tool = StringUtilities::getInstance()->createString($tool)->upperCamelize()->__toString();

        $this->set_parameter(self::PARAM_COURSE, $this->get_course()->get_id());
        $this->set_parameter(self::PARAM_TOOL, $tool);

        $breadcrumbtrail = BreadcrumbTrail::getInstance();

        $breadcrumb_title = CourseSettingsConnector::get_breadcrumb_title_for_course($this->get_course());

        $breadcrumbtrail->add(
            new Breadcrumb(
                $this->get_url([self::PARAM_CATEGORY => null, self::PARAM_TOOL => null]), $breadcrumb_title
            )
        );

        if (!$this->access_allowed())
        {
            throw new NotAllowedException();
        }

        if (is_null($this->get_course()))
        {
            throw new Exception(Translation::get('SelectedCourseNotValid'));
        }

        $this->load_course_theme();
        $this->load_course_language();

        if (!$tool)
        {
            $tool = 'Home';
        }

        $category = $this->getRequest()->query->get(self::PARAM_CATEGORY, 0);

        $this->course_tool_registration = DataManager::retrieve_course_tool_by_name($tool);

        if (!$this->course_tool_registration)
        {
            throw new UserException(Translation::get('SelectedCourseToolNotValid'));
        }

        if ($tool != 'CourseGroup')
        {
            $this->set_parameter('course_group', null);
        }

        $publicationId =
            $this->getRequest()->getFromQuery(\Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID);
        if (!empty($publicationId) && !is_array($publicationId))
        {
            $publication = DataManager::retrieve_by_id(ContentObjectPublication::class, $publicationId);
            if ($publication instanceof ContentObjectPublication)
            {
                $category = $publication->get_category_id();
                $publicationId = $publication->getId();
            }
        }

        $managerClass = $this->course_tool_registration->getContext() . '\Manager';
        if (!class_exists($managerClass) || !is_subclass_of($managerClass, IgnoreToolTrackingInterface::class))
        {
            Event::trigger(
                'VisitCourse', Manager::CONTEXT, [
                    CourseVisit::PROPERTY_USER_ID => $this->get_user_id(),
                    CourseVisit::PROPERTY_COURSE_ID => $this->get_course_id(),
                    CourseVisit::PROPERTY_TOOL_ID => $this->course_tool_registration->get_id(),
                    CourseVisit::PROPERTY_CATEGORY_ID => $category,
                    CourseVisit::PROPERTY_PUBLICATION_ID => $publicationId
                ]
            );

            DataManager::log_course_module_access($this->get_course_id(), $this->get_user_id(), $tool, $category);
        }

        $result = \Chamilo\Application\Weblcms\Tool\Manager::factory_and_launch(
            $this->course_tool_registration->getContext(), $this
        );

        return $result;
    }

    /**
     * determine if this user has access to this course platform admin & course admin always have access subscribed
     * users have access when status is 'open', 'open to platform' or 'open to world' other 'real' platform users have
     * access when status is 'open to platform' or 'open to world' anonymous users have access when status is 'open to
     * world'
     *
     * @return bool access_allowed
     */
    public function access_allowed()
    {
        $tool = $this->getRequest()->query->get(self::PARAM_TOOL);
        $tool_action = $this->getRequest()->query->get(self::PARAM_TOOL_ACTION);

        if ($this->is_teacher() || ($tool == 'User' &&
                $tool_action == \Chamilo\Application\Weblcms\Tool\Implementation\User\Manager::ACTION_VIEW_AS))
        {
            $allowed = true;
        }
        else
        {
            $course_settings_controller = CourseSettingsController::getInstance();
            $course_access = $course_settings_controller->get_course_setting(
                $this->get_course(), CourseSettingsConnector::COURSE_ACCESS
            );

            $viewAsCourseId = $this->getSession()->get('view_as_course_id');

            if ($course_access == CourseSettingsConnector::COURSE_ACCESS_CLOSED &&
                (!isset($viewAsCourseId) || $viewAsCourseId != $this->get_course_id()))
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
     * @return OpenCourseService
     */
    protected function getOpenCourseService()
    {
        return $this->getService(OpenCourseService::class);
    }

    /**
     * Returns the course
     *
     * @return Course
     * @throws \Exception
     */
    public function get_course()
    {
        if (is_null($this->course))
        {
            $course_id = $this->getRequest()->query->get(self::PARAM_COURSE);

            if (!$course_id)
            {
                throw new NoObjectSelectedException(
                    Translation::getInstance()->getTranslation('Course', null, Manager::CONTEXT)
                );
            }

            $this->course = CourseDataManager::retrieve_by_id(Course::class, $course_id);

            if (empty($this->course))
            {
                throw new ObjectNotExistException(
                    Translation::getInstance()->getTranslation('Course', null, Manager::CONTEXT), $course_id
                );
            }
        }

        return $this->course;
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
     * Returns the CourseTool registration of the selected tool
     *
     * @return CourseTool
     */
    public function get_tool_registration()
    {
        return $this->course_tool_registration;
    }

    /**
     * Fake user as necessary for "view as" functionality.
     */
    public function get_user(): ?User
    {
        $va_id = $this->getSession()->get(\Chamilo\Application\Weblcms\Tool\Manager::PARAM_VIEW_AS_ID);
        $course_id =
            $this->getSession()->get(\Chamilo\Application\Weblcms\Tool\Manager::PARAM_VIEW_AS_COURSE_ID);

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
     * Fake user id as necessary for "view as" functionality.
     * If no "view as" id is set, use normal user id.
     */
    public function get_user_id(): ?string
    {
        $va_id = $this->getSession()->get(\Chamilo\Application\Weblcms\Tool\Manager::PARAM_VIEW_AS_ID);
        $course_id =
            $this->getSession()->get(\Chamilo\Application\Weblcms\Tool\Manager::PARAM_VIEW_AS_COURSE_ID);

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
     * Returns whether or not the current user is viewing as another user and is returning to a normal user
     *
     * @return bool
     */
    protected function isReturningToNormalUser()
    {
        if ($this->getSession()->get('view_as_course_id') == $this->get_course_id())
        {
            if ($this->getRequest()->getFromRequestOrQuery(self::PARAM_TOOL) == 'User' &&
                $this->getRequest()->getFromRequestOrQuery(
                    self::PARAM_TOOL_ACTION
                ) == \Chamilo\Application\Weblcms\Tool\Implementation\User\Manager::ACTION_VIEW_AS)
            {
                return true;
            }
        }

        return false;
    }

    /**
     * Returns whether or not the tool is active
     *
     * @return bool
     * @throws NoObjectSelectedException
     * @throws ObjectNotExistException
     */
    protected function isToolActive()
    {
        return CourseSettingsController::getInstance()->get_course_setting(
            $this->get_course(), CourseSetting::COURSE_SETTING_TOOL_ACTIVE, $this->get_tool_registration()->get_id()
        );
    }

    /**
     * Returns whether or not the tool is visible
     *
     * @return bool
     * @throws NoObjectSelectedException
     * @throws ObjectNotExistException
     */
    protected function isToolVisible()
    {
        return CourseSettingsController::getInstance()->get_course_setting(
            $this->get_course(), CourseSetting::COURSE_SETTING_TOOL_VISIBLE, $this->get_tool_registration()->get_id()
        );
    }

    public function is_teacher()
    {
        if (is_null($this->is_teacher))
        {
            $user = $this->get_user();
            $course = $this->get_course();

            if ($user != null && $course != null)
            {
                // // If the user is a platform administrator, grant all rights
                // if ($user->isPlatformAdmin())
                // {
                // return true;
                // }

                $courseValidator = CourseAdminValidator::getInstance();

                // If the user is a sub administrator, grant all rights
                if ($courseValidator->isUserAdminOfCourse($user, $course))
                {
                    $this->is_teacher = true;
                }

                // If the user is enrolled as a teacher directlt or via a platform group, grant all rights
                $relation = $this->retrieve_course_user_relation($course->get_id(), $user->get_id());

                if (($relation && $relation->get_status() == 1) || $user->isPlatformAdmin())
                {
                    $this->is_teacher = true;
                }
                else
                {
                    $this->is_teacher =
                        CourseDataManager::is_teacher_by_platform_group_subscription($course->get_id(), $user);
                }
            }
            else
            {
                $this->is_teacher = false;
            }
        }

        return $this->is_teacher;
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
     * Loads the language of the course
     */
    public function load_course_language()
    {
        $course_settings_controller = CourseSettingsController::getInstance();
        $language = $course_settings_controller->get_course_setting(
            $this->get_course(), CourseSettingsConnector::LANGUAGE
        );

        // set user selected language or general platform language
        if ($language == 'platform_language')
        {
            $language = $this->getUserSettingService()->getSettingForUser(
                $this->getUser(), 'Chamilo\Core\Admin', 'platform_language'
            );
        }

        Translation::getInstance()->setLanguageIsocode($language);
    }

    /**
     * Loads the theme of the course
     */
    public function load_course_theme()
    {
        $course_settings_controller = CourseSettingsController::getInstance();
        $theme_setting = $course_settings_controller->get_course_setting(
            $this->get_course(), CourseSettingsConnector::THEME
        );

        if ($theme_setting)
        {
            $this->getThemeSystemPathBuilder()->setTheme($theme_setting);
            $this->getThemeWebPathBuilder()->setTheme($theme_setting);
        }
    }

    /**
     * Add a header when viewing course as another user.
     *
     * @param null $pageTitle
     *
     * @return string
     */
    public function render_header(string $pageTitle = ''): string
    {
        $html = [];

        $html[] = parent::renderHeader($pageTitle);
        $va_id = $this->getSession()->get(\Chamilo\Application\Weblcms\Tool\Manager::PARAM_VIEW_AS_ID);
        $course_id =
            $this->getSession()->get(\Chamilo\Application\Weblcms\Tool\Manager::PARAM_VIEW_AS_COURSE_ID);

        if (isset($va_id) && isset($course_id))
        {
            if ($course_id == $this->get_course_id())
            {
                $user = $this->get_user_info($va_id);

                $html[] = '<div class="alert alert-warning">' . Translation::get('ViewingAsUser') . ' ' .
                    $user->get_firstname() . ' ' . $user->get_lastname() . ' <a href="' . $this->get_url(
                        [
                            self::PARAM_TOOL => 'User',  // replace,
                            // seriously
                            self::PARAM_TOOL_ACTION => \Chamilo\Application\Weblcms\Tool\Implementation\User\Manager::ACTION_VIEW_AS
                        ]
                    ) . '">' . Translation::get('Back') . '</a></div>';
            }
        }

        $html[] = $this->getResourceManager()->getResourceHtml(
            $this->getWebPathBuilder()->getJavascriptPath('Chamilo\Application\Weblcms', true) . 'CourseVisit.js'
        );

        return implode(PHP_EOL, $html);
    }
}
