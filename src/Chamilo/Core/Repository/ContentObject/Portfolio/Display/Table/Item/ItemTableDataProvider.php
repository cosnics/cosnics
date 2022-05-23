<?php
namespace Chamilo\Core\Repository\ContentObject\Portfolio\Display\Table\Item;

use ArrayIterator;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableDataProvider;

/**
 * Portfolio item table data provider
 *
 * @package repository\content_object\portfolio\display
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ItemTableDataProvider extends DataClassTableDataProvider
{

    /**
     * Counts the data
     *
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     *
     * @return integer
     */
    public function count_data($condition)
    {
        $node = $this->get_component()->get_current_node();

        return count($node->get_children());
    }

    /**
     * Returns the data as a resultset
     *
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     * @param int $offset
     * @param int $count
     * @param \Chamilo\Libraries\Storage\Query\OrderBy $order_property
     *
     * @return \ArrayIterator
     */
    public function retrieve_data($condition, $offset, $count, $order_property = null)
    {
        $node = $this->get_component()->get_current_node();

        return new ArrayIterator($node->get_children());
    }
}