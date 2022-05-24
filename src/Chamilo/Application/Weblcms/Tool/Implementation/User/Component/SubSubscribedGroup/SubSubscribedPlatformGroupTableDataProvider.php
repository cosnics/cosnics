<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\User\Component\SubSubscribedGroup;

use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Core\Group\Storage\DataManager;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableDataProvider;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\OrderBy;

/**
 * * *************************************************************************** Data privider for a course subgroup
 * browser table.
 *
 * @author Stijn Van Hoecke ****************************************************************************
 */
class SubSubscribedPlatformGroupTableDataProvider extends DataClassTableDataProvider
{

    public function countData(?Condition $condition = null): int
    {
        return DataManager::count(
            Group::class, new DataClassCountParameters($condition)
        );
    }

    public function retrieveData(
        ?Condition $condition = null, ?int $offset = null, ?int $count = null, ?OrderBy $orderBy = null
    )
    {
        return DataManager::retrieves(
            Group::class, new DataClassRetrievesParameters($condition, $count, $offset, $orderBy)
        );
    }
}
