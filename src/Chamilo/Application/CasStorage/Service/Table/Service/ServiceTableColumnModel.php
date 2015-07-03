<?php
namespace Chamilo\Application\CasStorage\Service\Table\Service;

use Chamilo\Application\CasStorage\Service\Storage\DataClass\Service;
use Chamilo\Libraries\Format\Table\Column\DataClassPropertyTableColumn;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableColumnModel;
use Chamilo\Libraries\Format\Table\Interfaces\TableColumnModelActionsColumnSupport;

class ServiceTableColumnModel extends DataClassTableColumnModel implements TableColumnModelActionsColumnSupport
{

    /**
     * Initializes the columns for the table
     */
    public function initialize_columns()
    {
        $this->add_column(new DataClassPropertyTableColumn(Service :: class_name(), Service :: PROPERTY_NAME));
        $this->add_column(new DataClassPropertyTableColumn(Service :: class_name(), Service :: PROPERTY_DESCRIPTION));
        $this->add_column(new DataClassPropertyTableColumn(Service :: class_name(), Service :: PROPERTY_SERVICE_ID));
        $this->add_column(new DataClassPropertyTableColumn(Service :: class_name(), Service :: PROPERTY_THEME));
        $this->add_column(new DataClassPropertyTableColumn(Service :: class_name(), Service :: PROPERTY_ENABLED));
    }
}
