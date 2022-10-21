<?php
namespace Chamilo\Core\Repository\Workspace\Table\SharedIn;

use Chamilo\Core\Repository\Workspace\Table\Share\ShareTable;
use Chamilo\Libraries\Format\Table\FormAction\TableActions;
use Chamilo\Libraries\Format\Table\Interfaces\TableActionsSupport;

class SharedInTable extends ShareTable implements TableActionsSupport
{

    public function getTableActions(): TableActions
    {
        return new TableActions(__NAMESPACE__, self::TABLE_IDENTIFIER);
    }
}
