<?php
namespace Chamilo\Core\User\Table\Admin;

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
 * @package user.lib.user_manager.component.admin_user_browser
 */

/**
 * Data provider for a user browser table.
 * This class implements some functions to allow user browser tables to retrieve
 * information about the users to display.
 */
class AdminUserTableDataProvider extends DataClassTableDataProvider
{

    public function countData(?Condition $condition = null): int
    {
        return DataManager::count(User::class, new DataClassCountParameters($condition));
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
