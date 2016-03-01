<?php
namespace Chamilo\Core\Repository\Table\ExternalLink;

use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableDataProvider;
use Chamilo\Libraries\Storage\ResultSet\ArrayResultSet;

class ExternalLinkTableDataProvider extends DataClassTableDataProvider
{

    public function retrieve_data($condition, $offset, $count, $order_property = null)
    {
        return new ArrayResultSet(array($this->get_component()->get_object()->get_synchronization_data()));
    }

    public function count_data($condition)
    {
        return 1;
    }
}
