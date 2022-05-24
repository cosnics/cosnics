<?php
namespace Chamilo\Application\Weblcms\Request\Rights\Table\Entity;

use Chamilo\Application\Weblcms\Request\Rights\Storage\DataClass\RightsLocationEntityRightGroup;
use Chamilo\Application\Weblcms\Request\Rights\Storage\DataManager;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableDataProvider;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;

class EntityTableDataProvider extends DataClassTableDataProvider
{

    function retrieve_data($condition, $offset, $count, $order_property = null)
    {
        $parameters = new DataClassRetrievesParameters($condition, $count, $offset, $order_property);
        return DataManager::retrieves(RightsLocationEntityRightGroup::class, $parameters);
    }

    function count_data($condition)
    {
        $parameters = new DataClassCountParameters($condition);
        return DataManager::count(RightsLocationEntityRightGroup::class, $parameters);
    }
}