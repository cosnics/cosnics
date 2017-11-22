<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\User\Component\Group;

use Chamilo\Core\Group\Storage\DataClass\GroupRelUser;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableDataProvider;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;

/**
 * * *************************************************************************** Data provider for a platform group rel
 * user browser table.
 *
 * @author Stijn Van Hoecke ****************************************************************************
 */
class PlatformGroupRelUserTableDataProvider extends DataClassTableDataProvider
{

    public function retrieve_data($condition, $offset, $count, $order_property = null)
    {
        return \Chamilo\Core\Group\Storage\DataManager::retrieves(
            GroupRelUser::class_name(),
            new DataClassRetrievesParameters($condition, $count, $offset, $order_property));
    }

    public function count_data($condition)
    {
        return \Chamilo\Core\Group\Storage\DataManager::count(
            GroupRelUser::class_name(),
            new DataClassCountParameters($condition));
    }
}
