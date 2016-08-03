<?php
namespace Chamilo\Application\Survey\Export\Table\TrackerTable;

use Chamilo\Application\Survey\Export\Storage\DataClass\Export;
use Chamilo\Application\Survey\Export\Storage\DataManager;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableDataProvider;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;

class ExportTableDataProvider extends DataClassTableDataProvider
{

    function retrieve_data($condition, $offset, $count, $order_property = null)
    {
        $parameters = new DataClassRetrievesParameters($condition, $count, $offset, $order_property);
        return DataManager :: retrieves(Export :: class_name(), $parameters);
    }

    function count_data($condition)
    {
        $parameters = new DataClassCountParameters($condition);
        return DataManager :: count(Export :: class_name(), $parameters);
    }
}
?>