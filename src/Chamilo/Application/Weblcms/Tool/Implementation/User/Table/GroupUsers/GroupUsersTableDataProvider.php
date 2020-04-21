<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\User\Table\GroupUsers;

use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Core\User\Storage\DataManager;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableDataProvider;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\ResultSet\ResultSet;

/**
 * Table to display a list of users subscribed to a group
 */
class GroupUsersTableDataProvider extends DataClassTableDataProvider
{

    /**
     * Gets the users
     *
     * @param int $offset
     * @param int $count
     * @param string $order_property
     * @return ResultSet A set of matching learning objects.
     */
    public function retrieve_data($condition, $offset, $count, $order_property = null)
    {
        return DataManager::retrieves(
            User::class_name(),
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
            User::class_name(),
            new DataClassCountParameters($condition));
    }
}
