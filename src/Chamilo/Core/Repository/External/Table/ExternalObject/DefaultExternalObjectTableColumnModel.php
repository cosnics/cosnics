<?php
namespace Chamilo\Core\Repository\External\Table\ExternalObject;

use Chamilo\Core\Repository\External\ExternalObject;
use Chamilo\Libraries\Format\Table\Column\DataClassPropertyTableColumn;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableColumnModel;
use Chamilo\Libraries\Format\Table\Interfaces\TableColumnModelActionsColumnSupport;

class DefaultExternalObjectTableColumnModel extends DataClassTableColumnModel implements 
    TableColumnModelActionsColumnSupport
{

    /**
     * Initializes the columns for the table
     */
    public function initialize_columns()
    {
        $this->add_column(
            new DataClassPropertyTableColumn(ExternalObject::class_name(), ExternalObject::PROPERTY_TYPE));
        $this->add_column(
            new DataClassPropertyTableColumn(ExternalObject::class_name(), ExternalObject::PROPERTY_TITLE));
        $this->add_column(
            new DataClassPropertyTableColumn(ExternalObject::class_name(), ExternalObject::PROPERTY_DESCRIPTION));
        $this->add_column(
            new DataClassPropertyTableColumn(ExternalObject::class_name(), ExternalObject::PROPERTY_CREATED));
    }
}
