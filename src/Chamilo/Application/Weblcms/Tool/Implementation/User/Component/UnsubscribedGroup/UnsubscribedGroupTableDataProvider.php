<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\User\Component\UnsubscribedGroup;

use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableDataProvider;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;

/**
 * * *************************************************************************** Data provider for an unsubscribed
 * course group browser table.
 *
 * @author Stijn Van Hoecke ****************************************************************************
 */
class UnsubscribedGroupTableDataProvider extends DataClassTableDataProvider
{

    public function retrieve_data($condition, $offset, $count, $order_property = null)
    {
        return \Chamilo\Core\Group\Storage\DataManager :: retrieves(
            Group :: class_name(),
            new DataClassRetrievesParameters($condition, $count, $offset, $order_property));
    }

    public function count_data($condition)
    {
        return \Chamilo\Core\Group\Storage\DataManager :: count(
            Group :: class_name(),
            new DataClassCountParameters($condition));
    }
}
