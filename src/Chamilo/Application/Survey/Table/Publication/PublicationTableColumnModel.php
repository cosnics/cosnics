<?php
namespace Chamilo\Application\Survey\Table\Publication;

use Chamilo\Application\Survey\Storage\DataClass\Publication;
use Chamilo\Libraries\Format\Table\Column\DataClassPropertyTableColumn;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableColumnModel;
use Chamilo\Libraries\Format\Table\Interfaces\TableColumnModelActionsColumnSupport;

class PublicationTableColumnModel extends DataClassTableColumnModel implements TableColumnModelActionsColumnSupport
{

    public function initialize_columns()
    {
        $this->add_column(new DataClassPropertyTableColumn(Publication :: class_name(), Publication :: PROPERTY_TITLE));
        $this->add_column(
            new DataClassPropertyTableColumn(Publication :: class_name(), Publication :: PROPERTY_FROM_DATE));
        $this->add_column(
            new DataClassPropertyTableColumn(Publication :: class_name(), Publication :: PROPERTY_TO_DATE));
    }
}
?>