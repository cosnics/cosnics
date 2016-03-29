<?php
namespace Chamilo\Application\Weblcms\CourseType\Table\CourseType;

use Chamilo\Application\Weblcms\CourseType\Storage\DataClass\CourseType;
use Chamilo\Application\Weblcms\CourseType\Storage\DataManager;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableDataProvider;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;

/**
 * This class describes a data provider for the course type table
 *
 * @package \application\weblcms\course_type
 * @author Yannick & Tristan
 * @author Sven Vanpoucke - Hogeschool Gent - Refactoring
 */
class CourseTypeTableDataProvider extends DataClassTableDataProvider
{

    /**
     * **************************************************************************************************************
     * Inherited Functionality *
     * **************************************************************************************************************
     */

    /**
     * Retrieves the objects for this table
     *
     * @param $offset int
     * @param $count int
     * @param $order_property String
     *
     * @return \libraries\storage\ResultSet
     */
    public function retrieve_data($condition, $offset, $count, $order_property = null)
    {
        if ($order_property == null)
        {
            $order_property = new OrderBy(
                new PropertyConditionVariable(CourseType :: class_name(), CourseType :: PROPERTY_DISPLAY_ORDER));
        }

        $parameters = new DataClassRetrievesParameters($condition, $count, $offset, $order_property);

        return DataManager :: retrieves(CourseType :: class_name(), $parameters);
    }

    /**
     * Counts the number of objects for this table
     *
     * @return int
     */
    public function count_data($condition)
    {
        return DataManager :: count(CourseType :: class_name(), $condition);
    }
}
