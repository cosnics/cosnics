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
    public function initializeColumns()
    {
        $this->addColumn(
            new DataClassPropertyTableColumn(Instance::class, Instance::PROPERTY_IMPLEMENTATION));
        $this->addColumn(new DataClassPropertyTableColumn(Instance::class, Instance::PROPERTY_TITLE));
    }
}