<?php
namespace Chamilo\Core\Repository\Table\ExternalLink;

use ArrayIterator;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableDataProvider;

class ExternalLinkTableDataProvider extends DataClassTableDataProvider
{

    public function count_data($condition)
    {
        return 1;
    }

    public function retrieve_data($condition, $offset, $count, $order_property = null)
    {
        return new ArrayIterator(array($this->get_component()->getContentObject()->get_synchronization_data()));
    }
}
