<?php
namespace Chamilo\Core\Group\Table\GroupRelUser;

use Chamilo\Core\Group\Storage\DataClass\GroupRelUser;
use Chamilo\Libraries\Format\Table\Column\DataClassPropertyTableColumn;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableColumnModel;
use Chamilo\Libraries\Format\Table\Interfaces\TableColumnModelActionsColumnSupport;

class GroupRelUserTableColumnModel extends DataClassTableColumnModel implements TableColumnModelActionsColumnSupport
{

    public function initializeColumns()
    {
        $this->addColumn(
            new DataClassPropertyTableColumn(GroupRelUser::class, GroupRelUser::PROPERTY_USER_ID));
    }
}
