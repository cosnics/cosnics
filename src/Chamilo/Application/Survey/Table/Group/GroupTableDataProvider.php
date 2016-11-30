<?php
namespace Chamilo\Application\Survey\Table\Group;

use Chamilo\Application\Survey\Storage\DataManager;
use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableDataProvider;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;

class GroupTableDataProvider extends DataClassTableDataProvider
{

    function retrieve_data($condition, $offset, $count, $order_property = NULL)
    {
        return DataManager::retrieves(
            Group::class_name(), 
            new DataClassRetrievesParameters($condition, $count, $offset, $order_property));
    }

    function count_data($condition)
    {
        return DataManager::count(Group::class_name(), new DataClassCountParameters($condition));
    }
}
?>