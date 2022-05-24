<?php
namespace Chamilo\Core\Group\Table\SubscribeUser;

use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Core\User\Storage\DataManager;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableDataProvider;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;

/**
 *
 * @package groups.lib.group_manager.component.subscribe_user_browser
 */
/**
 * Data provider for a repository browser table.
 * This class implements some functions to allow repository browser tables
 * to retrieve information about the learning objects to display.
 */
class SubscribeUserTableDataProvider extends DataClassTableDataProvider
{

    /**
     * Gets the users
     *
     * @param int $offset
     * @param int $count
     * @param $order_property
     * @return \Chamilo\Libraries\Storage\Iterator\DataClassIterator A set of matching learning objects.
     */
    public function retrieve_data($condition, $offset, $count, $order_property = null)
    {
        return DataManager::retrieves(
            User::class,
            new DataClassRetrievesParameters($condition, $count, $offset, $order_property));
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
