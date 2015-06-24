<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\CourseSections\Component\CourseSections;

use Chamilo\Application\Weblcms\Storage\DataClass\CourseSection;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableDataProvider;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;

/**
 * $Id: course_sections_browser_table_data_provider.class.php 216 2009-11-13 14:08:06Z kariboe $
 * 
 * @package application.lib.weblcms.tool.course_sections.component.course_sections_browser
 */
/**
 * Data provider for a repository browser table.
 * This class implements some functions to allow repository browser tables
 * to retrieve information about the learning objects to display.
 */
class CourseSectionsTableDataProvider extends DataClassTableDataProvider
{

    /**
     * Gets the courses
     * 
     * @param $offset int
     * @param $count int
     * @param $order_property string
     * @return ResultSet A set of matching courses.
     */
    public function retrieve_data($condition, $offset, $count, $order_property = null)
    {
        $order_property = array(
            new OrderBy(
                new PropertyConditionVariable(CourseSection :: class_name(), CourseSection :: PROPERTY_DISPLAY_ORDER)));
        
        return \Chamilo\Application\Weblcms\Storage\DataManager :: retrieves(
            CourseSection :: class_name(), 
            new DataClassRetrievesParameters($condition, $count, $offset, $order_property));
    }

    /**
     * Gets the number of courses in the table
     * 
     * @return int
     */
    public function count_data($condition)
    {
        return \Chamilo\Application\Weblcms\Storage\DataManager :: count(CourseSection :: class_name(), $condition);
    }
}
