<?php
namespace Chamilo\Application\Survey\Mail\Table\MailTable;

use Chamilo\Application\Survey\Mail\Storage\DataClass\Mail;
use Chamilo\Libraries\Format\Table\Column\DataClassPropertyTableColumn;
use Chamilo\Libraries\Format\Table\Column\StaticTableColumn;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableColumnModel;
use Chamilo\Libraries\Format\Table\Interfaces\TableColumnModelActionsColumnSupport;
use Chamilo\Libraries\Platform\Translation;

class MailTableColumnModel extends DataClassTableColumnModel implements TableColumnModelActionsColumnSupport
{

    public function initialize_columns()
    {
        $this->add_column(new DataClassPropertyTableColumn(Mail :: class_name(), Mail :: PROPERTY_MAIL_HEADER));
        $this->add_column(new DataClassPropertyTableColumn(Mail :: class_name(), Mail :: PROPERTY_FROM_ADDRESS_NAME));
        $this->add_column(new DataClassPropertyTableColumn(Mail :: class_name(), Mail :: PROPERTY_SEND_DATE));
        $this->add_column(new StaticTableColumn(Translation :: get('SentMails')));
        $this->add_column(new StaticTableColumn(Translation :: get('MailsInQueue')));
        $this->add_column(new StaticTableColumn(Translation :: get('UnsentMails')));
    }
}
?>