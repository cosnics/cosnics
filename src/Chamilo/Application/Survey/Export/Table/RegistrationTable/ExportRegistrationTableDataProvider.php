<?php
namespace Chamilo\Application\Survey\Export\Table\RegistrationTable;

use Chamilo\Application\Survey\Export\Storage\DataClass\ExportRegistration;
use Chamilo\Application\Survey\Export\Storage\DataManager;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableDataProvider;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;

class ExportRegistrationTableDataProvider extends DataClassTableDataProvider
{

    function retrieve_data($condition, $offset, $count, $order_property = null)
    {
        $parameters = new DataClassRetrievesParameters($condition, $count, $offset, $order_property);
        return DataManager::retrieves(ExportRegistration::class_name(), $parameters);
    }

    function count_data($condition)
    {
        $parameters = new DataClassCountParameters($condition);
        return DataManager::count(ExportRegistration::class_name(), $parameters);
    }
}
?>