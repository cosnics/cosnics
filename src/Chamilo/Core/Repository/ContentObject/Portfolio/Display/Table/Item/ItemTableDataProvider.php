<?php
namespace Chamilo\Core\Repository\ContentObject\Portfolio\Display\Table\Item;

use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableDataProvider;
use Chamilo\Libraries\Storage\ResultSet\ArrayResultSet;

/**
 * Portfolio item table data provider
 * 
 * @package repository\content_object\portfolio\display
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ItemTableDataProvider extends DataClassTableDataProvider
{

    /**
     * Returns the data as a resultset
     * 
     * @param \libraries\storage\Condition $condition
     * @param $condition
     * @param int $offset
     * @param int $count
     * @param ObjectTableOrder[] $order_property
     * @return \libraries\storage\ResultSet
     */
    public function retrieve_data($condition, $offset, $count, $order_property = null)
    {
        $node = $this->get_component()->get_current_node();
        return new ArrayResultSet($node->get_children());
    }

    /**
     * Counts the data
     * 
     * @param \libraries\storage\Condition $condition
     * @return int
     */
    public function count_data($condition)
    {
        $node = $this->get_component()->get_current_node();
        return count($node->get_children());
    }
}