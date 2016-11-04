<?php
namespace Chamilo\Core\User\Table\Approval;

use Chamilo\Core\User\Storage\DataManager;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableDataProvider;

/**
 * Data provider for a user browser table.
 * This class implements some functions to allow user browser tables to retrieve
 * information about the users to display.
 */
class UserApprovalTableDataProvider extends DataClassTableDataProvider
{

    /**
     * Gets the users
     * 
     * @param $user String
     * @param $category String
     * @param $offset int
     * @param $count int
     * @param $order_property string
     * @return ResultSet A set of matching learning objects.
     */
    public function retrieve_data($condition, $offset, $count, $order_property = null)
    {
        return DataManager :: retrieve_approval_users($condition, $count, $offset, $order_property);
    }

    /**
     * Gets the number of users in the table
     * 
     * @return int
     */
    public function count_data($condition)
    {
        return DataManager :: count_approval_users($condition);
    }
}
