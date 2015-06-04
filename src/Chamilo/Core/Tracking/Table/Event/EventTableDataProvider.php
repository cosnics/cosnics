<?php
namespace Chamilo\Core\Tracking\Table\Event;

use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableDataProvider;

class EventTableDataProvider extends DataClassTableDataProvider
{

    public function retrieve_data($condition, $offset, $count, $order_property = null)
    {
        return $this->get_component()->retrieve_events($condition, $offset, $count, $order_property);
    }

    /**
     * Gets the number of learning objects in the table
     * 
     * @return int
     */
    public function count_data($condition)
    {
        return $this->get_component()->count_events($condition);
    }
}
