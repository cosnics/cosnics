<?php
namespace Chamilo\Core\Repository\Table\ContentObject\Version;

use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Storage\DataManager;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableDataProvider;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;

class VersionTableDataProvider extends DataClassTableDataProvider
{

    public function retrieve_data($condition, $offset, $count, $order_property = null)
    {
        if ($order_property == null)
        {
            $order_property = new OrderBy(
                new PropertyConditionVariable(ContentObject::class_name(), ContentObject::PROPERTY_ID, SORT_DESC));
        }
        
        $parameters = new DataClassRetrievesParameters($condition, $count, $offset, $order_property);
        
        return DataManager::retrieve_content_objects(ContentObject::class_name(), $parameters);
    }

    public function count_data($condition)
    {
        $parameters = new DataClassCountParameters($condition);
        
        return DataManager::count_content_objects(ContentObject::class_name(), $parameters);
    }
}
