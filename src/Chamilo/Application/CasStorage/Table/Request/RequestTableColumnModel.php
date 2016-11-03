<?php
namespace Chamilo\Application\CasStorage\Table\Request;

use Chamilo\Application\CasStorage\Storage\DataClass\AccountRequest;
use Chamilo\Libraries\Format\Table\Column\DataClassPropertyTableColumn;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableColumnModel;
use Chamilo\Libraries\Format\Table\Interfaces\TableColumnModelActionsColumnSupport;

class RequestTableColumnModel extends DataClassTableColumnModel implements TableColumnModelActionsColumnSupport
{

    /**
     * Initializes the columns for the table
     */
    public function initialize_columns()
    {
        $this->add_column(
            new DataClassPropertyTableColumn(AccountRequest :: class_name(), AccountRequest :: PROPERTY_FIRST_NAME));
        $this->add_column(
            new DataClassPropertyTableColumn(AccountRequest :: class_name(), AccountRequest :: PROPERTY_LAST_NAME));
        $this->add_column(
            new DataClassPropertyTableColumn(AccountRequest :: class_name(), AccountRequest :: PROPERTY_EMAIL));
        $this->add_column(
            new DataClassPropertyTableColumn(
                AccountRequest :: class_name(), 
                AccountRequest :: PROPERTY_REQUESTER_ID, 
                false));
        $this->add_column(
            new DataClassPropertyTableColumn(AccountRequest :: class_name(), AccountRequest :: PROPERTY_REQUEST_DATE));
        $this->add_column(
            new DataClassPropertyTableColumn(AccountRequest :: class_name(), AccountRequest :: PROPERTY_STATUS));
    }
}
