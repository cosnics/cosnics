<?php
namespace Chamilo\Core\User\Table\Admin;

use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Format\Table\Column\DataClassPropertyTableColumn;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableColumnModel;
use Chamilo\Libraries\Format\Table\Interfaces\TableColumnModelActionsColumnSupport;

/**
 * Table column model for the user browser table
 */
class AdminUserTableColumnModel extends DataClassTableColumnModel implements TableColumnModelActionsColumnSupport
{

    /**
     * Constructor
     */
    public function initializeColumns()
    {
        $this->addColumn(new DataClassPropertyTableColumn(User::class, User::PROPERTY_OFFICIAL_CODE));
        $this->addColumn(new DataClassPropertyTableColumn(User::class, User::PROPERTY_LASTNAME));
        $this->addColumn(new DataClassPropertyTableColumn(User::class, User::PROPERTY_FIRSTNAME));
        $this->addColumn(new DataClassPropertyTableColumn(User::class, User::PROPERTY_USERNAME));
        $this->addColumn(new DataClassPropertyTableColumn(User::class, User::PROPERTY_EMAIL));
        $this->addColumn(new DataClassPropertyTableColumn(User::class, User::PROPERTY_STATUS));
        $this->addColumn(new DataClassPropertyTableColumn(User::class, User::PROPERTY_PLATFORMADMIN));
        $this->addColumn(new DataClassPropertyTableColumn(User::class, User::PROPERTY_ACTIVE));
    }
}
