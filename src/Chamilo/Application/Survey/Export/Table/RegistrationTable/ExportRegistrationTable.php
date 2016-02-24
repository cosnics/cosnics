<?php
namespace Chamilo\Application\Survey\Export\Table\RegistrationTable;

use Chamilo\Application\Survey\Export\Manager;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTable;

class ExportRegistrationTable extends DataClassTable
{
    const TABLE_IDENTIFIER = Manager :: PARAM_EXPORT_REGISTRATION_ID;
}
?>