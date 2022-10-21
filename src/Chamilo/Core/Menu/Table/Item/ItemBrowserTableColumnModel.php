<?php
namespace Chamilo\Core\Menu\Table\Item;

use Chamilo\Core\Menu\Storage\DataClass\Item;
use Chamilo\Core\Menu\Storage\DataClass\ItemTitle;
use Chamilo\Libraries\Format\Table\Column\DataClassPropertyTableColumn;
use Chamilo\Libraries\Format\Table\Column\StaticTableColumn;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableColumnModel;
use Chamilo\Libraries\Format\Table\Interfaces\TableColumnModelActionsColumnSupport;

/**
 *
 * @package Chamilo\Core\Menu\Table\Item
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class ItemBrowserTableColumnModel extends DataClassTableColumnModel implements TableColumnModelActionsColumnSupport
{
    //const DEFAULT_ORDER_COLUMN_INDEX = 0;
    public const PROPERTY_TYPE = 'Type';

    public function initializeColumns()
    {
        $this->addColumn(new StaticTableColumn(self::PROPERTY_TYPE));

        $this->addColumn(
            new DataClassPropertyTableColumn(Item::class, Item::PROPERTY_SORT, false)
        );

        $this->addColumn(
            new DataClassPropertyTableColumn(ItemTitle::class, ItemTitle::PROPERTY_TITLE, false)
        );
    }
}
