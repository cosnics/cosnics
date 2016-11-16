<?php
namespace Chamilo\Application\Survey\Table\Group;

use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Libraries\Format\Table\Column\DataClassPropertyTableColumn;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableColumnModel;
use Chamilo\Libraries\Format\Table\Interfaces\TableColumnModelActionsColumnSupport;

class GroupTableColumnModel extends DataClassTableColumnModel implements TableColumnModelActionsColumnSupport
{

    public function initialize_columns()
    {
        $this->add_column(new DataClassPropertyTableColumn(Group::class_name(), Group::PROPERTY_NAME));
        $this->add_column(new DataClassPropertyTableColumn(Group::class_name(), Group::PROPERTY_DESCRIPTION));
    }
}
?>
