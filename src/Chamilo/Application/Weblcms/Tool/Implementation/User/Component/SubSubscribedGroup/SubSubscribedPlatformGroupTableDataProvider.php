<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\User\Component\SubSubscribedGroup;

use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Core\Group\Storage\DataManager;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableDataProvider;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;

/**
 * * *************************************************************************** Data privider for a course subgroup
 * browser table.
 *
 * @author Stijn Van Hoecke ****************************************************************************
 */
class SubSubscribedPlatformGroupTableDataProvider extends DataClassTableDataProvider
{

    public function retrieve_data($condition, $offset, $count, $order_property = null)
    {
        return DataManager::retrieves(
            Group::class_name(),
            new DataClassRetrievesParameters($condition, $count, $offset, $order_property));
    }

    public function count_data($condition)
    {
        return DataManager::count(
            Group::class_name(),
            new DataClassCountParameters($condition));
    }
}
