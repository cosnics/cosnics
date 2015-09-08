<?php
namespace Chamilo\Application\Survey\Mail\Table\MailRecipientTable;

use Chamilo\Application\Survey\Mail\Manager;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTable;

class MailRecipientTable extends DataClassTable
{
    const TABLE_IDENTIFIER = Manager :: PARAM_PUBLICATION_MAIL_ID;
}
?>