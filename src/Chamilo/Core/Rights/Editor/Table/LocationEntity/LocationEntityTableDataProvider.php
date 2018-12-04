<?php
namespace Chamilo\Core\Rights\Editor\Table\LocationEntity;

use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableDataProvider;

/**
 * @package Chamilo\Core\Rights\Editor\Table\LocationEntity
 *
 * @deprecated Should not be needed anymore
 */
abstract class LocationEntityTableDataProvider extends DataClassTableDataProvider
{

    public function retrieve_data($condition, $offset, $count, $order_property = null)
    {
        $condition = $condition[$this->get_table()->get_type()];
        $selected_entity = $this->get_component()->get_selected_entity();
        return $selected_entity->retrieve_entity_items($condition, $offset, $count, $order_property);
    }

    public function count_data($condition)
    {
        $selected_entity = $this->get_component()->get_selected_entity();
        $condition = $condition[$this->get_table()->get_type()];
        return $selected_entity->count_entity_items($condition);
    }
}
