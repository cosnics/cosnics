<?php
namespace Chamilo\Application\CasUser\Account\Table\Account;

use Chamilo\Application\CasUser\Account\Storage\DataClass\Account;
use Chamilo\Application\CasUser\Account\Storage\DataManager;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableDataProvider;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;

class AccountTableDataProvider extends DataClassTableDataProvider
{

    public function retrieve_data($condition, $offset, $count, $order_property = null)
    {
        $parameters = new DataClassRetrievesParameters($condition, $count, $offset, $order_property);
        return DataManager :: retrieves(Account :: class_name(), $parameters);
    }

    public function count_data($condition)
    {
        return DataManager :: count(Account :: class_name(), $condition);
    }
}
