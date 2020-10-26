<?php
namespace Chamilo\Core\Repository\ContentObject\Portfolio\Display\Table\User;

use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableDataProvider;

/**
 * User table data provider
 * 
 * @package repository\content_object\portfolio\display
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class UserTableDataProvider extends DataClassTableDataProvider
{

    /**
     * Returns the data as a resultset
     * 
     * @param \libraries\storage\Condition $condition
     * @param $condition
     * @param int $offset
     * @param int $count
     * @param ObjectTableOrder[] $order_property
     *
     * @return \Chamilo\Libraries\Storage\Iterator\DataClassIterator
     */
    public function retrieve_data($condition, $offset, $count, $order_property = null)
    {
        return $this->get_component()->get_parent()->retrieve_portfolio_possible_view_users(
            $condition, 
            $count, 
            $offset, 
            $order_property);
    }

    /**
     * Counts the data
     * 
     * @param \libraries\storage\Condition $condition
     *
     * @return int
     */
    public function count_data($condition)
    {
        return $this->get_component()->get_parent()->count_portfolio_possible_view_users($condition);
    }
}