<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\User\Table\GroupUsers;

use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Core\User\Storage\DataManager;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableDataProvider;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\OrderBy;

/**
 * Table to display a list of users subscribed to a group
 */
class GroupUsersTableDataProvider extends DataClassTableDataProvider
{
    
    public function countData(?Condition $condition = null): int
    {
        return DataManager::count(
            User::class, new DataClassCountParameters($condition)
        );
    }

    public function retrieveData(
        ?Condition $condition = null, ?int $offset = null, ?int $count = null, ?OrderBy $orderBy = null
    )
    {
        return DataManager::retrieves(
            User::class, new DataClassRetrievesParameters($condition, $count, $offset, $orderBy)
        );
    }
}
