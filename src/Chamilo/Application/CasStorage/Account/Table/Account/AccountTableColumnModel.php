<?php
namespace Chamilo\Application\CasStorage\Account\Table\Account;

use Chamilo\Application\CasStorage\Account\Storage\DataClass\Account;
use Chamilo\Libraries\Format\Table\Column\DataClassPropertyTableColumn;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableColumnModel;
use Chamilo\Libraries\Format\Table\Interfaces\TableColumnModelActionsColumnSupport;

class AccountTableColumnModel extends DataClassTableColumnModel implements TableColumnModelActionsColumnSupport
{

    /**
     * Initializes the columns for the table
     */
    public function initialize_columns()
    {
        $this->add_column(new DataClassPropertyTableColumn(Account :: class_name(), Account :: PROPERTY_FIRST_NAME));
        $this->add_column(new DataClassPropertyTableColumn(Account :: class_name(), Account :: PROPERTY_LAST_NAME));
        $this->add_column(new DataClassPropertyTableColumn(Account :: class_name(), Account :: PROPERTY_EMAIL));
        $this->add_column(new DataClassPropertyTableColumn(Account :: class_name(), Account :: PROPERTY_AFFILIATION));
        $this->add_column(new DataClassPropertyTableColumn(Account :: class_name(), Account :: PROPERTY_GROUP));
        $this->add_column(new DataClassPropertyTableColumn(Account :: class_name(), Account :: PROPERTY_STATUS));
    }
}
