<?php
namespace Chamilo\Core\Repository\Workspace\Table\Workspace\Shared;

use Chamilo\Core\Repository\Workspace\Table\Workspace\WorkspaceTable;
use Chamilo\Libraries\Format\Table\FormAction\TableActions;

/**
 *
 * @package Chamilo\Core\Repository\Workspace\Table\Workspace\Shared
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class SharedWorkspaceTable extends WorkspaceTable
{

    /**
     * @see \Chamilo\Core\Repository\Workspace\Table\Workspace\WorkspaceTable::getTableActions()
     */
    public function getTableActions(): TableActions
    {
        return new TableActions(__NAMESPACE__, self::TABLE_IDENTIFIER);
    }
}
