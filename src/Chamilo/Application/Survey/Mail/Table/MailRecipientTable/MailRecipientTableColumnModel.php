<?php
namespace Chamilo\Application\Survey\Mail\Table\MailRecipientTable;

use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Format\Table\Column\StaticTableColumn;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableColumnModel;
use Chamilo\Libraries\Format\Table\Interfaces\TableColumnModelActionsColumnSupport;

class MailRecipientTableColumnModel extends DataClassTableColumnModel implements TableColumnModelActionsColumnSupport
{

    public function initialize_columns()
    {
        $this->add_column(new StaticTableColumn(User :: PROPERTY_USERNAME));
        $this->add_column(new StaticTableColumn(User :: PROPERTY_EMAIL));
        $this->add_column(new StaticTableColumn(User :: PROPERTY_FIRSTNAME));
        $this->add_column(new StaticTableColumn(User :: PROPERTY_LASTNAME));
    }
}
?>