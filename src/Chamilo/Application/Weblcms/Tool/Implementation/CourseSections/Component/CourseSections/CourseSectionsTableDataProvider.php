<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\CourseSections\Component\CourseSections;

use Chamilo\Application\Weblcms\Storage\DataClass\CourseSection;
use Chamilo\Application\Weblcms\Storage\DataManager;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableDataProvider;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\OrderProperty;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;

/**
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
     * Gets the number of courses in the table
     *
     * @return int
     */
    public function count_data($condition)
    {
        return DataManager::count(
            CourseSection::class, new DataClassCountParameters($condition)
        );
    }

    /**
     * Gets the courses
     *
     * @param $offset int
     * @param $count int
     * @param $order_property string
     *
     * @return \Chamilo\Libraries\Storage\Iterator\DataClassIterator A set of matching courses.
     */
    public function retrieve_data($condition, $offset, $count, $order_property = null)
    {
        $order_property = new OrderBy(array(
            new OrderProperty(
                new PropertyConditionVariable(CourseSection::class, CourseSection::PROPERTY_DISPLAY_ORDER)
            )
        ));

        return DataManager::retrieves(
            CourseSection::class, new DataClassRetrievesParameters($condition, $count, $offset, $order_property)
        );
    }
}
