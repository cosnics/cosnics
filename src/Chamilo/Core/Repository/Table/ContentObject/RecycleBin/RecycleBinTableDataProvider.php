<?php
namespace Chamilo\Core\Repository\Table\ContentObject\RecycleBin;

use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Storage\DataManager;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableDataProvider;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;

class RecycleBinTableDataProvider extends DataClassTableDataProvider
{

    public function retrieve_data($condition, $offset, $count, $order_property = null)
    {
        if (is_null($order_property))
        {
            $order_property = array();
        }
        elseif (! is_array($order_property))
        {
            $order_property = array($order_property);
        }
        
        $parameters = new DataClassRetrievesParameters($condition, $count, $offset, $order_property);
        
        $objects = DataManager::retrieve_active_content_objects(ContentObject::class, $parameters);
        
        return $objects;
    }

    public function count_data($condition)
    {
        $parameters = new DataClassCountParameters($condition);
        
        return DataManager::count_active_content_objects(ContentObject::class, $parameters);
    }
}
