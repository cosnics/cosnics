<?php
namespace Chamilo\Application\CasUser\Rights\Table\Entity;

use Chamilo\Application\CasUser\Rights\Storage\DataClass\LocationEntityRightGroup;
use Chamilo\Application\CasUser\Rights\Storage\DataManager;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableDataProvider;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;

class EntityTableDataProvider extends DataClassTableDataProvider
{

    public function retrieve_data($condition, $offset, $count, $order_property = null)
    {
        $parameters = new DataClassRetrievesParameters($condition, $count, $offset, $order_property);
        return DataManager :: retrieves(LocationEntityRightGroup :: class_name(), $parameters);
    }

    public function count_data($condition)
    {
        $parameters = new DataClassCountParameters($condition);
        return DataManager :: count(LocationEntityRightGroup :: class_name(), $parameters);
    }
}
