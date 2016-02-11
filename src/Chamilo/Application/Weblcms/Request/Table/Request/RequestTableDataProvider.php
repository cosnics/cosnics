<?php
namespace Chamilo\Application\Weblcms\Request\Table\Request;

use Chamilo\Application\Weblcms\Request\Storage\DataClass\Request;
use Chamilo\Application\Weblcms\Request\Storage\DataManager;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableDataProvider;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;

class RequestTableDataProvider extends DataClassTableDataProvider
{

    function retrieve_data($condition, $offset, $count, $order_property = null)
    {
        $parameters = new DataClassRetrievesParameters($condition, $count, $offset, $order_property);
        return DataManager :: retrieves(Request :: class_name(), $parameters);
    }

    function count_data($condition)
    {
        $parameters = new DataClassCountParameters($condition);
        return DataManager :: count(Request :: class_name(), $parameters);
    }
}
?>