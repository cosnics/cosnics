<?php
namespace Chamilo\Core\Repository\Workspace\Table\Share;

use Chamilo\Core\Repository\Workspace\Storage\DataClass\Workspace;
use Chamilo\Libraries\Format\Table\Column\DataClassPropertyTableColumn;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableColumnModel;
use Chamilo\Libraries\Format\Table\Interfaces\TableColumnModelActionsColumnSupport;

class ShareTableColumnModel extends DataClassTableColumnModel implements TableColumnModelActionsColumnSupport
{

    public function initializeColumns()
    {
        $this->addColumn(new DataClassPropertyTableColumn(Workspace::class, Workspace::PROPERTY_NAME));
        $this->addColumn(
            new DataClassPropertyTableColumn(Workspace::class, Workspace::PROPERTY_CREATOR_ID, null, false));
        $this->addColumn(
            new DataClassPropertyTableColumn(Workspace::class, Workspace::PROPERTY_CREATION_DATE));
    }
}
