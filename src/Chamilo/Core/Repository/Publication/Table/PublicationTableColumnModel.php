<?php
namespace Chamilo\Core\Repository\Publication\Table;

use Chamilo\Core\Repository\Publication\Storage\DataClass\Attributes;
use Chamilo\Libraries\Format\Table\Column\DataClassPropertyTableColumn;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableColumnModel;
use Chamilo\Libraries\Format\Table\Interfaces\TableColumnModelActionsColumnSupport;

class PublicationTableColumnModel extends DataClassTableColumnModel implements TableColumnModelActionsColumnSupport
{

    public function initializeColumns()
    {
        $this->addColumn(new DataClassPropertyTableColumn(Attributes::class, Attributes::PROPERTY_TITLE));
        $this->addColumn(
            new DataClassPropertyTableColumn(Attributes::class, Attributes::PROPERTY_APPLICATION));
        $this->addColumn(new DataClassPropertyTableColumn(Attributes::class, Attributes::PROPERTY_LOCATION));
        $this->addColumn(new DataClassPropertyTableColumn(Attributes::class, Attributes::PROPERTY_DATE));
    }
}
