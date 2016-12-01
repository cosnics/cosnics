<?php
namespace Chamilo\Application\Weblcms\Course\Component;

use Chamilo\Application\Weblcms\Course\Table\SubscribedCourse\SubscribedCourseTable;
use Chamilo\Libraries\Format\Table\Interfaces\TableSupport;

/**
 * This class describes a browser for the subscribed courses
 * 
 * @package \application\weblcms\course
 * @author Yannick & Tristan
 * @author Sven Vanpoucke - Hogeschool Gent - Refactoring
 */
class BrowseSubscribedCoursesComponent extends BrowseSubscriptionCoursesComponent implements TableSupport
{

    /**
     * **************************************************************************************************************
     * Inherited Functionality *
     * **************************************************************************************************************
     */
    
    /**
     * Returns the course table for this component
     * 
     * @return CourseTable
     */
    protected function get_course_table()
    {
        return new SubscribedCourseTable($this);
    }
}
