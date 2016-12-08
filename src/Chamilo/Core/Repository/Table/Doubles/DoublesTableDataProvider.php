<?php
namespace Chamilo\Core\Repository\Table\Doubles;

use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Storage\DataManager;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableDataProvider;

class DoublesTableDataProvider extends DataClassTableDataProvider
{

    public function retrieve_data($condition, $offset, $count, $order_property = null)
    {
        $condition = $condition[$this->get_table()->is_detail()];
        
        if ($this->get_table()->is_detail())
        {
            return DataManager::retrieve_active_content_objects(ContentObject::class_name(), $condition);
        }
        
        return DataManager::retrieve_doubles_in_repository($condition, $count, $offset, $order_property);
    }

    public function count_data($condition)
    {
        $condition = $condition[$this->get_table()->is_detail()];
        
        if ($this->get_table()->is_detail())
        {
            return DataManager::count_active_content_objects(ContentObject::class_name(), $condition);
        }
        
        return DataManager::count_doubles_in_repository($condition);
    }
}
