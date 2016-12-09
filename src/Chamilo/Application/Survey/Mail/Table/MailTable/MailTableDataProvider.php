<?php
namespace Chamilo\Application\Survey\Mail\Table\MailTable;

use Chamilo\Application\Survey\Mail\Storage\DataClass\Mail;
use Chamilo\Application\Survey\Mail\Storage\DataManager;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableDataProvider;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;

class MailTableDataProvider extends DataClassTableDataProvider
{

    function retrieve_data($condition, $offset, $count, $order_property = null)
    {
        $parameters = new DataClassRetrievesParameters($condition, $count, $offset, $order_property);
        return DataManager::retrieves(Mail::class_name(), $parameters);
    }

    function count_data($condition)
    {
        $parameters = new DataClassCountParameters($condition);
        return DataManager::count(Mail::class_name(), $parameters);
    }
}
?>