<?php
namespace Chamilo\Core\Repository\ContentObject\Portfolio\Display\Table\Item;

use ArrayIterator;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableDataProvider;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Portfolio item table data provider
 *
 * @package repository\content_object\portfolio\display
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ItemTableDataProvider extends DataClassTableDataProvider
{

    public function countData(?Condition $condition = null): int
    {
        $node = $this->get_component()->get_current_node();

        return count($node->get_children());
    }

    public function retrieveData(
        ?Condition $condition = null, ?int $offset = null, ?int $count = null, ?OrderBy $orderBy = null
    ): ArrayCollection
    {
        $node = $this->get_component()->get_current_node();

        return new ArrayIterator($node->get_children());
    }
}