<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Table\CourseGroup;

use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroup;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataManager;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableDataProvider;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;

/**
 *
 * @package application.lib.weblcms.tool.course_group.component.user_table
 */
class CourseGroupTableDataProvider extends DataClassTableDataProvider
{

    /**
     * Gets the users
     *
     * @param $offset int
     * @param $count int
     * @param $order_property string
     * @return ResultSet A set of matching learning objects.
     */
    public function retrieve_data($condition, $offset, $count, $order_property = null)
    {
        return DataManager::retrieves(
            CourseGroup::class_name(),
            new DataClassRetrievesParameters($condition, $count, $offset, $order_property));
    }

    /**
     * Gets the number of users in the table
     *
     * @return int
     */
    public function count_data($condition)
    {
        return DataManager::count(CourseGroup::class_name(), new DataClassCountParameters($condition));
    }
}
