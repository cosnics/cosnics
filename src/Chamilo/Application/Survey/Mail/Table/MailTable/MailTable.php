<?php
namespace Chamilo\Application\Survey\Mail\Table\MailTable;

use Chamilo\Application\Survey\Mail\Manager;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTable;

class MailTable extends DataClassTable
{
    const TABLE_IDENTIFIER = Manager::PARAM_PUBLICATION_MAIL_ID;
}
?>