<?php
namespace Chamilo\Core\Repository\External\Table\Export;

use Chamilo\Core\Repository\ContentObject\File\Storage\DataClass\File;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableDataProvider;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;

class ExportTableDataProvider extends DataClassTableDataProvider
{

    public function retrieve_data($condition, $offset, $count, $order_property = null)
    {
        $parameters = new DataClassRetrievesParameters($condition, $count, $offset, $order_property);
        return \Chamilo\Core\Repository\Storage\DataManager :: retrieve_active_content_objects(
            File :: class_name(), 
            $parameters);
    }

    public function count_data($condition)
    {
        $parameters = new DataClassCountParameters($condition);
        return \Chamilo\Core\Repository\Storage\DataManager :: count_active_content_objects(
            (File :: class_name()), 
            $parameters);
    }
}
