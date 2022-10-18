<?php
namespace Chamilo\Core\Group\Table\SubscribeUser;

use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Core\User\Storage\DataManager;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableDataProvider;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Doctrine\Common\Collections\ArrayCollection;

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

    public function countData(?Condition $condition = null): int
    {
        return DataManager::count(
            User::class, new DataClassCountParameters($condition)
        );
    }

    public function retrieveData(
        ?Condition $condition = null, ?int $offset = null, ?int $count = null, ?OrderBy $orderBy = null
    ): ArrayCollection
    {
        return DataManager::retrieves(
            User::class, new DataClassRetrievesParameters($condition, $count, $offset, $orderBy)
        );
    }
}
