<?php
namespace Chamilo\Core\Menu\Table\Item;

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
    const TYPE = 'Type';

    public function initialize_columns()
    {
        $this->add_column(new StaticTableColumn(self::TYPE));
        $this->add_column(
            new DataClassPropertyTableColumn(ItemTitle::class_name(), ItemTitle::PROPERTY_TITLE, false));
    }
}
