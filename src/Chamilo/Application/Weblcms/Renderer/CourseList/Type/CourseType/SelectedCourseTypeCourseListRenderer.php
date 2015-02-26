<?php
namespace Chamilo\Application\Weblcms\Renderer\CourseList\Type\CourseType;

use Chamilo\Application\Weblcms\Renderer\CourseList\Type\CourseTypeCourseListRenderer;
/*
 * To change this template, choose Tools | Templates and open the template in the editor.
 */

/**
 * Description of selected_course_type_course_list_renderer
 * 
 * @author jevdheyd
 */
class SelectedCourseTypeCourseListRenderer extends CourseTypeCourseListRenderer
{

    public function __construct($parent, $target = '', $selected_course_type)
    {
        parent :: __construct($parent, $target);
        
        $this->selected_course_type = $selected_course_type;
    }

    public function as_html()
    {
        $this->courses = $this->retrieve_courses();
        
        $course_user_categories = $this->retrieve_course_user_categories_for_course_type();
        
        $html = array();
        
        while ($course_user_category = $course_user_categories->next_result())
        {
            $html[] = $this->display_courses_for_course_type_user_category($course_user_category);
        }
        
        return implode("\n", $html);
    }
}
?>
