<?php
namespace Chamilo\Core\Group\Table\SubscribeUser;

use Chamilo\Configuration\Configuration;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Format\Table\Column\DataClassPropertyTableColumn;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableColumnModel;
use Chamilo\Libraries\Format\Table\Interfaces\TableColumnModelActionsColumnSupport;

/**
 *
 * @package groups.lib.group_manager.component.subscribe_user_browser
 */
/**
 * Table column model for the user browser table
 */
class SubscribeUserTableColumnModel extends DataClassTableColumnModel implements TableColumnModelActionsColumnSupport
{
    const DEFAULT_ORDER_COLUMN_INDEX = 1;

    public function initializeColumns()
    {
        $this->addColumn(new DataClassPropertyTableColumn(User::class, User::PROPERTY_LASTNAME));
        $this->addColumn(new DataClassPropertyTableColumn(User::class, User::PROPERTY_FIRSTNAME));
        $this->addColumn(new DataClassPropertyTableColumn(User::class, User::PROPERTY_USERNAME));

        $showEmail = Configuration::getInstance()->get_setting(array('Chamilo\Core\User', 'show_email_addresses'));

        if($showEmail)
        {
            $this->addColumn(new DataClassPropertyTableColumn(User::class, User::PROPERTY_EMAIL));
        }

        $this->addColumn(new DataClassPropertyTableColumn(User::class, User::PROPERTY_STATUS));
        $this->addColumn(new DataClassPropertyTableColumn(User::class, User::PROPERTY_PLATFORMADMIN));
    }
}
