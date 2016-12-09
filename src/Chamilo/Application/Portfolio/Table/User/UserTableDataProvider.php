<?php
namespace Chamilo\Application\Portfolio\Table\User;

use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableDataProvider;

/**
 * Table data provider
 * 
 * @package application\portfolio
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
     * @param \libraries\ObjectTableOrder[] $order_property
     *
     * @return \libraries\storage\ResultSet
     */
    public function retrieve_data($condition, $offset, $count, $order_property = null)
    {
        return \Chamilo\Core\User\Storage\DataManager::retrieve_active_users(
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
        return \Chamilo\Core\User\Storage\DataManager::count_active_users($condition);
    }
}