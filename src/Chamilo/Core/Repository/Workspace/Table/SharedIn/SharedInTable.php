<?php
namespace Chamilo\Core\Repository\Workspace\Table\SharedIn;

use Chamilo\Core\Repository\Workspace\Table\Share\ShareTable;
use Chamilo\Libraries\Format\Table\FormAction\TableFormActions;
use Chamilo\Libraries\Format\Table\Interfaces\TableActionsSupport;

class SharedInTable extends ShareTable implements TableActionsSupport
{

    public function getTableActions(): TableFormActions
    {
        return new TableFormActions(__NAMESPACE__, self::TABLE_IDENTIFIER);
    }
}
