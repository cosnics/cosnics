<?php
namespace Chamilo\Core\Repository\Viewer\Table\Import;

use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableDataProvider;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;

class ImportTableDataProvider extends DataClassTableDataProvider
{

    public function retrieve_data($condition, $offset, $count, $order_property = null)
    {
        return \Chamilo\Core\Repository\Storage\DataManager :: retrieve_active_content_objects(
            ContentObject :: class_name(), 
            new DataClassRetrievesParameters($condition));
    }

    public function count_data($condition)
    {
        return \Chamilo\Core\Repository\Storage\DataManager :: count_active_content_objects(
            ContentObject :: class_name(), 
            new DataClassCountParameters($condition));
    }
}
