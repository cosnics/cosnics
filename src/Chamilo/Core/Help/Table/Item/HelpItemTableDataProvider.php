<?php
namespace Chamilo\Core\Help\Table\Item;

use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableDataProvider;

class HelpItemTableDataProvider extends DataClassTableDataProvider
{

    public function retrieve_data($condition, $offset, $count, $order_property = null)
    {
        return $this->get_component()->retrieve_help_items($condition, $offset, $count, $order_property);
    }

    public function count_data($condition)
    {
        return $this->get_component()->count_help_items($condition);
    }
}
