<?php
namespace Chamilo\Core\Repository\Instance\Table\Instance;

use Chamilo\Core\Repository\Instance\Storage\DataClass\Instance;
use Chamilo\Libraries\Format\Table\Column\DataClassPropertyTableColumn;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableColumnModel;
use Chamilo\Libraries\Format\Table\Interfaces\TableColumnModelActionsColumnSupport;

/**
 * Table column model for the schema
 * 
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class InstanceTableColumnModel extends DataClassTableColumnModel implements TableColumnModelActionsColumnSupport
{

    /**
     * Initializes the columns for the table
     */
    public function initialize_columns()
    {
        $this->add_column(
            new DataClassPropertyTableColumn(Instance :: class_name(), Instance :: PROPERTY_IMPLEMENTATION));
        $this->add_column(new DataClassPropertyTableColumn(Instance :: class_name(), Instance :: PROPERTY_TITLE));
    }
}