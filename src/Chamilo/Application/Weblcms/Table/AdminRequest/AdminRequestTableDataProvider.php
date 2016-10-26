<?php
namespace Chamilo\Application\Weblcms\Table\AdminRequest;

use Chamilo\Application\Weblcms\Storage\DataClass\CourseRequest;
use Chamilo\Application\Weblcms\Storage\DataManager;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableDataProvider;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;

/**
 * $Id: admin_course_type_browser_table_data_provider.class.php 218 2010-03-10 14:21:26Z yannick $
 *
 * @package application.lib.weblcms.weblcms_manager.component.admin_course_type_browser
 */
/**
 * Data provider for a repository browser table. This class implements some functions to allow repository browser tables
 * to retrieve information about the learning objects to display.
 */
class AdminRequestTableDataProvider extends DataClassTableDataProvider
{

    /**
     * Gets the coursetypes
     *
     * @param $offset int
     * @param $count int
     * @param $order_property string
     * @return ResultSet A set of matching coursetypes.
     */
    public function retrieve_data($condition, $offset, $count, $order_property = null)
    {
        return DataManager :: retrieves(
            CourseRequest :: class_name(),
            new DataClassRetrievesParameters($condition, $count, $offset, $order_property));
    }

    /**
     * Gets the number of coursetypes in the table
     *
     * @return int
     */
    public function count_data($condition)
    {
        return DataManager :: count(CourseRequest :: class_name(), new DataClassCountParameters($condition));
    }
}
