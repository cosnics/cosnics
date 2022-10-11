<?php
namespace Chamilo\Core\Repository\Workspace\Table\SharedIn;

use Chamilo\Core\Repository\Workspace\Table\Share\ShareTable;
use Chamilo\Libraries\Format\Table\FormAction\TableFormActions;
use Chamilo\Libraries\Format\Table\Interfaces\TableFormActionsSupport;

class SharedInTable extends ShareTable implements TableFormActionsSupport
{

    public function get_implemented_form_actions(): TableFormActions
    {
        return new TableFormActions(__NAMESPACE__, self::TABLE_IDENTIFIER);
    }
}
