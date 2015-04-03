<?php
namespace Chamilo\Core\Repository\ContentObject\Survey\Page\Builder\Component\QuestionBrowser;

use Chamilo\Core\Repository\Storage\DataClass\ComplexContentObjectItem;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableDataProvider;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Core\Repository\ContentObject\Survey\Page\Storage\DataManager;

class QuestionTableDataProvider extends DataClassTableDataProvider
{

    function retrieve_data($condition, $offset, $count, $order_property = null)
    {
        return DataManager :: retrieves(
            ComplexContentObjectItem :: class_name(), 
            new DataClassRetrievesParameters($condition, $count, $offset, $order_property));
    }

    function count_data($condition)
    {
        return DataManager :: count(
            ComplexContentObjectItem :: class_name(), 
            new DataClassCountParameters($condition));
    }
}
?>