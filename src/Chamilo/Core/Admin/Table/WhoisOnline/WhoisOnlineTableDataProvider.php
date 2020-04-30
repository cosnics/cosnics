<?php
namespace Chamilo\Core\Admin\Table\WhoisOnline;

use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Core\User\Storage\DataManager;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableDataProvider;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;

/**
 *
 * @package admin.lib.admin_manager.component.whois_online_table
 */
/**
 * Data provider for a user browser table.
 * This class implements some functions to allow user browser tables to retrieve
 * information about the users to display.
 */
class WhoisOnlineTableDataProvider extends DataClassTableDataProvider
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
        $parameters = new DataClassRetrievesParameters($condition, $count, $offset, $order_property);
        return DataManager::retrieves(
            User::class,
            $parameters);
    }

    /**
     * Gets the number of users in the table
     *
     * @return int
     */
    public function count_data($condition)
    {
        return DataManager::count(
            User::class,
            new DataClassCountParameters($condition));
    }
}
