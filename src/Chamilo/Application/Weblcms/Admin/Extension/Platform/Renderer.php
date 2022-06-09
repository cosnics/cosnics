<?php
namespace Chamilo\Application\Weblcms\Admin\Extension\Platform;

use Chamilo\Application\Weblcms\Admin\Extension\Platform\Storage\DataManager;
use Chamilo\Application\Weblcms\Course\Storage\DataClass\Course;
use Chamilo\Application\Weblcms\CourseSettingsConnector;
use Chamilo\Application\Weblcms\CourseSettingsController;
use Chamilo\Application\Weblcms\CourseType\Storage\DataClass\CourseType;
use Chamilo\Application\Weblcms\Renderer\CourseList\CourseListRenderer;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Tabs\Link\LinkTab;
use Chamilo\Libraries\Format\Tabs\Link\LinkTabsRenderer;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Session\Session;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Translation\Translation;
use Exception;

/**
 * Course list renderer to render the course list with tabs for the course types (used in courses home, courses sorter)
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class Renderer extends CourseListRenderer
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
     * @var \Doctrine\Common\Collections\ArrayCollection<CourseType>
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
            self::PARAM_SELECTED_COURSE_TYPE, $this->get_selected_course_type_parameter_value()
        );

        return $this->display_course_types();
    }

    /**
     * **************************************************************************************************************
     * Retrieve & Parse Functionality *
     * **************************************************************************************************************
     */

    protected function count_courses_for_course_type($selected_course_type_id)
    {
        return count($this->get_courses_for_course_type($selected_course_type_id));
    }

    /**
     * Shows the tabs of the course types Show the course list for the selected tab
     *
     * @return string
     */
    protected function display_course_types()
    {
        $renderer_name = ClassnameUtilities::getInstance()->getClassnameFromObject($this, true);
        $course_tabs = new LinkTabsRenderer($renderer_name);

        $selected_course_type_id = $this->get_selected_course_type_id();

        $created_tabs = [];

        foreach ($this->course_types as $course_type)
        {
            $created_tabs[$course_type[CourseType::PROPERTY_ID]] = new LinkTab(
                $course_type[CourseType::PROPERTY_ID], $course_type[CourseType::PROPERTY_TITLE], null,
                $this->get_course_type_url($course_type[CourseType::PROPERTY_ID])
            );

            if ($this->count_courses_for_course_type($course_type[CourseType::PROPERTY_ID]) > 0)
            {
                $course_tabs->addTab($created_tabs[$course_type[CourseType::PROPERTY_ID]]);
            }
        }

        // Add an extra tab for the no course type
        $created_tabs[0] =
            new LinkTab(0, Translation::get('NoCourseType'), null, $this->get_course_type_url(0));

        if ($this->count_courses_for_course_type(0) > 0)
        {
            $course_tabs->addTab($created_tabs[0]);
        }

        if ($course_tabs->size() > 0)
        {
            if ($created_tabs[$selected_course_type_id])
            {
                $created_tabs[$selected_course_type_id]->set_selected(true);
            }

            $content = $this->display_courses_for_course_type($selected_course_type_id);
            $course_tabs->set_content($content);

            return $course_tabs->render();
        }
        else
        {
            return '<div class="normal-message" style="text-align: center;">' . Translation::get('NoCourses') .
                '</div>';
        }
    }

    /**
     * Displays the courses for a user course category
     *
     * @param mixed[string] $course_type_user_category
     *
     * @return string
     */
    protected function display_courses_for_course_type($selected_course_type_id)
    {
        $courses = $this->get_courses_for_course_type($selected_course_type_id);

        $size = count($courses);

        $html = [];

        if ($size > 0)
        {
            $course_settings_controller = CourseSettingsController::getInstance();

            $html[] = '<ul>';
            $count = 0;

            foreach ($courses as $course_properties)
            {
                $course = DataClass::factory(Course::class, $course_properties);

                $course_id = $course->get_id();

                $course_visible = $course_settings_controller->get_course_setting(
                    $course->get_id(), CourseSettingsConnector::VISIBILITY
                );

                $locked = '';
                $text_style = '';
                $html[] = '<div style="float:left;">';

                $url = $this->get_course_url($course);

                $course_access = $course_settings_controller->get_course_setting(
                    $course_id, CourseSettingsConnector::COURSE_ACCESS
                );

                $course_closed = $course_access == CourseSettingsConnector::COURSE_ACCESS_CLOSED;

                if ($course_closed)
                {
                    $glyph = new FontAwesomeGlyph('lock');
                    $html[] = $glyph->render();
                }

                if ($course_visible)
                {
                    $glyph = new FontAwesomeGlyph('user');
                }
                else
                {
                    $glyph = new FontAwesomeGlyph('user', array('text-muted'));
                }

                if (!$course_visible)
                {
                    $text_style .= $this->get_invisible_text_style();
                }

                $html[] = '<li style="list-style: none; margin-bottom: 5px; margin-left: 15px;' . $text_style . '">';
                $html[] = $glyph->render();
                $html[] = '<a style="top: -2px; position: relative; ' . $text_style . '" href="' . $url . '">' .
                    $course->get_title();
                $html[] = '</a>';

                $text = [];
                $text[] = $course->get_visual_code();
                $text[] = \Chamilo\Core\User\Storage\DataManager::get_fullname_from_user(
                    $course->get_titular_id(), Translation::get('NoTitular')
                );

                if (count($text) > 0)
                {
                    $html[] = '<br />' . implode(' - ', $text);
                }

                $html[] = '</li>';
                $html[] = '</div>';
                $html[] = '<div style="clear: both;"></div>';

                $count ++;
            }

            $html[] = '</ul>';

            return implode(PHP_EOL, $html);
        }
    }

    /**
     * Returns the url for the selected course type
     *
     * @param int $course_type_id
     *
     * @return string
     */
    protected function get_course_type_url($course_type_id)
    {
        $parameters = [];
        $parameters[self::PARAM_SELECTED_COURSE_TYPE] = $course_type_id;

        return $this->get_parent()->get_url($parameters);
    }

    protected function get_courses_for_course_type($selected_course_type_id)
    {
        return $this->courses[$selected_course_type_id];
    }

    /**
     * **************************************************************************************************************
     * Display Functionality *
     * **************************************************************************************************************
     */

    public function get_invisible_text_style()
    {
        return "color: #999;";
    }

    /**
     * Returns the selected course type or selects the first one from the course type list
     *
     * @return int
     * @throws \Exception
     *
     */
    public function get_selected_course_type()
    {
        if (!isset($this->selected_course_type))
        {
            $selected_course_type_id = $this->get_selected_course_type_parameter_value();

            $course_type = null;

            if (is_null($selected_course_type_id))
            {
                do
                {

                    $course_type = $this->course_types->current();
                    $this->course_types->next();
                }
                while (!is_null($course_type) &&
                $this->count_courses_for_course_type($course_type[CourseType::PROPERTY_ID]) == 0);

                $this->course_types->first();

                $selected_course_type_id = $course_type[CourseType::PROPERTY_ID];
            }

            if ($selected_course_type_id > 0)
            {
                $course_type = \Chamilo\Application\Weblcms\CourseType\Storage\DataManager::retrieve_by_id(
                    CourseType::class, $selected_course_type_id
                );

                if (!$course_type || !$course_type->is_active())
                {
                    throw new Exception(Translation::get('NoValidCourseTypeSelected'));
                }
            }

            // Register the selected parameter id in the session for later retrieval
            $selected_course_type_id = (is_null($course_type)) ? $selected_course_type_id : $course_type->get_id();
            Session::register(self::PARAM_SELECTED_COURSE_TYPE, $selected_course_type_id);

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
     * **************************************************************************************************************
     * Helper Functionality *
     * **************************************************************************************************************
     */

    /**
     * Retrieve the selected course type parameter value either from the request or the session
     *
     * @return int
     */
    protected function get_selected_course_type_parameter_value()
    {
        return Request::get(self::PARAM_SELECTED_COURSE_TYPE);
    }

    /**
     * Parsers the courses in a structure in course type / course category
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $courses
     *
     * @return string[][][][]
     */
    protected function parse_courses($courses)
    {
        $parsed_courses = [];

        foreach ($courses as $course)
        {
            $parsed_courses[$course[Course::PROPERTY_COURSE_TYPE_ID]][] = $course;
        }

        return $parsed_courses;
    }

    /**
     * Retrieves the course types
     *
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Application\Weblcms\CourseType\Storage\DataClass\CourseType>
     */
    protected function retrieve_course_types()
    {
        return \Chamilo\Application\Weblcms\CourseType\Storage\DataManager::retrieve_active_course_types_with_user_order(
            $this->get_parent()->get_user_id()
        );
    }

    /**
     * Retrieves the courses with course categories Retrieves all the courses for every course type so we can decide
     * whether or not we want to show the course type tabs if required by the parent or courses are available
     *
     * @return string[][][][]
     */
    protected function retrieve_courses()
    {
        $courses = DataManager::retrieve_courses($this->get_parent()->get_user());

        return $this->parse_courses($courses);
    }
}
