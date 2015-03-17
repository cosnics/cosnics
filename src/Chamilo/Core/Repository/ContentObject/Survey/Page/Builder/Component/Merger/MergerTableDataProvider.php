<?php
namespace Chamilo\Core\Repository\ContentObject\Survey\Page\Builder\Component\Merger;

use Chamilo\Core\Repository\ContentObject\Survey\Page\Storage\DataClass\Page;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableDataProvider;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;

class MergerTableDataProvider extends DataClassTableDataProvider
{

    function retrieve_data($condition, $offset, $count, $order_property = null)
    {
        $parameters = new DataClassRetrievesParameters($condition, $count, $offset, $order_property);
        return \Chamilo\Core\Repository\Storage\DataManager :: retrieve_content_objects(
            Page :: class_name(), 
            $parameters);
    }

    function count_data($condition)
    {
        return \Chamilo\Core\Repository\Storage\DataManager :: count_content_objects(
            Page :: class_name(), 
            $condition);
    }
}
?>