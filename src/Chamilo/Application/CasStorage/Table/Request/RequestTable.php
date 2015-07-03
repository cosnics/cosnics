<?php
namespace Chamilo\Application\CasUser\Table\Request;

use Chamilo\Application\CasUser\Manager;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTable;
use Chamilo\Libraries\Format\Table\FormAction\TableFormActions;
use Chamilo\Libraries\Format\Table\Interfaces\TableFormActionsSupport;

class RequestTable extends DataClassTable implements TableFormActionsSupport
{
    const TABLE_IDENTIFIER = Manager :: PARAM_REQUEST_ID;

    public function get_implemented_form_actions()
    {
        $actions = new TableFormActions(__NAMESPACE__);
        return $actions;
    }
}
