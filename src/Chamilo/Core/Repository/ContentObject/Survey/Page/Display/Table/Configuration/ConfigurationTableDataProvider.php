<?php
namespace Chamilo\Core\Repository\ContentObject\Survey\Page\Display\Table\Configuration;

use Chamilo\Core\Repository\ContentObject\Survey\Page\Storage\DataClass\Configuration;
use Chamilo\Core\Repository\ContentObject\Survey\Page\Storage\DataManager;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableDataProvider;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;

class ConfigurationTableDataProvider extends DataClassTableDataProvider
{

    function retrieve_data($condition, $offset, $count, $order_property = null)
    {
        $order = array(
            new OrderBy(
                new PropertyConditionVariable(
                    Configuration :: class_name(),
                    Configuration :: PROPERTY_DISPLAY_ORDER,
                    SORT_ASC)));
        $parameters = new DataClassRetrievesParameters($condition, $count, $offset, $order);
        return DataManager :: retrieves(Configuration :: class_name(), new DataClassRetrievesParameters($condition));
    }

    function count_data($condition)
    {
        $parameters = new DataClassCountParameters($condition);
        return DataManager :: count(Configuration :: class_name(), $parameters);
    }
}
?>