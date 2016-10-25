<?php
namespace Chamilo\Application\Survey\Table\Participant;

use Chamilo\Application\Survey\Storage\DataClass\Participant;
use Chamilo\Application\Survey\Storage\DataManager;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableDataProvider;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;

class ParticipantTableDataProvider extends DataClassTableDataProvider
{

    function retrieve_data($condition, $offset, $count, $order_property = null)
    {
        return DataManager :: retrieves(
            Participant :: class_name(),
            new DataClassRetrievesParameters($condition, $count, $offset, $order_property));
    }

    function count_data($condition)
    {
        return DataManager :: count(Participant :: class_name(), new DataClassCountParameters($condition));
    }
}
?>