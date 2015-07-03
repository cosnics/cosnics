<?php
namespace Chamilo\Application\CasUser\Service\Table\Service;

use Chamilo\Application\CasUser\Service\Storage\DataClass\Service;
use Chamilo\Application\CasUser\Service\Storage\DataManager;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableDataProvider;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;

class ServiceTableDataProvider extends DataClassTableDataProvider
{

    public function retrieve_data($condition, $offset, $count, $order_property = null)
    {
        $parameters = new DataClassRetrievesParameters($condition, $count, $offset, $order_property);
        return DataManager :: retrieves(Service :: class_name(), $parameters);
    }

    public function count_data($condition)
    {
        return DataManager :: count(Service :: class_name(), $condition);
    }
}
