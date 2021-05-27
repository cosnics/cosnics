<?php
namespace Chamilo\Application\Weblcms;

use Chamilo\Application\Weblcms\Admin\CourseAdminValidator;
use Chamilo\Application\Weblcms\Course\Storage\DataClass\Course;
use Chamilo\Application\Weblcms\Course\Storage\DataManager as CourseDataManager;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Storage\DataClass\CourseCategory;
use Chamilo\Application\Weblcms\Storage\DataClass\CourseRequest;
use Chamilo\Application\Weblcms\Storage\DataClass\CourseSection;
use Chamilo\Application\Weblcms\Storage\DataClass\CourseTypeUserCategory;
use Chamilo\Application\Weblcms\Storage\DataManager;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataManager as CourseGroupDataManager;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 * ============================================================================== This is an application that creates a
 * fully fledged web-based learning content management system.
 * The Web-LCMS is based on so-called "tools", which each
 * represent a segment in the application.
 *
 * @author Tim De Pauw ==============================================================================
 */
abstract class Manager extends Application
{
    // Parameters
    const PARAM_REQUEST_TYPE = 'request_type';
    const PARAM_REQUEST_VIEW = 'request_view';
    const PARAM_REQUEST = 'request';
    const PARAM_REMOVE_SELECTED_REQUESTS = 'removed selected requests';
    const PARAM_COURSE = 'course';
    const PARAM_CATEGORY = 'publication_category';
    const PARAM_COURSE_CATEGORY_ID = 'category';
    const PARAM_COURSE_USER = 'course';
    const PARAM_COURSE_GROUP = 'course_group';
    const PARAM_COURSE_TYPE_USER_CATEGORY_ID = 'user_category';
    const PARAM_COURSE_TYPE = 'course_type';
    const PARAM_USERS = 'users';
    const PARAM_GROUP = 'group';
    const PARAM_TYPE = 'type';
    const PARAM_ACTIVE = 'active';
    const PARAM_TOOL = 'tool';
    const PARAM_COMPONENT_ACTION = 'action';
    const PARAM_DIRECTION = 'direction';
    const PARAM_REMOVE_SELECTED = 'remove_selected';
    const PARAM_REMOVE_SELECTED_COURSE_TYPES = 'remove selected coursetypes';
    const PARAM_ACTIVATE_SELECTED_COURSE_TYPES = 'activate selected coursetypes';
    const PARAM_DEACTIVATE_SELECTED_COURSE_TYPES = 'deactivate selected coursetypes';
    const PARAM_CHANGE_COURSE_TYPE_SELECTED_COURSES = 'Change Coursetype selected courses';
    const PARAM_UNSUBSCRIBE_SELECTED = 'unsubscribe_selected';
    const PARAM_SUBSCRIBE_SELECTED = 'subscribe_selected';
    const PARAM_SUBSCRIBE_SELECTED_AS_STUDENT = 'subscribe_selected_as_student';
    const PARAM_SUBSCRIBE_SELECTED_AS_ADMIN = 'subscribe_selected_as_admin';
    const PARAM_SUBSCRIBE_SELECTED_GROUP = 'subscribe_selected_group_admin';
    const PARAM_TOOL_ACTION = 'tool_action';
    const PARAM_STATUS = 'user_status';
    const PARAM_EXTRA = 'extra';
    const PARAM_PUBLICATION = 'publication';
    const PARAM_TEMPLATE_ID = 'template_id';

    // Actions
    const ACTION_VIEW_WEBLCMS_HOME = 'Home';
    const ACTION_VIEW_COURSE = 'CourseViewer';
    const ACTION_IMPORT_COURSES = 'CourseImporter';
    const ACTION_IMPORT_COURSE_USERS = 'CourseUserImporter';
    const ACTION_MANAGER_SORT = 'Sorter';
    const ACTION_COURSE_CATEGORY_MANAGER = 'CourseCategoryManager';
    const ACTION_ADMIN_REQUEST_BROWSER = 'AdminRequestBrowser';
    const ACTION_COURSE_USER_SUBSCRIPTION_REQUEST_GRANT = 'course_user_subscription_request_granter';
    const ACTION_REPORTING = 'Reporting';
    const ACTION_REQUEST = 'Request';
    const ACTION_COURSE_TYPE_MANAGER = 'CourseTypeManager';
    const ACTION_ADMIN_COURSE_MANAGER = 'AdminCourseManager';
    const ACTION_COURSE_MANAGER = 'CourseManager';
    const ACTION_CREATE_BOOKMARK = 'CourseBookmarkCreator';
    const ACTION_ANNOUNCEMENT = 'Announcement';
    const ACTION_ADMIN = 'Admin';
    const ACTION_BROWSE_OPEN_COURSES = 'OpenCoursesBrowser';

    // Default action
    const DEFAULT_ACTION = self::ACTION_VIEW_WEBLCMS_HOME;

    /**
     * The sections that this application offers.
     */
    private $sections;

    /**
     * The course_group object of the course_group currently active in this application
     */
    private $course_group;

    private $request;

    /**
     * Gets the identifier of the current tool
     *
     * @return string The identifier of current tool
     */
    public function get_tool_id()
    {
        return $this->get_parameter(self::PARAM_TOOL);
    }

    /**
     * Gets the user object for a given user
     *
     * @param $user_id int
     *
     * @return User
     */
    public function get_user_info($user_id)
    {
        return \Chamilo\Core\User\Storage\DataManager::retrieve_by_id(
            User::class,
            (int) $user_id);
    }

    public function set_request($request)
    {
        $this->request = $request;
    }

    /**
     * Returns the course_group that is being used.
     *
     * @return string The course_group.
     */
    public function get_course_group()
    {
        return $this->course_group;
    }

    public function get_request()
    {
        return $this->request;
    }

    public function get_home_url()
    {
        return $this->get_url(array(self::PARAM_ACTION => self::ACTION_VIEW_WEBLCMS_HOME));
    }

    /**
     * Sets the course_group
     *
     * @param $course_group CourseGroup
     */
    public function set_course_group($course_group)
    {
        $this->course_group = $course_group;
    }

    /**
     * Gets a list of all course_groups of the current active course in which the current user is subscribed.
     */
    public function get_course_groups()
    {
        return CourseGroupDataManager::retrieve_course_groups_from_user($this->get_user_id(), $this->get_course_id());
    }

    /**
     * Makes a category tree ready for displaying by adding a prefix to the category title based on the level of that
     * category in the tree structure.
     *
     * @param $tree array The category tree
     * @param $categories array In this array the new category titles (with prefix) will be stored. The keys in this
     *        array are the category ids, the values are the new titles
     * @param $level int The current level in the tree structure
     */
    private static function translate_category_tree($tree, $categories, $level = 0)
    {
        foreach ($tree as $node)
        {
            $obj = $node['obj'];
            $prefix = ($level ? str_repeat('&nbsp;&nbsp;&nbsp;', $level) . '&mdash; ' : '');
            $categories[$obj->get_id()] = $prefix . $obj->get_title();
            $subtree = $node['sub'];
            if (is_array($subtree) && count($subtree))
            {
                self::translate_category_tree($subtree, $categories, $level + 1);
            }
        }
    }

    /**
     * Gets a category
     *
     * @param $id int The id of the requested category
     * @return LearningPublicationCategory The requested category
     */
    public function get_category($id)
    {
        return DataManager::retrieve_by_id(ContentObjectPublication::class, $id);
    }

    /**
     * Returns the names of the sections known to this application.
     *
     * @return array The tools.
     */
    public function get_registered_sections()
    {
        if (is_null($this->sections))
        {
            $this->load_sections();
        }

        return $this->sections;
    }

    /**
     * Loads the sections installed on the system.
     */
    public function load_sections()
    {
        // if (! is_null($this->get_course_id()))
        {
            $condition = new EqualityCondition(
                new PropertyConditionVariable(CourseSection::class, CourseSection::PROPERTY_COURSE_ID),
                new StaticConditionVariable(Request::get(self::PARAM_COURSE)));
            $sections = DataManager::retrieves(
                CourseSection::class,
                new DataClassRetrievesParameters($condition));

            foreach($sections as $section)
            {
                // $type = isset($section->type) ? $section->type : '';
                $this->sections[] = $section;
            }
        }
    }

    /**
     * Determines whether or not the given name is a valid tool name.
     *
     * @param $name string The name to evaluate.
     * @return True if the name is a valid tool name, false otherwise.
     */
    public static function is_tool_name($name)
    {
        return (preg_match('/^[a-z][a-z_]+$/', $name) > 0);
    }

    public function count_requests($condition = null)
    {
        return DataManager::count(CourseRequest::class, new DataClassCountParameters($condition));
    }

    /**
     * Retrieves a personal course category for the user according to
     *
     * @param $user_id int
     * @param $sort int
     * @param $direction string
     *
     * @return CourseUserCategory The course user category.
     */
    public function retrieve_course_type_user_category_at_sort($user_id, $course_type_id, $sort, $direction)
    {
        return DataManager::retrieve_course_type_user_category_at_sort($user_id, $course_type_id, $sort, $direction);
    }

    /**
     * Retrieves a single course category from persistent storage.
     *
     * @param $category_code string The alphanumerical identifier of the course category.
     * @return CourseCategory The course category.
     */
    public function retrieve_course_category($course_category)
    {
        return DataManager::retrieve_by_id(CourseCategory::class, $course_category);
    }

    /**
     * Retrieves a single course user relation from persistent storage.
     *
     * @param $course_code string
     * @param $user_id int
     *
     * @return CourseCategory The course category.
     */
    public function retrieve_course_user_relation($course_code, $user_id)
    {
        return CourseDataManager::retrieve_course_user_relation_by_course_and_user($course_code, $user_id);
    }

    /**
     * Gets the date of the last visit of current user to the current location
     *
     * @param $tool string If $tool equals null, current active tool will be taken into account. If no tool is given or
     *        no tool is active the date of last visit to the course homepage will be returned.
     * @param $category_id int The category in the given tool of which the last visit date is requested. If $category_id
     *        equals null, the current active category will be used.
     * @return int
     */
    public function get_last_visit_date($tool = null, $category_id = null)
    {
        if (is_null($tool))
        {
            $tool = $this->get_parameter(self::PARAM_TOOL);
        }

        if (is_null($category_id))
        {
            $category_id = $this->get_parameter(self::PARAM_CATEGORY);

            if (is_null($category_id))
            {
                $category_id = 0;
            }
        }

        return DataManager::get_last_visit_date($this->get_course_id(), $this->get_user_id(), $tool, $category_id);
    }

    /**
     * Determines if a tool has new publications since the last time the current user visited the tool.
     *
     * @param $tool string
     * @param $course Course
     */
    public function tool_has_new_publications($tool, Course $course = null)
    {
        if ($course == null)
        {
            $course = $this->get_course();
        }

        return DataManager::tool_has_new_publications($tool, $this->get_user(), $course);
    }

    /**
     * Returns the url to the course's page
     *
     * @param $course Course
     *
     * @return String
     */
    public function get_course_viewing_url($course)
    {
        return $this->get_url(
            array(self::PARAM_ACTION => self::ACTION_VIEW_COURSE, self::PARAM_COURSE => $course->get_id()));
    }

    /**
     * Returns the link to the course's page
     *
     * @param $course Course
     *
     * @return String
     */
    public function get_course_viewing_link($course, $encode = false)
    {
        return $this->get_link(
            array(self::PARAM_ACTION => self::ACTION_VIEW_COURSE, self::PARAM_COURSE => $course->get_id()),
            $encode);
    }

    /**
     * Returns the maintenance url for the course
     *
     * @param $course Course
     *
     * @return String
     */
    public function get_course_maintenance_url($course)
    {
        return $this->get_url(
            array(
                self::PARAM_ACTION => self::ACTION_VIEW_COURSE,
                self::PARAM_COURSE => $course->get_id(),
                self::PARAM_TOOL => 'maintenance'));
    }

    /**
     * Returns the editing url for the course user category
     *
     * @param mixed[string] $course_type_user_category
     *
     * @return String
     */
    public function get_course_user_category_edit_url($course_type_user_category)
    {
        return $this->get_url(
            array(
                self::PARAM_ACTION => self::ACTION_MANAGER_SORT,
                self::PARAM_COMPONENT_ACTION => 'edit',
                self::PARAM_COURSE_TYPE_USER_CATEGORY_ID => $course_type_user_category[CourseTypeUserCategory::PROPERTY_ID]));
    }

    /**
     * Returns the creating url for a course user category
     *
     * @return String
     */
    public function get_course_user_category_add_url()
    {
        return $this->get_url(
            array(self::PARAM_ACTION => self::ACTION_MANAGER_SORT, self::PARAM_COMPONENT_ACTION => 'add'));
    }

    /**
     * Returns the moving url for the course user category
     *
     * @param mixed[string] $course_type_user_category
     * @param $direction string
     *
     * @return String
     */
    public function get_course_user_category_move_url($course_type_user_category = [], $direction)
    {
        return $this->get_url(
            array(
                self::PARAM_ACTION => self::ACTION_MANAGER_SORT,
                self::PARAM_COMPONENT_ACTION => 'movecat',
                self::PARAM_DIRECTION => $direction,
                self::PARAM_COURSE_TYPE_USER_CATEGORY_ID => $course_type_user_category[CourseTypeUserCategory::PROPERTY_ID]));
    }

    /**
     * Returns the deleting url for the course user category
     *
     * @param mixed[string] $course_type_user_category
     *
     * @return String
     */
    public function get_course_user_category_delete_url($course_type_user_category)
    {
        return $this->get_url(
            array(
                self::PARAM_ACTION => self::ACTION_MANAGER_SORT,
                self::PARAM_COMPONENT_ACTION => 'delete',
                self::PARAM_COURSE_TYPE_USER_CATEGORY_ID => $course_type_user_category[CourseTypeUserCategory::PROPERTY_ID]));
    }

    /**
     * Returns the creating url for a course category
     *
     * @return String
     */
    public function get_course_category_add_url()
    {
        return $this->get_url(
            array(self::PARAM_ACTION => self::ACTION_COURSE_CATEGORY_MANAGER, self::PARAM_COMPONENT_ACTION => 'add'));
    }

    /**
     * Returns the deleting url for the course category
     *
     * @param $course_category CourseCategory
     *
     * @return String
     */
    public function get_course_category_delete_url($coursecategory)
    {
        return $this->get_url(
            array(
                self::PARAM_ACTION => self::ACTION_COURSE_CATEGORY_MANAGER,
                self::PARAM_COMPONENT_ACTION => 'delete',
                self::PARAM_COURSE_CATEGORY_ID => $coursecategory->get_code()));
    }

    /**
     * Returns the editing url for the course user relation
     *
     * @param mixed[string] $course_type_user_category
     * @param $course Course
     *
     * @return String
     */
    public function get_course_user_edit_url($course_type_user_category = null, Course $course = null)
    {
        if ($course_type_user_category)
        {
            $course_type_user_category_id = $course_type_user_category[CourseTypeUserCategory::PROPERTY_ID];
        }

        return $this->get_url(
            array(
                self::PARAM_ACTION => self::ACTION_MANAGER_SORT,
                self::PARAM_COMPONENT_ACTION => 'assign',
                self::PARAM_COURSE => $course->get_id(),
                self::PARAM_COURSE_TYPE_USER_CATEGORY_ID => $course_type_user_category_id));
    }

    /**
     * Returns the moving url for the course user relation
     *
     * @param $course Course
     * @param $direction string
     *
     * @return String
     */
    public function get_course_user_move_url($course_type_user_category, Course $course, $direction)
    {
        return $this->get_url(
            array(
                self::PARAM_ACTION => self::ACTION_MANAGER_SORT,
                self::PARAM_COMPONENT_ACTION => 'move',
                self::PARAM_DIRECTION => $direction,
                self::PARAM_COURSE => $course->get_id(),
                self::PARAM_COURSE_TYPE_USER_CATEGORY_ID => $course_type_user_category[CourseTypeUserCategory::PROPERTY_ID]));
    }

    public function is_teacher($course, $user)
    {
        if ($user != null && $course != null)
        {
            // // If the user is a platform administrator, grant all rights
            // if ($user->is_platform_admin())
            // {
            // return true;
            // }

            $courseValidator = CourseAdminValidator::getInstance();

            // If the user is a sub administrator, grant all rights
            if ($courseValidator->isUserAdminOfCourse($user, $course))
            {
                return true;
            }

            // If the user is enrolled as a teacher directlt or via a platform group, grant all rights
            $relation = $this->retrieve_course_user_relation($course->get_id(), $user->get_id());

            if (($relation && $relation->get_status() == 1) || $user->is_platform_admin())
            {
                return true;
            }
            else
            {
                return CourseDataManager::is_teacher_by_platform_group_subscription($course->get_id(), $user);
            }
        }

        return false;
    }

    /**
     * Unsubscribe a user from a course.
     *
     * @param $course Course
     * @param $user_id int
     *
     * @return boolean
     */
    public function unsubscribe_user_from_course($course, $user_id)
    {
        $success = true;
        $course_groups = CourseGroupDataManager::retrieve_course_groups_from_user($user_id, $course->get_id());
        foreach($course_groups as $course_group)
        {
            $success &= CourseGroupDataManager::unsubscribe_users_from_course_groups($user_id, $course_group->get_id());
        }

        // unsubscribe the user from the course
        return ($success && CourseDataManager::unsubscribe_user_from_course($course->get_id(), $user_id));
    }

    /**
     * Subscribe a group to a course.
     *
     * @param $course Course
     * @param $group_id int
     *
     * @return boolean
     */
    public function subscribe_group_to_course($course, $group_id, $status)
    {
        return CourseDataManager::subscribe_group_to_course($course->get_id(), $group_id, $status);
    }

    /**
     * Unsubscribe a group from a course.
     *
     * @param $course Course
     * @param $user_id int
     *
     * @return boolean
     */
    public function unsubscribe_group_from_course($course, $group_id)
    {
        return CourseDataManager::unsubscribe_group_from_course($course->get_id(), $group_id);
    }

    public function get_reporting_url($params)
    {
        $array = array(
            Application::PARAM_CONTEXT => self::context(),
            self::PARAM_TOOL => null,
            self::PARAM_ACTION => self::ACTION_REPORTING);
        $array = array_merge($array, $params);

        return $this->get_url($array);
    }

    /**
     * Indicates whether the current tool may be accessed for the current course.
     *
     * @return bool
     */
    public function get_course_id()
    {
        return Request::get(self::PARAM_COURSE);
    }
}
