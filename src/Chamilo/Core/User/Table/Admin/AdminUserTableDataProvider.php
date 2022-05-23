<?php
namespace Chamilo\Core\User\Table\Admin;

use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Core\User\Storage\DataManager;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableDataProvider;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;

/**
 *
 * @package user.lib.user_manager.component.admin_user_browser
 */

/**
 * Data provider for a user browser table.
 * This class implements some functions to allow user browser tables to retrieve
 * information about the users to display.
 */
class AdminUserTableDataProvider extends DataClassTableDataProvider
{

    /**
     * Gets the number of users in the table
     *
     * @return int
     */
    public function count_data($condition)
    {
        return DataManager::count(User::class, new DataClassCountParameters($condition));
    }

    /**
     * Gets the users
     *
     * @param $user String
     * @param $category String
     * @param $offset int
     * @param $count int
     * @param $order_property string
     *
     * @return \Chamilo\Libraries\Storage\Iterator\DataClassIterator A set of matching learning objects.
     */
    public function retrieve_data($condition, $offset, $count, $order_property = null)
    {
        return DataManager::retrieves(
            User::class, new DataClassRetrievesParameters($condition, $count, $offset, $order_property)
        );
    }
}
