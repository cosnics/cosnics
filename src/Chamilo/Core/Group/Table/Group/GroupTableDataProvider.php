<?php
namespace Chamilo\Core\Group\Table\Group;

use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableDataProvider;

class GroupTableDataProvider extends DataClassTableDataProvider
{

    public function retrieve_data($condition, $offset, $count, $order_property = null)
    {
        return $this->get_component()->retrieve_groups($condition, $offset, $count, $order_property);
    }

    public function count_data($condition)
    {
        return $this->get_component()->count_groups($condition);
    }
}
