<?php
namespace Chamilo\Core\User\Table\Approval;

use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Format\Table\Column\DataClassPropertyTableColumn;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableColumnModel;
use Chamilo\Libraries\Format\Table\Interfaces\TableColumnModelActionsColumnSupport;

/**
 * Table column model for the user browser table
 */
class UserApprovalTableColumnModel extends DataClassTableColumnModel implements TableColumnModelActionsColumnSupport
{

    public function initialize_columns()
    {
        $this->add_column(new DataClassPropertyTableColumn(User::class, User::PROPERTY_OFFICIAL_CODE));
        $this->add_column(new DataClassPropertyTableColumn(User::class, User::PROPERTY_USERNAME));
        $this->add_column(new DataClassPropertyTableColumn(User::class, User::PROPERTY_EMAIL));
        $this->add_column(new DataClassPropertyTableColumn(User::class, User::PROPERTY_LASTNAME));
        $this->add_column(new DataClassPropertyTableColumn(User::class, User::PROPERTY_FIRSTNAME));
    }
}
