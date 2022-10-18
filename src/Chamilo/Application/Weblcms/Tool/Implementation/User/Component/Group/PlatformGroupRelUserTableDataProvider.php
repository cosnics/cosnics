<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\User\Component\Group;

use Chamilo\Core\Group\Storage\DataClass\GroupRelUser;
use Chamilo\Core\Group\Storage\DataManager;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableDataProvider;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * * *************************************************************************** Data provider for a platform group rel
 * user browser table.
 *
 * @author Stijn Van Hoecke ****************************************************************************
 */
class PlatformGroupRelUserTableDataProvider extends DataClassTableDataProvider
{

    public function countData(?Condition $condition = null): int
    {
        return DataManager::count(
            GroupRelUser::class, new DataClassCountParameters($condition)
        );
    }

    public function retrieveData(
        ?Condition $condition = null, ?int $offset = null, ?int $count = null, ?OrderBy $orderBy = null
    ): ArrayCollection
    {
        return DataManager::retrieves(
            GroupRelUser::class, new DataClassRetrievesParameters($condition, $count, $offset, $orderBy)
        );
    }
}
