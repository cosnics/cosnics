<?php
namespace Chamilo\Core\Group\Table\SubscribeUser;

use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Format\Table\Column\DataClassPropertyTableColumn;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableColumnModel;
use Chamilo\Libraries\Format\Table\Interfaces\TableColumnModelActionsColumnSupport;

/**
 * $Id: subscribe_user_browser_table_column_model.class.php 224 2009-11-13 14:40:30Z kariboe $
 * 
 * @package groups.lib.group_manager.component.subscribe_user_browser
 */
/**
 * Table column model for the user browser table
 */
class SubscribeUserTableColumnModel extends DataClassTableColumnModel implements TableColumnModelActionsColumnSupport
{
    const DEFAULT_ORDER_COLUMN_INDEX = 1;

    public function initialize_columns()
    {
        $this->add_column(new DataClassPropertyTableColumn(User :: class_name(), User :: PROPERTY_LASTNAME));
        $this->add_column(new DataClassPropertyTableColumn(User :: class_name(), User :: PROPERTY_FIRSTNAME));
        $this->add_column(new DataClassPropertyTableColumn(User :: class_name(), User :: PROPERTY_USERNAME));
        $this->add_column(new DataClassPropertyTableColumn(User :: class_name(), User :: PROPERTY_EMAIL));
        $this->add_column(new DataClassPropertyTableColumn(User :: class_name(), User :: PROPERTY_STATUS));
        $this->add_column(new DataClassPropertyTableColumn(User :: class_name(), User :: PROPERTY_PLATFORMADMIN));
    }
}
