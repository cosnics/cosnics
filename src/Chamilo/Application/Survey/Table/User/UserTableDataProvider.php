<?php
namespace Chamilo\Application\Survey\Table\User;

use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableDataProvider;

class UserTableDataProvider extends DataClassTableDataProvider
{

    function retrieve_data($condition, $offset, $count, $order_property = null, $order_direction = null)
    {
        return \Chamilo\Core\User\Storage\DataManager :: retrieve_active_users(
            $condition, 
            $count, 
            $offset, 
            $order_property, 
            $order_direction);
    }

    function count_data($condition)
    {
        return \Chamilo\Core\User\Storage\DataManager :: count_active_users($condition);
    }
}
?>