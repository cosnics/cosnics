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
    public function initializeColumns()
    {
        $this->addColumn(
            new DataClassPropertyTableColumn(ExternalObject::class, ExternalObject::PROPERTY_TYPE));
        $this->addColumn(
            new DataClassPropertyTableColumn(ExternalObject::class, ExternalObject::PROPERTY_TITLE));
        $this->addColumn(
            new DataClassPropertyTableColumn(ExternalObject::class, ExternalObject::PROPERTY_DESCRIPTION));
        $this->addColumn(
            new DataClassPropertyTableColumn(ExternalObject::class, ExternalObject::PROPERTY_CREATED));
    }
}
