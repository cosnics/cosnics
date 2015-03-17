<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Table\Request;

use Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Storage\DataClass\Request;
use Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Storage\DataManager;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableDataProvider;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;

/**
 * Data provider for ephorus requests browser table.
 * 
 * @author Tom Goethals - Hogeschool Gent
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class RequestTableDataProvider extends DataClassTableDataProvider
{

    /**
     * Gets the objects to display in the table.
     * For now, objects are composed in the code itself from several source
     * objects.
     * 
     * @param $offset
     * @param $count
     * @param null $order_property
     *
     * @return mixed
     */
    public function retrieve_data($condition, $offset, $count, $order_property = null)
    {
        if ($order_property == null)
        {
            $order_property = new OrderBy(
                new PropertyConditionVariable(Request :: class_name(), Request :: PROPERTY_REQUEST_TIME));
        }
        return DataManager :: retrieve_results_content_objects_by_params(
            new DataClassRetrievesParameters($this->get_condition(), $count, $offset, $order_property));
    }

    /**
     * Returns the count of the objects
     * 
     * @return int
     */
    public function count_data($condition)
    {
        return DataManager :: count_results_content_objects_by_params(new DataClassCountParameters($condition));
    }
}
