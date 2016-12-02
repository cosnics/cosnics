<?php
namespace Chamilo\Application\Survey\Mail\Table\MailRecipientTable;

use Chamilo\Application\Survey\Mail\Storage\DataClass\UserMail;
use Chamilo\Application\Survey\Mail\Storage\DataManager;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableDataProvider;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;

class MailRecipientTableDataProvider extends DataClassTableDataProvider
{

    function retrieve_data($condition, $offset, $count, $order_property = null)
    {
        $parameters = new DataClassRetrievesParameters($condition, $count, $offset, $order_property);
        return DataManager::retrieves(UserMail::class_name(), $parameters);
    }

    function count_data($condition)
    {
        $parameters = new DataClassCountParameters($condition);
        return DataManager::count(UserMail::class_name(), $parameters);
    }
}
?>