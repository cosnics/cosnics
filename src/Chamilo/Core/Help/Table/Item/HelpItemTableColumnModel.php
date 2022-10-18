<?php
namespace Chamilo\Core\Help\Table\Item;

use Chamilo\Core\Help\Storage\DataClass\HelpItem;
use Chamilo\Libraries\Format\Table\Column\DataClassPropertyTableColumn;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableColumnModel;
use Chamilo\Libraries\Format\Table\Interfaces\TableColumnModelActionsColumnSupport;

class HelpItemTableColumnModel extends DataClassTableColumnModel implements TableColumnModelActionsColumnSupport
{

    public function initializeColumns()
    {
        $this->addColumn(new DataClassPropertyTableColumn(HelpItem::class, HelpItem::PROPERTY_CONTEXT));
        $this->addColumn(new DataClassPropertyTableColumn(HelpItem::class, HelpItem::PROPERTY_IDENTIFIER));
        $this->addColumn(new DataClassPropertyTableColumn(HelpItem::class, HelpItem::PROPERTY_LANGUAGE));
        $this->addColumn(new DataClassPropertyTableColumn(HelpItem::class, HelpItem::PROPERTY_URL));
    }
}
