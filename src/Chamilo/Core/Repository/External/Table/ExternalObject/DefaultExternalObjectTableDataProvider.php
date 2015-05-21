<?php
namespace Chamilo\Core\Repository\External\Table\ExternalObject;

use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableDataProvider;

class DefaultExternalObjectTableDataProvider extends DataClassTableDataProvider
{

    public function retrieve_data($condition, $offset, $count, $order_property = null)
    {
        return $this->get_component()->retrieve_external_repository_objects(
            $condition, 
            $order_property, 
            $offset, 
            $count);
    }

    public function count_data($condition)
    {
        return $this->get_component()->count_external_repository_objects($condition);
    }
}
