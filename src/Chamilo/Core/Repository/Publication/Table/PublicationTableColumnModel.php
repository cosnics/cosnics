<?php
namespace Chamilo\Core\Repository\Publication\Table;

use Chamilo\Core\Repository\Publication\Storage\DataClass\Attributes;
use Chamilo\Libraries\Format\Table\Column\DataClassPropertyTableColumn;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableColumnModel;
use Chamilo\Libraries\Format\Table\Interfaces\TableColumnModelActionsColumnSupport;

class PublicationTableColumnModel extends DataClassTableColumnModel implements TableColumnModelActionsColumnSupport
{

    public function initialize_columns()
    {
        $this->add_column(new DataClassPropertyTableColumn(Attributes :: class_name(), Attributes :: PROPERTY_TITLE));
        $this->add_column(
            new DataClassPropertyTableColumn(Attributes :: class_name(), Attributes :: PROPERTY_APPLICATION));
        $this->add_column(new DataClassPropertyTableColumn(Attributes :: class_name(), Attributes :: PROPERTY_LOCATION));
        $this->add_column(new DataClassPropertyTableColumn(Attributes :: class_name(), Attributes :: PROPERTY_DATE));
    }
}
