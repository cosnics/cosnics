<?php
namespace Chamilo\Application\Weblcms\Renderer\CourseList;

use Chamilo\Application\Weblcms\Course\Storage\DataClass\Course;
use Chamilo\Application\Weblcms\Course\Storage\DataManager as CourseDataManager;
use Chamilo\Application\Weblcms\CourseSettingsConnector;
use Chamilo\Application\Weblcms\CourseSettingsController;
use Chamilo\Application\Weblcms\Manager;
use Chamilo\Application\Weblcms\Storage\DataClass\CourseSetting;
use Chamilo\Application\Weblcms\Storage\DataClass\CourseTool;
use Chamilo\Application\Weblcms\Storage\DataManager;
use Chamilo\Libraries\Format\Structure\IdentRenderer;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;

/**
 * Course list renderer to render the course list (used in courses home, courses sorter, courses block...)
 * 
 * @author Sven Vanpoucke
 */
class CourseListRenderer
{

    /**
     * The parent on which the course list renderer is running
     */
    private $parent;

    /**
     * Show the what's new icons or not
     * 
     * @var boolean
     */
    private $new_publication_icons;

    /**
     * Link target.
     * 
     * @var string
     */
    private $target = '';

    private $tools;

    public function __construct($parent, $target = '')
    {
        $this->parent = $parent;
        $this->new_publication_icons = false;
        $this->target = $target;
    }

    public function get_parent()
    {
        return $this->parent;
    }

    public function set_parent($parent)
    {
        $this->parent = $parent;
    }

    public function get_user()
    {
        return $this->get_parent()->get_user();
    }

    public function show_new_publication_icons()
    {
        $this->new_publication_icons = true;
    }

    public function hide_new_publication_icons()
    {
        $this->new_publication_icons = false;
    }

    public function get_new_publication_icons()
    {
        return $this->new_publication_icons;
    }

    public function get_target()
    {
        return $this->target;
    }

    public function set_target($target)
    {
        $this->target = $target;
    }

    /**
     * Renders the course list
     */
    public function render()
    {
        echo $this->as_html();
    }

    /**
     * Returns the course list as html
     */
    public function as_html()
    {
        return $this->display_courses();
    }

    private $retrievedCourses;

    /**
     * Retrieves the courses for the user
     */
    protected function retrieve_courses()
    {
        if (! isset($this->retrievedCourses))
        {
            $this->retrievedCourses = CourseDataManager::retrieve_all_courses_from_user(
                $this->get_user(), 
                $this->get_retrieve_courses_condition());
        }
        
        return $this->retrievedCourses;
    }

    public function get_courses()
    {
        return $this->retrieve_courses();
    }

    /**
     * Returns the conditions needed to retrieve the courses
     */
    protected function get_retrieve_courses_condition()
    {
        return null;
    }

    protected function loadCourseSettings($courses)
    {
        $courseIdentifiers = array();
        
        foreach ($courses as $course)
        {
            $courseIdentifiers[] = $course->getId();
        }
        
        $courseSettingsController = CourseSettingsController::getInstance();
        $courseSettingsController->loadSettingsForCoursesByIdentifiers($courseIdentifiers);
    }

    /**
     * Displays the courses
     */
    protected function display_courses()
    {
        $html = array();
        $courses = $this->retrieve_courses()->as_array();
        
        $target = $this->target ? ' target="' . $this->target . '" ' : '';
        
        $this->loadCourseSettings($courses);
        
        if (count($courses) > 0)
        {
            $html[] = '<ul class="list-group">';
            
            $course_settings_controller = CourseSettingsController::getInstance();
            
            if ($this->get_new_publication_icons())
            {
                // Accelerate notification icon generation by querying all courses at ones and storing the results in a
                // cache.
                DataManager::fill_new_publications_cache(
                    $this->get_user(), 
                    DataManager::create_courses_array($courses));
            }
            
            foreach ($courses as $course)
            {
                $id = $course->get_id();
                
                $course_access = $course_settings_controller->get_course_setting(
                    $course, 
                    CourseSettingsConnector::COURSE_ACCESS);
                
                $course_visible = $course_settings_controller->get_course_setting(
                    $course, 
                    CourseSettingsConnector::VISIBILITY);
                
                if (($course_access == CourseSettingsConnector::COURSE_ACCESS_CLOSED || ! $course_visible) &&
                     ! $course->is_course_admin($this->get_user()))
                {
                    continue;
                }
                
                $html[] = '<li class="list-group-item"><a href="' . htmlspecialchars($this->get_course_url($course)) .
                     '"' . $target . '>' . htmlspecialchars($course->get_title()) . '</a>';
                
                if ($this->get_new_publication_icons())
                {
                    $html[] = $this->display_new_publication_icons($course);
                }
                
                $html[] = '</li>';
            }
            $html[] = '</ul>';
        }
        else
        {
            $html[] = $this->get_no_courses_message_as_html();
        }
        
        return implode($html, "\n");
    }

    /**
     * Defines the display of the message when there are no courses to display.
     */
    protected function get_no_courses_message_as_html()
    {
        return '<div class="normal-message">' . Translation::get('NoCourses') . '</div>';
    }

    private function getTools()
    {
        if (! isset($this->tools))
        {
            $this->tools = DataManager::retrieves(CourseTool::class_name(), new DataClassRetrievesParameters())->as_array();
        }
        
        return $this->tools;
    }

    /**
     * Displays the what's new icons
     * 
     * @param $course Course
     */
    protected function display_new_publication_icons(Course $course)
    {
        $html = array();
        $target = $this->target ? ' target="' . $this->target . '" ' : '';
        
        $course_settings_controller = CourseSettingsController::getInstance();
        
        foreach ($this->getTools() as $tool)
        {
            $active = $course_settings_controller->get_course_setting(
                $course, 
                CourseSetting::COURSE_SETTING_TOOL_ACTIVE, 
                $tool->get_id());
            
            $visible = $course_settings_controller->get_course_setting(
                $course, 
                CourseSetting::COURSE_SETTING_TOOL_VISIBLE, 
                $tool->get_id());
            
            $hasNewPublications = DataManager::tool_has_new_publications($tool->get_name(), $this->get_user(), $course);
            
            if ($active && $visible && $hasNewPublications)
            {
                $identRenderer = new IdentRenderer($tool->getContext(), true, false, IdentRenderer::SIZE_XS);
                $toolUrl = htmlspecialchars($this->get_tool_url($tool->get_name(), $course));
                
                $html[] = '<a href="' . $toolUrl . '"' . $target . '>';
                $html[] = $identRenderer->render();
                $html[] = '</a>';
            }
        }
        
        return implode($html, "\n");
    }

    /**
     * Gets the url from the given course
     * 
     * @param $course Course
     */
    public function get_course_url(Course $course)
    {
        $parameters = array();
        $parameters[Manager::PARAM_CONTEXT] = Manager::context();
        $parameters[Manager::PARAM_ACTION] = Manager::ACTION_VIEW_COURSE;
        $parameters[Manager::PARAM_COURSE] = $course->get_id();
        
        return $this->get_parent()->get_link($parameters);
    }

    /**
     * Gets the url from the given tool in the given course
     * 
     * @param $tool String
     * @param $course Course
     */
    public function get_tool_url($tool, Course $course)
    {
        $parameters = array();
        $parameters[Manager::PARAM_CONTEXT] = Manager::context();
        $parameters[Manager::PARAM_ACTION] = Manager::ACTION_VIEW_COURSE;
        $parameters[Manager::PARAM_COURSE] = $course->get_id();
        $parameters[Manager::PARAM_TOOL] = $tool;
        
        return $this->get_parent()->get_link($parameters);
    }
}
