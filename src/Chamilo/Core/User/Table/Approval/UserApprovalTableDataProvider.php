<?php
namespace Chamilo\Core\User\Table\Approval;

use Chamilo\Core\User\Storage\DataManager;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableDataProvider;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\OrderBy;

/**
 * Data provider for a user browser table.
 * This class implements some functions to allow user browser tables to retrieve
 * information about the users to display.
 */
class UserApprovalTableDataProvider extends DataClassTableDataProvider
{

    public function countData(?Condition $condition = null): int
    {
        return DataManager::count_approval_users($condition);
    }

    public function retrieveData(
        ?Condition $condition = null, ?int $offset = null, ?int $count = null, ?OrderBy $orderBy = null
    )
    {
        return DataManager::retrieve_approval_users($condition, $count, $offset, $orderBy);
    }
}
