<?php
namespace Chamilo\Core\Group\Table\Group;

use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Core\User\Manager;
use Chamilo\Libraries\Format\Table\Column\DataClassPropertyTableColumn;
use Chamilo\Libraries\Format\Table\Column\StaticTableColumn;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableColumnModel;
use Chamilo\Libraries\Format\Table\Interfaces\TableColumnModelActionsColumnSupport;
use Chamilo\Libraries\Translation\Translation;

/**
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

    public function initializeColumns()
    {
        $this->addColumn(new DataClassPropertyTableColumn(Group::class, Group::PROPERTY_NAME));
        $this->addColumn(new DataClassPropertyTableColumn(Group::class, Group::PROPERTY_CODE));
        $this->addColumn(new DataClassPropertyTableColumn(Group::class, Group::PROPERTY_DESCRIPTION));
        $this->addColumn(
            new StaticTableColumn(Translation::get(self::USERS, null, Manager::context())));
        $this->addColumn(new StaticTableColumn(Translation::get(self::SUBGROUPS)));
    }
}
