<?php
namespace Chamilo\Application\Weblcms\Renderer\CourseList\Type;

use Chamilo\Application\Weblcms\Course\Storage\DataClass\Course;
use Chamilo\Application\Weblcms\CourseSettingsConnector;
use Chamilo\Application\Weblcms\CourseSettingsController;
use Chamilo\Application\Weblcms\CourseType\Storage\DataClass\CourseType;
use Chamilo\Application\Weblcms\Manager;
use Chamilo\Application\Weblcms\Renderer\CourseList\CourseListRenderer;
use Chamilo\Application\Weblcms\Storage\DataClass\CourseTypeUserCategory;
use Chamilo\Application\Weblcms\Storage\DataClass\CourseTypeUserCategoryRelCourse;
use Chamilo\Application\Weblcms\Storage\DataClass\CourseUserCategory;
use Chamilo\Application\Weblcms\Storage\DataManager;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Format\Tabs\DynamicVisualTab;
use Chamilo\Libraries\Format\Tabs\DynamicVisualTabsRenderer;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Session\Session;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * Course list renderer to render the course list with tabs for the course types (used in courses home, courses sorter)
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class CourseTypeCourseListRenderer extends CourseListRenderer
{
    /**
     * **************************************************************************************************************
     * Parameters *
     * **************************************************************************************************************
     */
    const PARAM_SELECTED_COURSE_TYPE = 'selected_course_type';

    /**
     * **************************************************************************************************************
     * Display Order Properties *
     * **************************************************************************************************************
     */

    /**
     * The course type list
     *
     * @var ResultSet<CourseType>
     */
    protected $course_types;

    /**
     * The selected course type
     *
     * @var CourseType
     */
    protected $selected_course_type;

    /**
     * The course list for the selected course type
     *
     * @var Course[]
     */
    protected $courses;

    /**
     * **************************************************************************************************************
     * Main Functionality *
     * **************************************************************************************************************
     */

    /**
     * Returns the course list as html
     *
     * @return string
     */
    public function as_html()
    {
        $this->courses = $this->retrieve_courses();
        $this->course_types = $this->retrieve_course_types();

        $this->get_parent()->set_parameter(
            self :: PARAM_SELECTED_COURSE_TYPE,
            $this->get_selected_course_type_parameter_value());

        return $this->display_course_types();
    }

    /**
     * **************************************************************************************************************
     * Retrieve & Parse Functionality *
     * **************************************************************************************************************
     */

    /**
     * Retrieves the course types
     *
     * @return RecordResultSet
     */
    protected function retrieve_course_types()
    {
        return \Chamilo\Application\Weblcms\CourseType\Storage\DataManager :: retrieve_active_course_types_with_user_order(
            $this->get_parent()->get_user_id());
    }

    /**
     * Retrieves the courses with course categories Retrieves all the courses for every course type so we can decide
     * whether or not we want to show the course type tabs if required by the parent or courses are available
     *
     * @return Course[][][]
     */
    protected function retrieve_courses()
    {
        $user_id = $this->get_parent()->get_user_id();

        $courses = DataManager :: retrieve_all_courses_with_course_categories(
            $this->get_parent()->get_user()
        );

        return $this->parse_courses($courses);
    }

    /**
     * Parsers the courses in a structure in course type / course category
     *
     * @param RecordResultSet $courses
     *
     * @return mixed[][][][]
     */
    protected function parse_courses($courses)
    {
        $parsed_courses = array();

        while ($course = $courses->next_result())
        {
            $category_id = $course[CourseTypeUserCategoryRelCourse :: PROPERTY_COURSE_TYPE_USER_CATEGORY_ID];
            $category = ! is_null($category_id) ? $category_id : 0;

            $parsed_courses[$course[Course :: PROPERTY_COURSE_TYPE_ID]][$category][] = $course;
        }

        return $parsed_courses;
    }

    /**
     * Retrieves the course user categories for a course type
     *
     * @return RecordResultSet
     */
    protected function retrieve_course_user_categories_for_course_type()
    {
        return DataManager :: retrieve_course_user_categories_from_course_type(
            $this->get_selected_course_type_id(),
            $this->get_parent()->get_user_id());
    }

    /**
     * **************************************************************************************************************
     * Courses Retrieve Helper Functionality *
     * **************************************************************************************************************
     */

    /**
     * Retrieves the courses for a course user category in a given course type
     *
     * @param mixed[string] $course_type_user_category
     *
     * @return Course[]
     */
    protected function get_courses_for_course_type_user_category($course_type_user_category = null)
    {
        $course_type_id = $this->get_selected_course_type_id();
        $course_type_user_category_id = $course_type_user_category ? $course_type_user_category[CourseTypeUserCategory :: PROPERTY_ID] : 0;

        return $this->courses[$course_type_id][$course_type_user_category_id];
    }

    /**
     * Counts the courses for a course user category in a given course type
     *
     * @param mixed[string] $course_type_user_category
     *
     * @return int
     */
    protected function count_courses_for_course_type_user_category($course_type_user_category = null)
    {
        return count($this->get_courses_for_course_type_user_category($course_type_user_category));
    }

    /**
     * Counts the courses for a given course type id
     *
     * @param int $course_type_id
     *
     * @return int
     */
    protected function count_courses_for_course_type($course_type_id)
    {
        return count($this->courses[$course_type_id]);
    }

    /**
     * **************************************************************************************************************
     * Display Functionality *
     * **************************************************************************************************************
     */

    /**
     * Shows the tabs of the course types Show the course list for the selected tab
     *
     * @return string
     */
    protected function display_course_types()
    {
        $html = array();

        $html[] = '<ul class="nav nav-tabs course-list-tabs">';

        $renderer_name = ClassnameUtilities :: getInstance()->getClassnameFromObject($this, true);
        $course_tabs = new DynamicVisualTabsRenderer($renderer_name);

        $selected_course_type_id = $this->get_selected_course_type_id();

        $created_tabs = array();

        while ($course_type = $this->course_types->next_result())
        {
            $created_tabs[$course_type[CourseType :: PROPERTY_ID]] = new DynamicVisualTab(
                $course_type[CourseType :: PROPERTY_ID],
                $course_type[CourseType :: PROPERTY_TITLE],
                null,
                $this->get_course_type_url($course_type[CourseType :: PROPERTY_ID]));

            if ($this->get_parent()->show_empty_courses() ||
                 $this->count_courses_for_course_type($course_type[CourseType :: PROPERTY_ID]) > 0)
            {
                $course_tabs->add_tab($created_tabs[$course_type[CourseType :: PROPERTY_ID]]);

                $active = $selected_course_type_id == $course_type[CourseType :: PROPERTY_ID] ? 'active' : '';

                $html[] = '<li role="presentation" class="' . $active . '"><a href="';
                $html[] = $this->get_course_type_url($course_type[CourseType :: PROPERTY_ID]);
                $html[] = '">' . $course_type[CourseType :: PROPERTY_TITLE] . '</a></li>';
            }
        }

        // Add an extra tab for the no course type
        $created_tabs[0] = new DynamicVisualTab(
            0,
            Translation :: get('NoCourseType'),
            null,
            $this->get_course_type_url(0));

        if ($this->get_parent()->show_empty_courses() || $this->count_courses_for_course_type(0) > 0)
        {
            $course_tabs->add_tab($created_tabs[0]);

            $active = $selected_course_type_id == 0 ? 'active' : '';

            $html[] = '<li role="presentation" class="' . $active . '"><a href="';
            $html[] = $this->get_course_type_url(0);
            $html[] = '">' . Translation :: get('NoCourseType') . '</a></li>';
        }

        $html[] = '</ul>';

        if ($course_tabs->size() > 0)
        {
            if ($created_tabs[$selected_course_type_id])
            {
                $created_tabs[$selected_course_type_id]->set_selected(true);
            }

            $content = $this->display_course_user_categories_for_course_type();
            $course_tabs->set_content($content);

            $html[] = '<div class="course-list-tab-content">';
            $html[] = $content;
            $html[] = '</div>';

            return implode(PHP_EOL, $html);
            // return $course_tabs->render();
        }
        else
        {
            return '<div class="normal-message" style="text-align: center;">' . Translation :: get('NoCourses') .
                 '</div>';
        }
    }

    /**
     * Displays the course user categories for the selected course type
     *
     * @return string
     */
    protected function display_course_user_categories_for_course_type()
    {
        $html = array();

        $html[] = '<div class="list-group">';
        $html[] = $this->display_course_user_category();

        $course_type_user_categories = $this->retrieve_course_user_categories_for_course_type();

        $count = 0;
        $size = $course_type_user_categories->size();

        while ($course_type_user_category = $course_type_user_categories->next_result())
        {
            $html[] = $this->display_course_user_category($course_type_user_category, $count, $size);
            $count ++;
        }

        $html[] = '</div>';

        return implode($html, "\n");
    }

    /**
     * Displays a course user category box
     *
     * @param mixed[string] $course_type_user_category
     * @param int $offset
     * @param int $count
     *
     * @return string
     */
    protected function display_course_user_category($course_type_user_category = null, $offset = 1, $count = 1)
    {
        $html = array();

        if (isset($course_type_user_category))
        {
            $title = Utilities :: htmlentities($course_type_user_category[CourseUserCategory :: PROPERTY_TITLE]);
            $course_type_user_category_id = $course_type_user_category[CourseTypeUserCategory :: PROPERTY_ID];
        }
        else
        {
            $course_type_user_category_id = 0;
        }

        // $html[] = '<div id="course_user_category_' . $course_type_user_category_id . '">';

        if ($title)
        {
            $html[] = '<div class="list-group-item list-group-header">';
            $html[] = '<h5 class="list-group-item-heading pull-left">' . $title . '</h5>';
            $html[] = '<div class="pull-right">';
            $html[] = $this->get_course_type_user_category_actions($course_type_user_category, $offset, $count);
            $html[] = '</div>';
            $html[] = '<div class="clearfix"></div>';
            $html[] = '</div>';
        }

        if ($this->count_courses_for_course_type_user_category($course_type_user_category) == 0)
        {
            if (! $this->get_parent()->show_empty_courses())
            {
                return;
            }

            $html[] = '<div class="list-group-item">' . Translation :: get('NoCourses') . '</div>';
        }
        else
        {
            $html[] = $this->display_courses_for_course_type_user_category($course_type_user_category);
        }

        // $html[] = '</div>';

        return implode($html, "\n");
    }

    protected function loadCourseSettings($courses)
    {
        $courseIdentifiers = array();

        foreach ($courses as $course)
        {
            $courseIdentifiers[] = $course[Course :: PROPERTY_ID];
        }

        $courseSettingsController = CourseSettingsController :: get_instance();
        $courseSettingsController->loadSettingsForCoursesByIdentifiers($courseIdentifiers);
    }

    /**
     * Displays the courses for a user course category
     *
     * @param mixed[string] $course_type_user_category
     *
     * @return string
     */
    protected function display_courses_for_course_type_user_category($course_type_user_category = null)
    {
        $courses = $this->get_courses_for_course_type_user_category($course_type_user_category);
        $size = count($courses);

        $this->loadCourseSettings($courses);

        $html = array();

        if ($size > 0)
        {
            $course_instances = array();
            foreach ($courses as $course_properties)
            {
                $course_instances[$course_properties[Course :: PROPERTY_ID]] = DataClass :: factory(
                    Course :: class_name(),
                    $course_properties);
            }

            // Accelerate notification icon generation by querying all courses at ones and storing the results in a
            // cache.
            DataManager :: fill_new_publications_cache($this->get_user(), $course_instances);

            $course_settings_controller = CourseSettingsController :: get_instance();

            $count = 0;
            foreach ($courses as $course_properties)
            {
                $course = DataClass :: factory(Course :: class_name(), $course_properties);

                $course_id = $course->get_id();

                $course_admin = $course->is_course_admin($this->get_user());

                $course_visible = $course_settings_controller->get_course_setting(
                    $course,
                    CourseSettingsConnector :: VISIBILITY);

                if ($course_admin || $course_visible)
                {
                    $locked = '';
                    $invisbleClass = '';

                    if (! $course_visible)
                    {
                        $invisbleClass = 'invisible-course';
                    }

                    $html[] = '<div class="list-group-item ' . $invisbleClass . '">';

                    $url = $this->get_course_url($course);

                    $course_access = $course_settings_controller->get_course_setting(
                        $course,
                        CourseSettingsConnector :: COURSE_ACCESS);

                    $course_closed = $course_access == CourseSettingsConnector :: COURSE_ACCESS_CLOSED;

                    if ($course_closed)
                    {
                        if (! $course_admin)
                        {
                            $url = null;
                        }

                        $locked = '<span class="glyphicon glyphicon-lock" aria-hidden="true"></span>';
                    }

                    $html[] = '<h5 class="list-group-item-heading pull-left">';
                    $html[] = $locked;
                    $html[] = ' <a href="' . $url . '">';
                    $html[] = $course->get_title();
                    $html[] = '</a>';

                    if ($this->get_new_publication_icons() && (! $course_closed || $course_admin))
                    {
                        $html[] = $this->display_new_publication_icons($course);
                    }

                    $html[] = '</h5>';

                    $html[] = '<div class="pull-right">';
                    $html[] = $this->get_course_actions($course_type_user_category, $course, $count, $size);
                    $html[] = '</div>';
                    $html[] = '<div class="clearfix"></div>';

                    $text = array();

                    if ($course_settings_controller->get_course_setting(
                        $course,
                        CourseSettingsConnector :: SHOW_COURSE_CODE))
                    {
                        $text[] = $course->get_visual_code();
                    }

                    if ($course_settings_controller->get_course_setting(
                        $course,
                        CourseSettingsConnector :: SHOW_COURSE_TITULAR))
                    {
                        $text[] = \Chamilo\Core\User\Storage\DataManager :: get_fullname_from_user(
                            $course->get_titular_id(),
                            Translation :: get('NoTitular'));
                    }

                    if ($course_settings_controller->get_course_setting(
                        $course,
                        CourseSettingsConnector :: SHOW_COURSE_LANGUAGE))
                    {
                        $language = $course_settings_controller->get_course_setting(
                            $course,
                            CourseSettingsConnector :: LANGUAGE);

                        if ($language != 'platform_language')
                        {
                            $languageName = \Chamilo\Configuration\Configuration :: get_instance()->getLanguageNameFromIsocode(
                                $language);

                            $text[] = $languageName;
                        }
                        else
                        {
                            $text[] = Translation :: get('PlatformLanguage', null, 'Chamilo\Core\Admin');
                        }
                    }

                    if (count($text) > 0)
                    {
                        $html[] = '<p class="list-group-item-text">' . implode(' - ', $text) . '</p>';
                    }

                    if (! $course_visible)
                    {
                        $html[] = '<p class="list-group-item-text"><span class="label label-warning">' .
                             Translation :: getInstance()->getTranslation('Invisible', null, Manager :: context()) .
                             '</span></p>';
                    }

                    $html[] = '</li>';
                    $html[] = '</div>';

                    $count ++;
                }
            }

            return implode($html, "\n");
        }
    }

    /**
     * **************************************************************************************************************
     * Helper Functionality *
     * **************************************************************************************************************
     */

    /**
     * Returns the selected course type or selects the first one from the course type list
     *
     * @throws \Exception
     *
     * @return int
     */
    public function get_selected_course_type()
    {
        if (! isset($this->selected_course_type))
        {
            $selected_course_type_id = $this->get_selected_course_type_parameter_value();
            $course_types = $this->retrieve_course_types();

            $course_type = null;

            if (is_null($selected_course_type_id))
            {
                do
                {
                    $course_type = $course_types->next_result();
                }
                while (! is_null($course_type) && ! $this->get_parent()->show_empty_courses() &&
                     $this->count_courses_for_course_type($course_type[CourseType :: PROPERTY_ID]) == 0);

                $course_types->reset();

                $selected_course_type_id = $course_type[CourseType :: PROPERTY_ID];
            }

            if ($selected_course_type_id > 0)
            {
                $course_type = \Chamilo\Application\Weblcms\CourseType\Storage\DataManager :: retrieve_by_id(
                    CourseType :: class_name(),
                    $selected_course_type_id);

                if (! $course_type || ! $course_type->is_active())
                {
                    throw new \Exception(Translation :: get('NoValidCourseTypeSelected'));
                }
            }

            // Register the selected parameter id in the session for later retrieval
            $selected_course_type_id = (is_null($course_type)) ? $selected_course_type_id : $course_type->get_id();
            Session :: register(self :: PARAM_SELECTED_COURSE_TYPE, $selected_course_type_id);

            $this->selected_course_type = $course_type;
        }

        return $this->selected_course_type;
    }

    /**
     * Returns the id of the selected course type
     *
     * @return int
     */
    public function get_selected_course_type_id()
    {
        $selected_course_type = $this->get_selected_course_type();

        if ($selected_course_type)
        {
            return $selected_course_type->get_id();
        }

        return 0;
    }

    /**
     * Retrieve the selected course type parameter value either from the request or the session
     *
     * @return int
     */
    protected function get_selected_course_type_parameter_value()
    {
        $selected_course_type = Request :: get(self :: PARAM_SELECTED_COURSE_TYPE);

        if (! isset($selected_course_type))
        {
            $selected_course_type = Session :: retrieve(self :: PARAM_SELECTED_COURSE_TYPE);
        }

        return $selected_course_type;
    }

    /**
     * Returns the url for the selected course type
     *
     * @param int $course_type_id
     * @return string
     */
    protected function get_course_type_url($course_type_id)
    {
        $parameters = array();
        $parameters[self :: PARAM_SELECTED_COURSE_TYPE] = $course_type_id;

        return $this->get_parent()->get_url($parameters);
    }

    /**
     * Retrieves the actions for the given course user category
     *
     * @param mixed[string] $course_type_user_category
     * @param int $offset
     * @param int $count
     *
     * @return string
     */
    public function get_course_type_user_category_actions($course_type_user_category = null, $offset = 1, $count = 1)
    {
        if (method_exists($this->get_parent(), 'get_course_type_user_category_actions'))
        {
            return $this->get_parent()->get_course_type_user_category_actions(
                $course_type_user_category,
                $offset,
                $count);
        }
    }

    /**
     * Retrieves the actions for the given course
     *
     * @param mixed[string] $course_type_user_category
     * @param course\Course $course
     * @param int $offset
     * @param int $count
     *
     * @return string
     */
    public function get_course_actions($course_type_user_category = null, Course $course = null, $offset = 1, $count = 1)
    {
        if (method_exists($this->get_parent(), 'get_course_actions'))
        {
            return $this->get_parent()->get_course_actions($course_type_user_category, $course, $offset, $count);
        }
    }
}
