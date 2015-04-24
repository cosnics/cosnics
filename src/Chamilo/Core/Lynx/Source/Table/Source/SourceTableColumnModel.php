<?php
namespace Chamilo\Core\Lynx\Source\Table\Source;

use Chamilo\Core\Lynx\Source\DataClass\Source;
use Chamilo\Libraries\Format\Table\Column\DataClassPropertyTableColumn;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableColumnModel;
use Chamilo\Libraries\Format\Table\Interfaces\TableColumnModelActionsColumnSupport;

class SourceTableColumnModel extends DataClassTableColumnModel implements TableColumnModelActionsColumnSupport
{

    /**
     * Initializes the columns for the table
     */
    public function initialize_columns()
    {
        $this->add_column(new DataClassPropertyTableColumn(Source :: class_name(), Source :: PROPERTY_NAME));
        $this->add_column(new DataClassPropertyTableColumn(Source :: class_name(), Source :: PROPERTY_URI));
        $this->add_column(new DataClassPropertyTableColumn(Source :: class_name(), Source :: PROPERTY_DESCRIPTION));
    }
}
