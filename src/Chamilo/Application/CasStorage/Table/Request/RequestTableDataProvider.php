<?php
namespace Chamilo\Application\CasStorage\Table\Request;

use Chamilo\Application\CasStorage\Storage\DataClass\AccountRequest;
use Chamilo\Application\CasStorage\Storage\DataManager;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableDataProvider;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;

class RequestTableDataProvider extends DataClassTableDataProvider
{

    public function retrieve_data($condition, $offset, $count, $order_property = null)
    {
        $parameters = new DataClassRetrievesParameters($condition, $count, $offset, $order_property);
        return DataManager::retrieves(AccountRequest::class_name(), $parameters);
    }

    public function count_data($condition)
    {
        return DataManager::count(AccountRequest::class_name(), $condition);
    }
}
