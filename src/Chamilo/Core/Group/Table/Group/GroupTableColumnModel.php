<?php
namespace Chamilo\Core\Group\Table\Group;

use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Libraries\Format\Table\Column\DataClassPropertyTableColumn;
use Chamilo\Libraries\Format\Table\Column\StaticTableColumn;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableColumnModel;
use Chamilo\Libraries\Format\Table\Interfaces\TableColumnModelActionsColumnSupport;
use Chamilo\Libraries\Platform\Translation;

/**
 * $Id: group_browser_table_column_model.class.php 224 2009-11-13 14:40:30Z kariboe $
 * 
 * @package groups.lib.group_manager.component.group_browser
 */
/**
 * Table column model for the user browser table
 */
class GroupTableColumnModel extends DataClassTableColumnModel implements TableColumnModelActionsColumnSupport

{
    const USERS = 'Users';
    const SUBGROUPS = 'Subgroups';

    public function initialize_columns()
    {
        $this->add_column(new DataClassPropertyTableColumn(Group :: class_name(), Group :: PROPERTY_NAME));
        $this->add_column(new DataClassPropertyTableColumn(Group :: class_name(), Group :: PROPERTY_CODE));
        $this->add_column(new DataClassPropertyTableColumn(Group :: class_name(), Group :: PROPERTY_DESCRIPTION));
        $this->add_column(
            new StaticTableColumn(Translation :: get(self :: USERS, null, \Chamilo\Core\User\Manager :: context())));
        $this->add_column(new StaticTableColumn(Translation :: get(self :: SUBGROUPS)));
    }
}
