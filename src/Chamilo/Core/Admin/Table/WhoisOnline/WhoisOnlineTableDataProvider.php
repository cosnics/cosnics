<?php
namespace Chamilo\Core\Admin\Table\WhoisOnline;

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
 * @package admin.lib.admin_manager.component.whois_online_table
 */

/**
 * Data provider for a user browser table.
 * This class implements some functions to allow user browser tables to retrieve
 * information about the users to display.
 */
class WhoisOnlineTableDataProvider extends DataClassTableDataProvider
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
        $parameters = new DataClassRetrievesParameters($condition, $count, $offset, $orderBy);

        return DataManager::retrieves(
            User::class, $parameters
        );
    }
}
